<?php 
$titulo = htmlspecialchars($producto['tipo']) . " - Detalle";
require_once 'views/layout/header.php'; 
?>

<div class="producto-detalle">
    <div class="galeria-imagenes">
        <div class="imagen-principal">
            <?php if (!empty($imagenes)): ?>
                <img id="imagenPrincipal" src="/assets/uploads/productos/<?php echo $imagenes[0]['rutaImagen']; ?>" 
                     alt="<?php echo htmlspecialchars($producto['tipo']); ?>">
            <?php else: ?>
                <img id="imagenPrincipal" src="/assets/uploads/productos/default.jpg" alt="Sin imagen">
            <?php endif; ?>
        </div>
        
        <div class="miniaturas">
            <?php if (!empty($imagenes)): ?>
                <?php foreach ($imagenes as $index => $imagen): ?>
                    <img class="miniatura <?php echo $index === 0 ? 'active' : ''; ?>" 
                         src="/assets/uploads/productos/<?php echo $imagen['rutaImagen']; ?>" 
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
            <button id="btnAgregarCarrito" class="btn-primary" data-producto-id="<?php echo $producto['id']; ?>">
                Agregar al Carrito
            </button>
            <a href="/producto/index" class="btn-secondary">Volver a Productos</a>
        </div>
        
        <div id="mensaje" class="mensaje" style="display: none;"></div>
    </div>
</div>

<script>
function cambiarImagen(rutaImagen, elemento) {
    document.getElementById('imagenPrincipal').src = '/assets/uploads/productos/' + rutaImagen;
    
    // Remover clase active de todas las miniaturas
    document.querySelectorAll('.miniatura').forEach(img => img.classList.remove('active'));
    
    // Agregar clase active a la miniatura clickeada
    elemento.classList.add('active');
}
</script>

<?php require_once 'views/layout/footer.php'; ?>
