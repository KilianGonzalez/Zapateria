// Validación de formularios (cliente)

// Helpers
function setError(idInput, idError, mensaje) {
    const input = document.getElementById(idInput);
    const span = document.getElementById(idError);
    if (input) input.classList.add('input-error');
    if (span) span.textContent = mensaje;
}

function clearError(idInput, idError) {
    const input = document.getElementById(idInput);
    const span = document.getElementById(idError);
    if (input) input.classList.remove('input-error');
    if (span) span.textContent = '';
}

// LOGIN
function initValidacionLogin() {
    const form = document.getElementById('formLogin');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        let valido = true;
        const correo = document.getElementById('correo').value.trim();
        const contrasena = document.getElementById('contrasena').value;
        
        clearError('correo', 'errorCorreo');
        clearError('contrasena', 'errorContrasena');
        
        const emailRegex = /^([a-zA-Z0-9._%+-]+)@([a-zA-Z0-9.-]+)\.([a-zA-Z]{2,})$/;
        if (!emailRegex.test(correo)) {
            setError('correo', 'errorCorreo', 'Correo electrónico no válido');
            valido = false;
        }
        
        if (contrasena.length < 6) {
            setError('contrasena', 'errorContrasena', 'La contraseña debe tener al menos 6 caracteres');
            valido = false;
        }
        
        if (!valido) e.preventDefault();
    });
}

// REGISTRO (incluye AJAX correo)
function initValidacionRegistro() {
    const form = document.getElementById('formRegistro');
    const inputCorreo = document.getElementById('correo');
    if (!form || !inputCorreo) return;
    
    let correoDisponible = false;
    let timeoutAjax = null;
    
    inputCorreo.addEventListener('input', function() {
        const correo = this.value.trim();
        clearError('correo', 'errorCorreo');
        const info = document.getElementById('infoCorreo');
        if (info) info.textContent = '';
        
        if (timeoutAjax) clearTimeout(timeoutAjax);
        
        if (correo.length < 5) return;
        
        timeoutAjax = setTimeout(function() {
            ajax('/auth/verificarCorreo', 'POST', { correo: correo }, function(error, resp) {
                if (error) return;
                const infoSpan = document.getElementById('infoCorreo');
                if (!infoSpan) return;
                
                if (resp.existe) {
                    infoSpan.textContent = 'Este correo ya está registrado';
                    infoSpan.style.color = 'red';
                    correoDisponible = false;
                } else {
                    infoSpan.textContent = 'Correo disponible';
                    infoSpan.style.color = 'green';
                    correoDisponible = true;
                }
            });
        }, 400);
    });
    
    form.addEventListener('submit', function(e) {
        let valido = true;
        
        const nombre = document.getElementById('nombre').value.trim();
        const correo = document.getElementById('correo').value.trim();
        const contrasena = document.getElementById('contrasena').value;
        const contrasenaConfirm = document.getElementById('contrasena_confirm').value;
        const pregunta = document.getElementById('preguntaSeguridad').value;
        const respuesta = document.getElementById('respuestaSeguridad').value.trim();
        
        clearError('nombre', 'errorNombre');
        clearError('correo', 'errorCorreo');
        clearError('contrasena', 'errorContrasena');
        clearError('contrasena_confirm', 'errorContrasenaConfirm');
        clearError('preguntaSeguridad', 'errorPregunta');
        clearError('respuestaSeguridad', 'errorRespuesta');
        
        if (nombre.length < 2) {
            setError('nombre', 'errorNombre', 'El nombre es obligatorio');
            valido = false;
        }
        
        const emailRegex = /^([a-zA-Z0-9._%+-]+)@([a-zA-Z0-9.-]+)\.([a-zA-Z]{2,})$/;
        if (!emailRegex.test(correo)) {
            setError('correo', 'errorCorreo', 'Correo electrónico no válido');
            valido = false;
        }
        
        if (!correoDisponible) {
            setError('correo', 'errorCorreo', 'El correo ya está registrado o no ha sido verificado');
            valido = false;
        }
        
        if (contrasena.length < 6) {
            setError('contrasena', 'errorContrasena', 'Mínimo 6 caracteres');
            valido = false;
        }
        
        if (contrasena !== contrasenaConfirm) {
            setError('contrasena_confirm', 'errorContrasenaConfirm', 'Las contraseñas no coinciden');
            valido = false;
        }
        
        if (!pregunta) {
            setError('preguntaSeguridad', 'errorPregunta', 'Selecciona una pregunta');
            valido = false;
        }
        
        if (respuesta.length < 2) {
            setError('respuestaSeguridad', 'errorRespuesta', 'La respuesta es obligatoria');
            valido = false;
        }
        
        if (!valido) e.preventDefault();
    });
}

// CHECKOUT
function initValidacionCheckout() {
    const form = document.getElementById('formCheckout');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        let valido = true;
        const direccion = document.getElementById('direccion').value.trim();
        const cuenta = document.getElementById('cuentaBancaria').value.trim();
        
        clearError('direccion', 'errorDireccion');
        clearError('cuentaBancaria', 'errorCuenta');
        
        if (direccion.length < 10) {
            setError('direccion', 'errorDireccion', 'Introduce una dirección más detallada');
            valido = false;
        }
        
        const ibanRegex = /^ES\d{22}$/;
        if (!ibanRegex.test(cuenta)) {
            setError('cuentaBancaria', 'errorCuenta', 'Formato inválido. Debe ser ES seguido de 22 dígitos');
            valido = false;
        }
        
        if (!valido) e.preventDefault();
    });
}

// NUEVA CONTRASEÑA
function initValidacionNuevaContrasena() {
    const form = document.getElementById('formNuevaContrasena');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        let valido = true;
        const pass = document.getElementById('nueva_contrasena').value;
        const pass2 = document.getElementById('confirmar_contrasena').value;
        
        clearError('nueva_contrasena', 'errorNuevaContrasena');
        clearError('confirmar_contrasena', 'errorConfirmarContrasena');
        
        if (pass.length < 6) {
            setError('nueva_contrasena', 'errorNuevaContrasena', 'Mínimo 6 caracteres');
            valido = false;
        }
        
        if (pass !== pass2) {
            setError('confirmar_contrasena', 'errorConfirmarContrasena', 'Las contraseñas no coinciden');
            valido = false;
        }
        
        if (!valido) e.preventDefault();
    });
}