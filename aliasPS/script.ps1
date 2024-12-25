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

