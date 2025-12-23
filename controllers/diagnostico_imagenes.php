<?php
require_once 'config.php';
require_once 'controllers/Database.php';

echo "<h2>Diagnóstico de Imágenes</h2>";

$database = new Database();
$conn = $database->getConnection();

// Obtener todas las imágenes de la BD
$query = "SELECT ip.*, p.id as producto_id, m.nombre as marca, t.nombre as tipo
          FROM ImagenesProducto ip
          INNER JOIN Productos p ON ip.idProducto = p.id
          LEFT JOIN Marcas m ON p.idMarca = m.id
          LEFT JOIN tipoProductos t ON p.idTipo = t.id
          ORDER BY m.nombre, p.id";

$stmt = $conn->query($query);
$imagenes = $stmt->fetchAll();

$carpetaImagenes = __DIR__ . '/assets/uploads/productos/';
$baseUrl = ASSETS_URL . '/uploads/productos/';

echo "<h3>Análisis de imágenes en la base de datos:</h3>";
echo "<p><strong>Carpeta física:</strong> " . $carpetaImagenes . "</p>";

// Verificar si existe la carpeta
if (!is_dir($carpetaImagenes)) {
    echo "<p style='color: red;'>⚠ La carpeta de imágenes NO EXISTE. Créala en: assets/uploads/productos/</p>";
    mkdir($carpetaImagenes, 0777, true);
    echo "<p style='color: green;'>✓ Carpeta creada</p>";
}

echo "<table border='1' cellpadding='8' style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background: #ecf0f1;'>
        <th>ID</th>
        <th>Producto</th>
        <th>Marca</th>
        <th>Ruta BD</th>
        <th>Estado</th>
        <th>Archivo Real</th>
        <th>Vista Previa</th>
      </tr>";

$imagenesNoEncontradas = [];
$archivosDisponibles = scandir($carpetaImagenes);

foreach ($imagenes as $img) {
    $rutaBD = $img['rutaImagen'];
    $rutaCompleta = $carpetaImagenes . $rutaBD;
    $existe = file_exists($rutaCompleta);
    
    echo "<tr>";
    echo "<td>{$img['id']}</td>";
    echo "<td>{$img['tipo']}</td>";
    echo "<td><strong>{$img['marca']}</strong></td>";
    echo "<td><code>{$rutaBD}</code></td>";
    
    if ($existe) {
        echo "<td style='color: green;'>✓ Existe</td>";
        echo "<td>" . basename($rutaBD) . "</td>";
        echo "<td><img src='{$baseUrl}{$rutaBD}' style='max-width: 80px; max-height: 80px; object-fit: contain;' /></td>";
    } else {
        echo "<td style='color: red;'>✗ NO EXISTE</td>";
        
        // Buscar archivo similar
        $nombreArchivo = basename($rutaBD);
        $nombreSinEspacios = str_replace(' ', '', $nombreArchivo);
        $nombreMinusculas = strtolower($nombreArchivo);
        $nombreMinusculasSinEspacios = strtolower($nombreSinEspacios);
        
        $archivoEncontrado = null;
        foreach ($archivosDisponibles as $archivo) {
            if ($archivo == '.' || $archivo == '..') continue;
            
            $archivoLower = strtolower($archivo);
            $archivoSinEspacios = str_replace(' ', '', $archivoLower);
            
            if ($archivoLower == $nombreMinusculas || 
                $archivoSinEspacios == $nombreMinusculasSinEspacios ||
                $archivo == $nombreArchivo ||
                $archivo == $nombreSinEspacios) {
                $archivoEncontrado = $archivo;
                break;
            }
        }
        
        if ($archivoEncontrado) {
            echo "<td style='color: orange;'>Posible: <strong>{$archivoEncontrado}</strong></td>";
            echo "<td><img src='{$baseUrl}{$archivoEncontrado}' style='max-width: 80px; max-height: 80px; object-fit: contain;' /></td>";
            $imagenesNoEncontradas[] = [
                'id' => $img['id'],
                'rutaBD' => $rutaBD,
                'archivoReal' => $archivoEncontrado
            ];
        } else {
            echo "<td style='color: red;'>No encontrado</td>";
            echo "<td>-</td>";
        }
    }
    
    echo "</tr>";
}

echo "</table>";

// Mostrar archivos en la carpeta
echo "<h3>Archivos disponibles en la carpeta:</h3>";
echo "<ul>";
foreach ($archivosDisponibles as $archivo) {
    if ($archivo == '.' || $archivo == '..') continue;
    echo "<li><code>{$archivo}</code> <img src='{$baseUrl}{$archivo}' style='max-width: 60px; max-height: 60px; object-fit: contain; vertical-align: middle;' /></li>";
}
echo "</ul>";

// Botón para corregir rutas
if (!empty($imagenesNoEncontradas)) {
    echo "<h3>Correcciones sugeridas:</h3>";
    echo "<form method='POST'>";
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Ruta Actual BD</th><th>Archivo Real</th><th>Acción</th></tr>";
    
    foreach ($imagenesNoEncontradas as $img) {
        echo "<tr>";
        echo "<td>{$img['id']}</td>";
        echo "<td><code>{$img['rutaBD']}</code></td>";
        echo "<td><strong>{$img['archivoReal']}</strong></td>";
        echo "<td>
                <input type='hidden' name='correcciones[{$img['id']}]' value='{$img['archivoReal']}'>
                Actualizar
              </td>";
        echo "</tr>";
    }
    
    echo "</table>";
    echo "<br><button type='submit' name='corregir' style='padding: 10px 20px; background: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;'>
            Corregir todas las rutas automáticamente
          </button>";
    echo "</form>";
}

// Procesar correcciones
if (isset($_POST['corregir']) && isset($_POST['correcciones'])) {
    echo "<h3>Aplicando correcciones...</h3>";
    
    foreach ($_POST['correcciones'] as $id => $nuevaRuta) {
        $queryUpdate = "UPDATE ImagenesProducto SET rutaImagen = ? WHERE id = ?";
        $stmtUpdate = $conn->prepare($queryUpdate);
        
        if ($stmtUpdate->execute([$nuevaRuta, $id])) {
            echo "<p style='color: green;'>✓ Actualizado ID {$id} a: {$nuevaRuta}</p>";
        } else {
            echo "<p style='color: red;'>✗ Error al actualizar ID {$id}</p>";
        }
    }
    
    echo "<p><a href='diagnostico_imagenes.php' style='padding: 10px 20px; background: #27ae60; color: white; text-decoration: none; border-radius: 5px;'>Recargar para verificar</a></p>";
}

echo "<hr>";
echo "<p><a href='" . BASE_URL . "/producto/index'>Volver a Productos</a></p>";
?>