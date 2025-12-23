<?php 
$titulo = htmlspecialchars($producto['tipo']) . " - Detalle";
require_once 'views/layout/header.php'; 
?>

<div class="producto-detalle">
    <div class="galeria-imagenes">
        <div class="imagen-principal">
            <?php if (!empty($imagenes)): ?>
                <img id="imagenPrincipal" 
                     src="<?php echo ASSETS_URL; ?>/uploads/productos/<?php echo $imagenes[0]['rutaImagen']; ?>" 
                     alt="<?php echo htmlspecialchars($producto['tipo']); ?>">
            <?php else: ?>
                <img id="imagenPrincipal" 
                     src="<?php echo ASSETS_URL; ?>/uploads/productos/default.jpg" 
                     alt="Sin imagen">
            <?php endif; ?>
        </div>
        
        <div class="miniaturas">
            <?php if (!empty($imagenes)): ?>
                <?php foreach ($imagenes as $index => $imagen): ?>
                    <img class="miniatura <?php echo $index === 0 ? 'active' : ''; ?>" 
                         src="<?php echo ASSETS_URL; ?>/uploads/productos/<?php echo $imagen['rutaImagen']; ?>" 
                         alt="Imagen <?php echo $index + 1; ?>"
                         onclick="cambiarImagen('<?php echo $imagen['rutaImagen']; ?>', this)">
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="producto-informacion">
        <h1><?php echo htmlspecialchars($producto['tipo']); ?></h1>
        <p class="marca-detalle">Marca: <strong><?php echo htmlspecialchars($producto['marca']); ?></strong></p>
        
        <div class="producto-specs">
            <p><strong>Color:</strong> <?php echo htmlspecialchars($producto['color']); ?></p>
            <p><strong>Talla:</strong> <?php echo htmlspecialchars($producto['talla']); ?></p>
            <p><strong>Sexo:</strong> 
                <?php 
                $sexo = $producto['sexo'] == 'M' ? 'Hombre' : ($producto['sexo'] == 'F' ? 'Mujer' : 'Unisex');
                echo $sexo;
                ?>
            </p>
        </div>
        
        <p class="precio-detalle"><?php echo number_format($producto['precio'], 2); ?> €</p>
        
        <div class="acciones">
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <button id="btnAgregarCarrito" class="btn-primary" data-producto-id="<?php echo $producto['id']; ?>">
                    Agregar al Carrito
                </button>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>/auth/login" class="btn-primary">Inicia sesión para comprar</a>
            <?php endif; ?>
            <a href="<?php echo BASE_URL; ?>/producto/index" class="btn-secondary">Volver a Productos</a>
        </div>
        
        <div id="mensaje" class="mensaje" style="display: none;"></div>
    </div>
</div>

<script>
function cambiarImagen(rutaImagen, elemento) {
    document.getElementById('imagenPrincipal').src = '<?php echo ASSETS_URL; ?>/uploads/productos/' + rutaImagen;
    
    document.querySelectorAll('.miniatura').forEach(img => img.classList.remove('active'));
    elemento.classList.add('active');
}
</script>

<?php require_once 'views/layout/footer.php'; ?>