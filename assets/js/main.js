// Ejecutar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    
    // Filtros de productos
    const btnFiltrar = document.getElementById('btnFiltrar');
    if (btnFiltrar) {
        btnFiltrar.addEventListener('click', filtrarProductos);
    }
    
    const btnLimpiar = document.getElementById('btnLimpiar');
    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', limpiarFiltros);
    }
    
    // Agregar al carrito
    const btnAgregarCarrito = document.getElementById('btnAgregarCarrito');
    if (btnAgregarCarrito) {
        btnAgregarCarrito.addEventListener('click', function() {
            const productoId = this.getAttribute('data-producto-id');
            agregarAlCarrito(productoId);
        });
    }
    
    // Cargar tipos de productos y marcas para los filtros
    cargarFiltrosIniciales();
});

// Cargar tipos y marcas en los selectores
function cargarFiltrosIniciales() {
    // Cargar tipos de productos
    ajax('/producto/obtenerTipos', 'GET', null, function(error, tipos) {
        if (!error && tipos) {
            const selectTipo = document.getElementById('filtroTipo');
            if (selectTipo) {
                tipos.forEach(tipo => {
                    const option = document.createElement('option');
                    option.value = tipo.id;
                    option.textContent = tipo.nombre;
                    selectTipo.appendChild(option);
                });
            }
        }
    });
    
    // Cargar marcas
    ajax('/producto/obtenerMarcas', 'GET', null, function(error, marcas) {
        if (!error && marcas) {
            const selectMarca = document.getElementById('filtroMarca');
            if (selectMarca) {
                marcas.forEach(marca => {
                    const option = document.createElement('option');
                    option.value = marca.id;
                    option.textContent = marca.nombre;
                    selectMarca.appendChild(option);
                });
            }
        }
    });
}

// Ejecutar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    
    // Filtros de productos
    const btnFiltrar = document.getElementById('btnFiltrar');
    if (btnFiltrar) {
        btnFiltrar.addEventListener('click', filtrarProductos);
    }
    
    const btnLimpiar = document.getElementById('btnLimpiar');
    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', limpiarFiltros);
    }
    
    // Agregar al carrito
    const btnAgregarCarrito = document.getElementById('btnAgregarCarrito');
    if (btnAgregarCarrito) {
        btnAgregarCarrito.addEventListener('click', function() {
            const productoId = this.getAttribute('data-producto-id');
            agregarAlCarrito(productoId);
        });
    }
    
    // Búsqueda de productos
    const inputBusqueda = document.getElementById('busqueda');
    if (inputBusqueda) {
        let timeoutBusqueda;
        
        inputBusqueda.addEventListener('input', function() {
            clearTimeout(timeoutBusqueda);
            const termino = this.value.trim();
            
            if (termino.length < 2) {
                document.getElementById('resultadosBusqueda').style.display = 'none';
                return;
            }
            
            // Esperar 300ms después de que el usuario deje de escribir
            timeoutBusqueda = setTimeout(function() {
                buscarProductos(termino);
            }, 300);
        });
        
        inputBusqueda.addEventListener('focus', function() {
            if (this.value.trim().length >= 2) {
                buscarProductos(this.value.trim());
            }
        });
    }
    
    // Botón de búsqueda
    const btnBuscar = document.getElementById('btnBuscar');
    if (btnBuscar) {
        btnBuscar.addEventListener('click', function() {
            const termino = document.getElementById('busqueda').value.trim();
            if (termino.length >= 2) {
                window.location.href = '/producto/resultados?q=' + encodeURIComponent(termino);
            }
        });
    }
    
    // Enter en el campo de búsqueda
    if (inputBusqueda) {
        inputBusqueda.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const termino = this.value.trim();
                if (termino.length >= 2) {
                    window.location.href = '/producto/resultados?q=' + encodeURIComponent(termino);
                }
            }
        });
    }
    
    // Cargar tipos de productos y marcas para los filtros
    cargarFiltrosIniciales();
});

// Cargar tipos y marcas en los selectores
function cargarFiltrosIniciales() {
    // Cargar tipos de productos
    ajax('/producto/obtenerTipos', 'GET', null, function(error, tipos) {
        if (!error && tipos) {
            const selectTipo = document.getElementById('filtroTipo');
            if (selectTipo) {
                tipos.forEach(tipo => {
                    const option = document.createElement('option');
                    option.value = tipo.id;
                    option.textContent = tipo.nombre;
                    selectTipo.appendChild(option);
                });
            }
        }
    });
    
    // Cargar marcas
    ajax('/producto/obtenerMarcas', 'GET', null, function(error, marcas) {
        if (!error && marcas) {
            const selectMarca = document.getElementById('filtroMarca');
            if (selectMarca) {
                marcas.forEach(marca => {
                    const option = document.createElement('option');
                    option.value = marca.id;
                    option.textContent = marca.nombre;
                    selectMarca.appendChild(option);
                });
            }
        }
    });
}

