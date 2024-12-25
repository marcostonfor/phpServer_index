# phpServer_index

>## Documento index.php para el servidor web incrustado del lenguaje PHP

1. **Función `getIcon`**:
   ```php
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
   ```
> Esta función toma el nombre de un archivo o carpeta y su ruta completa, y devuelve un ícono HTML según el tipo de archivo (o si es una carpeta).

2. **Obtención del directorio actual**:
   ```php
   $currentDir = isset($_GET['dir']) ? realpath($_GET['dir']) : getcwd();

   if (!$currentDir || !is_dir($currentDir)) {
       die("Directorio no válido.");
   }
   ```
> Aquí se obtiene el directorio actual a explorar. Si no se ha pasado un directorio en la URL (`$_GET['dir']`), se usa el directorio actual (`getcwd()`). Si el directorio no es válido, se detiene la ejecución.

3. **Escaneo del directorio**:
   ```php
   $items = scandir($currentDir);
   ```
> Se escanea el contenido del directorio actual.

4. **Estructura HTML y estilos**:
   ```php
   echo "<!DOCTYPE html>";
   echo "<html lang='en'>";
   echo "<head>";
   echo "<meta charset='UTF-8'>";
   echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
   echo "<title>Explorador de Archivos</title>";
   echo "<style>";
   // Estilos CSS
   echo "</style>";
   echo "</head>";
   echo "<body>";
   echo "<h1>Explorando: &#x1f440; " . htmlspecialchars($currentDir) . "</h1>";
   ```

> Aquí se genera la estructura básica del HTML, incluyendo los estilos CSS para el diseño de la página.

5. **Enlace al directorio padre**:
   ```php
   $parentDir = dirname($currentDir);
   if ($parentDir && $parentDir !== $currentDir) {
       echo "<a class='back-link' href='?dir=" . urlencode($parentDir) . "'>&#x1f4c2;.. &#x21a9;</a>";
   }
   ```
> Se genera un enlace para navegar al directorio padre, si existe.

6. **Generación de la vista de archivos y carpetas**:
   ```php
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
   ```

> Se genera una vista en cuadrícula de todos los elementos del directorio, diferenciando entre carpetas y archivos y aplicando íconos según el tipo.

7. **Previsualización de imágenes**:
   ```php
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
   ```

> Se agrega un contenedor de previsualización de imágenes que se muestra cuando se pasa el cursor sobre enlaces de imágenes.

8. **Cargar íconos de FontAwesome**:
   ```php
   echo "<script src='https://kit.fontawesome.com/fa49ba9e26.js' crossorigin='anonymous'></script>";
   echo "</body>";
   echo "</html>";
   ```

> Finalmente, se carga el kit de íconos de FontAwesome y se cierra el HTML.

Este archivo `index.php` crea una interfaz gráfica para explorar archivos y carpetas en un directorio dado, mostrando íconos específicos según el tipo de archivo y permitiendo previsualizar imágenes al pasar el cursor sobre ellas.


