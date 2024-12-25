<?php
function getIcon($item, $fullPath)
{
    if (is_dir($fullPath)) {
        return "&#x1f4c2;"; // Carpeta abierta
    }

    $extension = pathinfo($item, PATHINFO_EXTENSION);

    // Iconos según la extensión
    switch (strtolower($extension)) {
        case 'txt':
            return "&#x1f4dc;"; // Documento
        case 'md':
            return "<i class='fa-brands fa-markdown'></i>";
        case 'png':
        case 'jpg':
        case 'jpeg':
        case 'svg':
            return "<i class='fa-solid fa-image'></i>"; // Imagen
        case 'php':
            return "<i class='fa-solid fa-file-code'></i>";
        case 'html':
        case 'js':
        case 'css':
            return "<i class='fa-solid fa-code'></i>"; // Código
        default:
            return "&#x1f4c4;"; // Archivo genérico
    }
}

// Obtener el directorio actual
$currentDir = isset($_GET['dir']) ? realpath($_GET['dir']) : getcwd();

if (!$currentDir || !is_dir($currentDir)) {
    die("Directorio no válido.");
}

// Escanear el directorio
$items = scandir($currentDir);

echo "<!DOCTYPE html>";
echo "<html lang='en'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>Explorador de Archivos</title>";
echo "<style>";
echo "
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
        padding: 0;
        box-sizing: border-box;
    }
    h1 {
        font-size: 12pt;
        width: fit-content;
        margin: 3vh 1vw;
        text-shadow: 0.1vw 0.1vw 3px hsl(19, 100%, 50%);
    }
    .grid-container {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 10px;
        width: 85%;
        margin: auto auto;
    }
    .grid-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 10px;
        text-align: center;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #f9f9f9;
        transition: transform 0.2s, background-color 0.2s;
    }
    .grid-item:hover {
        transform: scale(1.05);
        background-color: #eaeaea;
    }
    .icon {
        font-size: 2rem;
        margin-bottom: 10px;
    }
    a {
        text-decoration: none;
        color: #007bff;
        font-weight: bold;
    }
    a:hover {
        text-decoration: underline;
    }
    .back-link {
        display: block;
        margin: 20px 0;
        text-align: left;
    }
";
echo "</style>";
echo "</head>";
echo "<body>";
echo "<h1>Explorando: &#x1f440; " . htmlspecialchars($currentDir) . "</h1>";

// Enlace para volver al directorio padre
$parentDir = dirname($currentDir);
if ($parentDir && $parentDir !== $currentDir) {
    echo "<a class='back-link' href='?dir=" . urlencode($parentDir) . "'>&#x1f4c2;.. &#x21a9;</a>";
}

echo "<div class='grid-container'>";
foreach ($items as $item) {
    if ($item === '.' || $item === '..') {
        continue;
    }

    $fullPath = $currentDir . DIRECTORY_SEPARATOR . $item;
    $icon = getIcon($item, $fullPath);

    echo "<div class='grid-item'>";
    echo "<div class='icon'>$icon</div>";

    // Carpeta: Enlace al directorio interno
    if (is_dir($fullPath)) {
        echo "<a href='?dir=" . urlencode($fullPath) . "'>$item</a>";
    }
    // Archivo: Enlace directo al archivo
    else {
        $urlPath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $fullPath);
        $fileExtension = pathinfo($item, PATHINFO_EXTENSION);

        // Si es una imagen, agrega el atributo data-preview
        if (in_array(strtolower($fileExtension), ['png', 'jpg', 'jpeg', 'svg'])) {
            echo "<a href='" . htmlspecialchars($urlPath) . "' target='_blank' data-preview='" . htmlspecialchars($urlPath) . "'>$item</a>";
        } else {
            echo "<a href='" . htmlspecialchars($urlPath) . "' target='_blank'>$item</a>";
        }
    }

    echo "</div>";
}

echo "</div>";
echo "<div id='preview-container' style='display: none; position: absolute; z-index: 1000; pointer-events: none;'>
    <img id='preview-image' src='' alt='Previsualización' style='max-width: 300px; max-height: 300px; border: 2px solid #ddd; border-radius: 5px; box-shadow: 0px 4px 6px rgba(0,0,0,0.2);'>
</div>";
echo "<script>
document.addEventListener('DOMContentLoaded', () => {
    const previewContainer = document.getElementById('preview-container');
    const previewImage = document.getElementById('preview-image');

    document.querySelectorAll('[data-preview]').forEach(link => {
        link.addEventListener('mouseover', (e) => {
            const imageUrl = link.getAttribute('data-preview');
            previewImage.src = imageUrl;
            previewContainer.style.display = 'block';
        });

        link.addEventListener('mousemove', (e) => {
            // Verifica que las coordenadas sean válidas
            const mouseX = e.pageX || e.clientX + window.scrollX;
            const mouseY = e.pageY || e.clientY + window.scrollY;

            // Asigna las posiciones al contenedor de previsualización
            previewContainer.style.left = (mouseX + 15) + 'px';
            previewContainer.style.top = (mouseY + 15) + 'px';
        });

        link.addEventListener('mouseout', () => {
            previewContainer.style.display = 'none';
            previewImage.src = '';
        });
    });
});

</script>";

echo "<script src='https://kit.fontawesome.com/fa49ba9e26.js' crossorigin='anonymous'></script>";
echo "</body>";
echo "</html>";

