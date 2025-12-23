// Ejecutar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Filtros de productos
    const btnFiltrar = document.getElementById('btnFiltrar');
    if (btnFiltrar) btnFiltrar.addEventListener('click', filtrarProductos);
    
    const btnLimpiar = document.getElementById('btnLimpiar');
    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', function() {
            document.getElementById('filtroTipo').value = '';
            document.getElementById('filtroMarca').value = '';
            document.getElementById('filtroSexo').value = '';
            document.getElementById('precioMin').value = '';
            document.getElementById('precioMax').value = '';
            filtrarProductos();
        });
    }
    
    // Agregar al carrito desde detalle
    const btnAgregarCarrito = document.getElementById('btnAgregarCarrito');
    if (btnAgregarCarrito) {
        btnAgregarCarrito.addEventListener('click', function() {
            const productoId = this.getAttribute('data-producto-id');
            agregarAlCarrito(productoId);
        });
    }
    
    // Búsqueda
    const inputBusqueda = document.getElementById('busqueda');
    if (inputBusqueda) {
        let timeoutBusqueda;
        
        inputBusqueda.addEventListener('input', function() {
            const termino = this.value.trim();
            if (timeoutBusqueda) clearTimeout(timeoutBusqueda);
            
            if (termino.length < 2) {
                const resultadosDiv = document.getElementById('resultadosBusqueda');
                if (resultadosDiv) resultadosDiv.style.display = 'none';
                return;
            }
            
            timeoutBusqueda = setTimeout(function() {
                buscarProductos(termino);
            }, 300);
        });
        
        inputBusqueda.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const termino = this.value.trim();
                if (termino.length >= 2) {
                    window.location.href = BASE_URL + '/producto/resultados?q=' + encodeURIComponent(termino);
                }
            }
        });
    }
    
    const btnBuscar = document.getElementById('btnBuscar');
    if (btnBuscar && inputBusqueda) {
        btnBuscar.addEventListener('click', function() {
            const termino = inputBusqueda.value.trim();
            if (termino.length >= 2) {
                window.location.href = BASE_URL + '/producto/resultados?q=' + encodeURIComponent(termino);
            }
        });
    }
    
    // Cargar filtros iniciales (tipos y marcas)
    const filtroTipo = document.getElementById('filtroTipo');
    const filtroMarca = document.getElementById('filtroMarca');
    
    if (filtroTipo) {
        ajax('/producto/obtenerTipos', 'GET', null, function(error, tipos) {
            if (!error && tipos) {
                tipos.forEach(tipo => {
                    const option = document.createElement('option');
                    option.value = tipo.id;
                    option.textContent = tipo.nombre;
                    filtroTipo.appendChild(option);
                });
            }
        });
    }
    
    if (filtroMarca) {
        ajax('/producto/obtenerMarcas', 'GET', null, function(error, marcas) {
            if (!error && marcas) {
                marcas.forEach(marca => {
                    const option = document.createElement('option');
                    option.value = marca.id;
                    option.textContent = marca.nombre;
                    filtroMarca.appendChild(option);
                });
            }
        });
    }
    
    // Inicializar validaciones
    initValidacionLogin();
    initValidacionRegistro();
    initValidacionCheckout();
    initValidacionNuevaContrasena();
});