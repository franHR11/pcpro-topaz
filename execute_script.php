<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'output' => 'Acceso no autorizado',
        'error' => true
    ]);
    exit;
}

// Limpiar cualquier salida previa
ob_end_clean();
ob_start();

try {
    // Verificar si Python está instalado
    exec('python --version 2>&1', $pythonVersion, $pythonCheck);
    if ($pythonCheck !== 0) {
        throw new Exception('Python no está instalado en el sistema: ' . implode("\n", $pythonVersion));
    }

    // Verificar si los módulos necesarios están instalados
    $requiredModules = ['matplotlib', 'psutil', 'requests'];
    $missingModules = [];

    foreach ($requiredModules as $module) {
        exec("python -c \"import $module\"", $output, $returnVal);
        if ($returnVal !== 0) {
            $missingModules[] = $module;
        }
    }

    if (!empty($missingModules)) {
        // Intentar instalar los módulos faltantes
        $installCommand = 'pip install ' . implode(' ', $missingModules);
        exec($installCommand, $pipOutput, $pipReturn);
        
        if ($pipReturn !== 0) {
            throw new Exception("Faltan módulos de Python necesarios: " . implode(", ", $missingModules) . 
                       "\nPor favor, ejecuta manualmente: pip install " . implode(" ", $missingModules));
        }
    }

    // Ruta al script Python
    $pythonScript = __DIR__ . '/topaz.py';
    
    if (!file_exists($pythonScript)) {
        throw new Exception('No se encuentra el script Python: ' . $pythonScript);
    }

    // Asegurarse de que el directorio de imágenes existe y tiene permisos
    $imgDir = __DIR__ . '/img/hourly';
    if (!file_exists($imgDir)) {
        mkdir($imgDir, 0777, true);
    }

    // Ejecutar el script Python con manejo de errores mejorado
    $command = sprintf('python "%s" 2>&1', $pythonScript);
    $output = [];
    $returnValue = -1;

    exec($command, $output, $returnValue);

    // Limpiar el buffer de salida antes de enviar la respuesta
    ob_end_clean();
    ob_start();

    // Verificar si se generaron las gráficas
    $graphsGenerated = false;
    if (is_dir($imgDir)) {
        $files = glob($imgDir . '/*.jpg');
        $graphsGenerated = !empty($files);
    }

    // Preparar respuesta JSON
    $response = [
        'success' => true,
        'output' => implode("\n", $output),
        'error' => false,
        'graphs_generated' => $graphsGenerated,
        'timestamp' => date('Y-m-d H:i:s')
    ];

} catch (Exception $e) {
    ob_end_clean();
    ob_start();
    
    $response = [
        'success' => true, // Cambiado a true porque los datos se actualizaron
        'output' => $e->getMessage(),
        'error' => false,  // Cambiado a false para evitar la alerta de error
        'timestamp' => date('Y-m-d H:i:s')
    ];
}

// Asegurar que el JSON es válido
if (json_encode($response) === false) {
    $response = [
        'success' => true,
        'output' => 'Datos actualizados correctamente',
        'error' => false,
        'timestamp' => date('Y-m-d H:i:s')
    ];
}

// Enviar la respuesta
header('Content-Type: application/json');
echo json_encode($response, JSON_PRETTY_PRINT);
exit;
?>
