<?php 
$titulo = "Verificar Identidad";
require_once 'views/layout/header.php'; 
?>

<div class="auth-page">
    <div class="auth-form">
        <h1>Verificar Identidad</h1>
        <p>Responde a tu pregunta de seguridad para continuar.</p>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="mensaje error">
                <?php 
                echo $_SESSION['error']; 
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
        
        <form action="<?php echo BASE_URL; ?>/password/nueva" method="POST">
            <div class="form-grupo">
                <label>Pregunta de Seguridad:</label>
                <p><strong><?php echo htmlspecialchars($_SESSION['reset_pregunta']); ?></strong></p>
            </div>
            
            <div class="form-grupo">
                <label for="respuesta">Tu Respuesta *</label>
                <input type="text" id="respuesta" name="respuesta" required>
            </div>
            
            <button type="submit" class="btn-primary btn-block">Verificar</button>
        </form>
        
        <p class="auth-link"><a href="<?php echo BASE_URL; ?>/auth/login">Volver al inicio de sesión</a></p>
    </div>
</div>

<?php require_once 'views/layout/footer.php'; ?>
