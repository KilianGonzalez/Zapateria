// BASE_URL viene definido en footer.php
// Función helper para peticiones AJAX
function ajax(url, method, data, callback) {
    console.log('AJAX request:', method, url, data);
    const xhr = new XMLHttpRequest();
    const fullUrl = url.startsWith('http') ? url : BASE_URL + url;
    xhr.open(method, fullUrl, true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            console.log('AJAX response:', xhr.status, xhr.responseText);
            if (xhr.status === 200) {
                if (callback) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        callback(null, response);
                    } catch (e) {
                        console.error('Error parsing JSON:', e);
                        callback(e, null);
                    }
                }
            } else {
                if (callback) callback(new Error('Error ' + xhr.status), null);
            }
        }
    };
    
    if (method === 'POST') {
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        const params = new URLSearchParams(data || {}).toString();
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

// Mostrar productos en el DOM (para filtros/búsqueda)
function mostrarProductos(productos) {
    const container = document.getElementById('productosContainer');
    
    if (!container) return;
    
    if (!productos || productos.length === 0) {
        container.innerHTML = '<p class="no-productos">No se encontraron productos con esos filtros.</p>';
        return;
    }
    
    let html = '';
    productos.forEach(producto => {
        const imagen = producto.imagenPrincipal || 'default.jpg';
        html += `
            <div class="producto-card">
                <a href="${BASE_URL}/producto/detalle/${producto.id}">
                    <div class="producto-imagen">
                        <img src="${ASSETS_URL}/uploads/productos/${imagen}" 
                             alt="${producto.tipo}"
                             onerror="this.src='${ASSETS_URL}/uploads/productos/default.jpg'">
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
    console.log('agregarAlCarrito llamado con ID:', productoId);
    
    ajax('/pedido/agregarCarrito', 'POST', { idProducto: productoId, cantidad: 1 }, function(error, response) {
        console.log('Respuesta agregar carrito:', error, response);
        
        if (error) {
            console.error('Error al agregar al carrito:', error);
            mostrarMensaje('Error al agregar al carrito', 'error');
            return;
        }
        
        if (response.success) {
            mostrarMensaje(response.message || 'Producto agregado al carrito', 'success');
            if (response.totalItems) {
                actualizarContadorCarrito(response.totalItems);
            }
        } else {
            mostrarMensaje(response.message || 'Error al agregar al carrito', 'error');
            if (response.message && response.message.includes('sesión')) {
                setTimeout(function() {
                    window.location.href = BASE_URL + '/auth/login';
                }, 2000);
            }
        }
    });
}

// Actualizar contador carrito (header)
function actualizarContadorCarrito(totalItems) {
    console.log('Actualizando contador carrito:', totalItems);
    
    let badge = document.querySelector('.carrito-badge');
    const carritoLink = document.querySelector('a[href*="/pedido/carrito"]');
    
    if (!badge && carritoLink) {
        badge = document.createElement('span');
        badge.className = 'carrito-badge';
        carritoLink.appendChild(badge);
    }
    
    if (badge) {
        badge.textContent = totalItems;
        badge.style.display = totalItems > 0 ? 'inline-block' : 'none';
    }
}

// Loading
function mostrarLoading(mostrar) {
    const loading = document.getElementById('loading');
    if (loading) loading.style.display = mostrar ? 'block' : 'none';
}

// Mensajes flotantes
function mostrarMensaje(texto, tipo) {
    console.log('Mostrando mensaje:', texto, tipo);
    
    const mensaje = document.getElementById('mensaje');
    if (mensaje) {
        mensaje.textContent = texto;
        mensaje.className = 'mensaje ' + tipo;
        mensaje.style.display = 'block';
        setTimeout(() => mensaje.style.display = 'none', 3000);
    } else {
        const msg = document.createElement('div');
        msg.className = 'mensaje ' + tipo;
        msg.textContent = texto;
        msg.style.position = 'fixed';
        msg.style.top = '20px';
        msg.style.right = '20px';
        msg.style.zIndex = '2000';
        msg.style.padding = '1rem';
        msg.style.borderRadius = '5px';
        msg.style.boxShadow = '0 2px 8px rgba(0,0,0,0.2)';
        
        if (tipo === 'success') {
            msg.style.background = '#d4edda';
            msg.style.color = '#155724';
            msg.style.border = '1px solid #c3e6cb';
        } else {
            msg.style.background = '#f8d7da';
            msg.style.color = '#721c24';
            msg.style.border = '1px solid #f5c6cb';
        }
        
        document.body.appendChild(msg);
        setTimeout(() => {
            if (msg.parentNode) {
                document.body.removeChild(msg);
            }
        }, 3000);
    }
}

// Buscar productos (autocompletado)
function buscarProductos(termino) {
    console.log('buscarProductos llamado con:', termino);
    
    if (termino.length < 2) {
        const resultadosDiv = document.getElementById('resultadosBusqueda');
        if (resultadosDiv) resultadosDiv.style.display = 'none';
        return;
    }
    
    ajax('/producto/buscar', 'POST', { termino: termino }, function(error, productos) {
        console.log('Resultados búsqueda:', error, productos);
        if (error) {
            console.error('Error al buscar productos:', error);
            return;
        }
        mostrarResultadosBusqueda(productos);
    });
}

// Mostrar resultados de búsqueda en el dropdown
function mostrarResultadosBusqueda(productos) {
    const resultadosDiv = document.getElementById('resultadosBusqueda');
    const input = document.getElementById('busqueda');
    
    if (!resultadosDiv || !input) {
        console.error('No se encontró resultadosDiv o input');
        return;
    }
    
    if (!productos || productos.length === 0) {
        resultadosDiv.innerHTML = '<div class="resultado-item no-resultados">No se encontraron productos</div>';
        resultadosDiv.style.display = 'block';
        return;
    }
    
    let html = '';
    productos.slice(0, 5).forEach(producto => {
        const imagen = producto.imagenPrincipal || 'default.jpg';
        html += `
            <a href="${BASE_URL}/producto/detalle/${producto.id}" class="resultado-item">
                <img src="${ASSETS_URL}/uploads/productos/${imagen}" 
                     alt="${producto.tipo}"
                     onerror="this.src='${ASSETS_URL}/uploads/productos/default.jpg'">
                <div class="resultado-info">
                    <strong>${producto.tipo}</strong>
                    <span>${producto.marca} - ${producto.color}</span>
                    <span class="precio">${parseFloat(producto.precio).toFixed(2)} €</span>
                </div>
            </a>
        `;
    });
    
    if (productos.length > 5) {
        html += `<a href="${BASE_URL}/producto/resultados?q=${encodeURIComponent(input.value)}" class="ver-todos">
                    Ver todos los resultados (${productos.length})
                 </a>`;
    }
    
    resultadosDiv.innerHTML = html;
    resultadosDiv.style.display = 'block';
    console.log('Resultados mostrados');
}

// Cerrar resultados de búsqueda al hacer clic fuera
document.addEventListener('click', function(e) {
    const resultadosDiv = document.getElementById('resultadosBusqueda');
    const buscador = document.querySelector('.buscador');
    
    if (resultadosDiv && buscador && !buscador.contains(e.target)) {
        resultadosDiv.style.display = 'none';
    }
});