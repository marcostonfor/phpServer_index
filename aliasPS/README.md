# Script de PowerShell para crear el alias:

> `server`
```powershell
function startServer {
    $indexPath = "C:/Users/marcf/Documents/index.php"
    $currentDir = Get-Location

    # Copiar index.php al directorio actual si no existe
    if (-Not (Test-Path "$currentDir/index.php")) {
        Copy-Item $indexPath $currentDir
    }

    # Iniciar servidor PHP en segundo plano sin privilegios de administrador
    $serverProcess = Start-Process -NoNewWindow -FilePath "php" -ArgumentList "-S 127.0.0.1:8080" -PassThru

    # Esperar a que se presione una tecla para detener el servidor
    Read-Host -Prompt "Presiona ENTER para detener el servidor"

    # Detener el servidor PHP
    Stop-Process -Id $serverProcess.Id

    # Esperar un momento para asegurarse de que el servidor se ha detenido
    Start-Sleep -Seconds 1

    # Eliminar el index.php copiado
    Remove-Item "$currentDir/index.php" -Force
}

Set-Alias -Name server -Value startServer
```

Este script:
1. Copia el archivo `index.php` al directorio actual si no existe.
2. Inicia el `servidor PHP` en segundo plano sin requerir privilegios de administrador.
3. Espera a que se presione `ENTER` para detener el servidor.
4. `Detiene` el servidor PHP.
5. Espera un momento para asegurar que el servidor se ha detenido completamente.
6. Elimina el archivo `index.php` copiado.

Al usar `Start-Process` con la opción `-NoNewWindow`, el servidor se ejecutará en segundo plano y podrás detenerlo sin necesidad de privilegios especiales. Así, cualquier usuario puede ejecutar este script sin problemas.

