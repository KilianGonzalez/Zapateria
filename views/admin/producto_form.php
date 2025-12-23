<?php 
$titulo = isset($producto) ? "Editar Producto" : "Crear Producto";
require_once 'views/layout/header.php'; 
?>

<div class="admin-page">
    <h1><?php echo isset($producto) ? 'Editar Producto' : 'Crear Nuevo Producto'; ?></h1>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="mensaje error">
            <?php 
            echo $_SESSION['error']; 
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>
    
    <form id="formProducto" 
          action="<?php echo BASE_URL; ?><?php echo isset($producto) ? '/admin/actualizarProducto/' . $producto['id'] : '/admin/guardarProducto'; ?>" 
          method="POST"
          <?php if (!isset($producto)): ?>enctype="multipart/form-data"<?php endif; ?>>
        
        <div class="form-grupo">
            <label for="idTipo">Tipo de Producto *</label>
            <select id="idTipo" name="idTipo" required>
                <option value="">Selecciona un tipo</option>
                <?php foreach ($tipos as $tipo): ?>
                    <option value="<?php echo $tipo['id']; ?>"
                            <?php echo (isset($producto) && $producto['idTipo'] == $tipo['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($tipo['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span class="error-mensaje" id="errorTipo"></span>
        </div>
        
        <div class="form-grupo">
            <label for="idMarca">Marca *</label>
            <select id="idMarca" name="idMarca" required>
                <option value="">Selecciona una marca</option>
                <?php foreach ($marcas as $marca): ?>
                    <option value="<?php echo $marca['id']; ?>"
                            <?php echo (isset($producto) && $producto['idMarca'] == $marca['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($marca['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span class="error-mensaje" id="errorMarca"></span>
        </div>
        
        <div class="form-grupo">
            <label for="color">Color *</label>
            <input type="text" id="color" name="color" required
                   value="<?php echo isset($producto) ? htmlspecialchars($producto['color']) : ''; ?>">
            <span class="error-mensaje" id="errorColor"></span>
        </div>
        
        <div class="form-grupo">
            <label for="talla">Talla *</label>
            <input type="text" id="talla" name="talla" required
                   value="<?php echo isset($producto) ? htmlspecialchars($producto['talla']) : ''; ?>">
            <span class="error-mensaje" id="errorTalla"></span>
        </div>
        
        <div class="form-grupo">
            <label for="precio">Precio (€) *</label>
            <input type="number" id="precio" name="precio" required step="0.01" min="0"
                   value="<?php echo isset($producto) ? $producto['precio'] : ''; ?>">
            <span class="error-mensaje" id="errorPrecio"></span>
        </div>
        
        <div class="form-grupo">
            <label for="sexo">Sexo *</label>
            <select id="sexo" name="sexo" required>
                <option value="">Selecciona</option>
                <option value="M" <?php echo (isset($producto) && $producto['sexo'] == 'M') ? 'selected' : ''; ?>>Hombre</option>
                <option value="F" <?php echo (isset($producto) && $producto['sexo'] == 'F') ? 'selected' : ''; ?>>Mujer</option>
                <option value="U" <?php echo (isset($producto) && $producto['sexo'] == 'U') ? 'selected' : ''; ?>>Unisex</option>
            </select>
            <span class="error-mensaje" id="errorSexo"></span>
        </div>
        
        <?php if (!isset($producto)): ?>
            <!-- SOLO MOSTRAR CAMPOS DE IMAGEN AL CREAR -->
            <hr style="margin: 2rem 0;">
            
            <h3>Imágenes del Producto</h3>
            <p style="color: #7f8c8d; margin-bottom: 1rem;">
                Puedes subir hasta 4 imágenes (JPG, PNG, GIF, WEBP - máximo 5MB cada una).
                <strong>La primera imagen será la principal.</strong>
            </p>
            
            <div class="form-grupo">
                <label for="imagen1">Imagen 1 (Principal) *</label>
                <input type="file" id="imagen1" name="imagen1" accept="image/*" required>
                <small>Esta será la imagen principal del producto</small>
            </div>
            
            <div class="form-grupo">
                <label for="imagen2">Imagen 2</label>
                <input type="file" id="imagen2" name="imagen2" accept="image/*">
            </div>
            
            <div class="form-grupo">
                <label for="imagen3">Imagen 3</label>
                <input type="file" id="imagen3" name="imagen3" accept="image/*">
            </div>
            
            <div class="form-grupo">
                <label for="imagen4">Imagen 4</label>
                <input type="file" id="imagen4" name="imagen4" accept="image/*">
            </div>
        <?php else: ?>
            <!-- MOSTRAR IMÁGENES ACTUALES SOLO COMO VISTA (NO EDITABLE) -->
            <?php if (!empty($imagenes)): ?>
                <hr style="margin: 2rem 0;">
                <h3>Imágenes del Producto</h3>
                <p style="color: #7f8c8d; margin-bottom: 1rem;">
                    Las imágenes no se pueden modificar al editar. Si necesitas cambiarlas, elimina el producto y créalo de nuevo.
                </p>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                    <?php foreach ($imagenes as $img): ?>
                        <div style="text-align: center;">
                            <img src="<?php echo ASSETS_URL; ?>/uploads/productos/<?php echo $img['rutaImagen']; ?>" 
                                 style="width: 100%; height: 150px; object-fit: contain; border: 1px solid #bdc3c7; border-radius: 5px; padding: 5px; background: #ecf0f1;">
                            <?php if ($img['esPrincipal']): ?>
                                <p style="color: #27ae60; font-weight: bold; margin-top: 0.5rem; font-size: 0.85rem;">Principal</p>
                            <?php else: ?>
                                <p style="color: #7f8c8d; margin-top: 0.5rem; font-size: 0.85rem;">Imagen <?php echo $img['orden']; ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <div class="form-acciones">
            <a href="<?php echo BASE_URL; ?>/admin/dashboard" class="btn-secondary">Cancelar</a>
            <button type="submit" class="btn-primary">
                <?php echo isset($producto) ? 'Actualizar' : 'Crear'; ?> Producto
            </button>
        </div>
    </form>
</div>

<?php require_once 'views/layout/footer.php'; ?>