// Función helper para hacer peticiones AJAX
function ajax(url, method, data, callback) {
    const xhr = new XMLHttpRequest();
    xhr.open(method, url, true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    callback(null, response);
                } catch (e) {
                    callback(e, null);
                }
            } else {
                callback(new Error('Error en la petición: ' + xhr.status), null);
            }
        }
    };
    
    if (method === 'POST') {
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        const params = new URLSearchParams(data).toString();
        xhr.send(params);
    } else {
        xhr.send();
    }
}

// Filtrar productos
function filtrarProductos() {
    const filtros = {
        idTipo: document.getElementById('filtroTipo').value,
        idMarca: document.getElementById('filtroMarca').value,
        sexo: document.getElementById('filtroSexo').value,
        precioMin: document.getElementById('precioMin').value,
        precioMax: document.getElementById('precioMax').value
    };
    
    mostrarLoading(true);
    
    ajax('/producto/filtrar', 'POST', filtros, function(error, productos) {
        mostrarLoading(false);
        
        if (error) {
            console.error('Error al filtrar productos:', error);
            return;
        }
        
        mostrarProductos(productos);
    });
}

// Mostrar productos en el DOM
function mostrarProductos(productos) {
    const container = document.getElementById('productosContainer');
    
    if (productos.length === 0) {
        container.innerHTML = '<p class="no-productos">No se encontraron productos con esos filtros.</p>';
        return;
    }
    
    let html = '';
    productos.forEach(producto => {
        const imagen = producto.imagenPrincipal || 'default.jpg';
        html += `
            <div class="producto-card">
                <a href="/producto/detalle/${producto.id}">
                    <div class="producto-imagen">
                        <img src="/assets/uploads/productos/${imagen}" 
                             alt="${producto.tipo}"
                             onerror="this.src='/assets/uploads/productos/default.jpg'">
                    </div>
                    <div class="producto-info">
                        <h3>${producto.tipo}</h3>
                        <p class="marca">${producto.marca}</p>
                        <p class="color">Color: ${producto.color}</p>
                        <p class="precio">${parseFloat(producto.precio).toFixed(2)} €</p>
                    </div>
                </a>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

// Agregar producto al carrito
function agregarAlCarrito(productoId) {
    ajax('/pedido/agregarCarrito', 'POST', { idProducto: productoId }, function(error, response) {
        if (error) {
            mostrarMensaje('Error al agregar al carrito', 'error');
            return;
        }
        
        if (response.success) {
            mostrarMensaje('Producto agregado al carrito', 'success');
            // Actualizar contador de carrito si existe
            actualizarContadorCarrito(response.totalItems);
        } else {
            mostrarMensaje(response.message || 'Error al agregar al carrito', 'error');
        }
    });
}

// Actualizar contador de items en el carrito
function actualizarContadorCarrito(totalItems) {
    const contador = document.getElementById('carritoContador');
    if (contador && totalItems > 0) {
        contador.textContent = totalItems;
        contador.style.display = 'inline-block';
    }
}

// Mostrar/ocultar loading
function mostrarLoading(mostrar) {
    const loading = document.getElementById('loading');
    if (loading) {
        loading.style.display = mostrar ? 'block' : 'none';
    }
}

// Mostrar mensajes al usuario
function mostrarMensaje(texto, tipo) {
    const mensaje = document.getElementById('mensaje');
    if (mensaje) {
        mensaje.textContent = texto;
        mensaje.className = 'mensaje ' + tipo;
        mensaje.style.display = 'block';
        
        setTimeout(() => {
            mensaje.style.display = 'none';
        }, 3000);
    } else {
        // Si no existe el elemento mensaje, crear uno temporal
        const mensajeTemp = document.createElement('div');
        mensajeTemp.className = 'mensaje ' + tipo;
        mensajeTemp.textContent = texto;
        mensajeTemp.style.position = 'fixed';
        mensajeTemp.style.top = '20px';
        mensajeTemp.style.right = '20px';
        mensajeTemp.style.zIndex = '1000';
        mensajeTemp.style.padding = '1rem';
        mensajeTemp.style.borderRadius = '5px';
        mensajeTemp.style.boxShadow = '0 2px 8px rgba(0, 0, 0, 0.2)';
        
        document.body.appendChild(mensajeTemp);
        
        setTimeout(() => {
            document.body.removeChild(mensajeTemp);
        }, 3000);
    }
}

// Limpiar filtros
function limpiarFiltros() {
    document.getElementById('filtroTipo').value = '';
    document.getElementById('filtroMarca').value = '';
    document.getElementById('filtroSexo').value = '';
    document.getElementById('precioMin').value = '';
    document.getElementById('precioMax').value = '';
    
    filtrarProductos();
}

// Actualizar cantidad en el carrito
function actualizarCantidadCarrito(idProducto, cantidad) {
    ajax('/pedido/actualizarCantidad', 'POST', 
        { idProducto: idProducto, cantidad: cantidad }, 
        function(error, response) {
            if (!error && response.success) {
                // Actualizar subtotal y total en la interfaz
                const row = document.querySelector(`tr[data-producto-id="${idProducto}"]`);
                if (row) {
                    row.querySelector('.subtotal').textContent = response.subtotal + ' €';
                }
                const totalElement = document.querySelector('.total strong');
                if (totalElement) {
                    totalElement.textContent = response.total + ' €';
                }
            } else {
                mostrarMensaje('Error al actualizar la cantidad', 'error');
            }
        }
    );
}

// Eliminar producto del carrito
function eliminarDelCarrito(idProducto) {
    if (!confirm('¿Estás seguro de eliminar este producto del carrito?')) {
        return;
    }
    
    ajax('/pedido/eliminarDelCarrito', 'POST', 
        { idProducto: idProducto }, 
        function(error, response) {
            if (!error && response.success) {
                // Recargar la página para actualizar el carrito
                location.reload();
            } else {
                mostrarMensaje('Error al eliminar el producto', 'error');
            }
        }
    );
}
