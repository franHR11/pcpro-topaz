<?php
// Start the session at the beginning of the script
session_start();

// Define valid credentials
$VALID_USERNAME = 'franHR';
$VALID_PASSWORD = 'franHR';

// Handle Logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // Destroy the session
    session_unset();
    session_destroy();
    // Redirect to the login form
    header("Location: index.php");
    exit;
}

// Handle Login Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    if ($username === $VALID_USERNAME && $password === $VALID_PASSWORD) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit;
    } else {
        $error = "Usuario o contraseña inválidos.";
    }
}

$loggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

// Function to retrieve images from a folder
function getImages($folder) {
    $images = [];
    if (is_dir($folder)) {
        $files = scandir($folder);
        foreach ($files as $file) {
            if (preg_match('/\.(jpg|jpeg|png|gif|svg)$/i', $file)) {
                $images[] = $file;
            }
        }
    }
    return $images;
}

// Define chart folders
$chartFolders = [
    'hourly' => 'img/hourly',
    'minute' => 'img/minute',
    'second' => 'img/second',
];

// Get selected chart type
$selectedType = isset($_GET['type']) && isset($chartFolders[$_GET['type']]) ? $_GET['type'] : 'hourly';
$images = $loggedIn ? getImages($chartFolders[$selectedType]) : [];

// Añadir nuevas funciones para leer datos
function readJsonMetrics() {
    $jsonFile = __DIR__ . '/metrics_export.json';
    if (file_exists($jsonFile)) {
        return json_decode(file_get_contents($jsonFile), true);
    }
    return null;
}

function getSystemAlerts() {
    $metrics = readJsonMetrics();
    $alerts = [];
    if ($metrics) {
        if ($metrics['metrics'][1] > 90) $alerts[] = "CPU Usage Critical: {$metrics['metrics'][1]}%";
        if ($metrics['metrics'][2] > 90) $alerts[] = "RAM Usage Critical: {$metrics['metrics'][2]}%";
        if ($metrics['metrics'][3] > 90) $alerts[] = "Disk Usage Critical: {$metrics['metrics'][3]}%";
    }
    return $alerts;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pcpro | Topaz</title>
    <link rel="icon" href="assent/topaz1.png" type="image/svg+xml">
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700&display=swap');

        /* Reset and General Styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: Ubuntu,'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            color: #333;
            min-height: 100vh;
        }

        /* Header Styles */
        .header {
            background-color: #007bff;
            width: 100%;
            padding: 15px 30px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header .app-name {
            font-size: 1.5rem;
            font-weight: bold;
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            justify-content: center;
            align-items: center;
            align-content: stretch;
        }
        .header .app-name img{
            width:80px;
            margin-right:20px;
        }
        .header .logout-button {
            background-color: #007bff;
            color: white;
            border: 2px solid white;
            padding: 8px 16px;
            border-radius: 25px;
            cursor: pointer;
            text-decoration: none;
            font-size: 1rem;
            transition: background-color 0.3s, border-color 0.3s;
        }
        .header .logout-button:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background: #fff;
            padding: 20px;
            border-right: 1px solid #ddd;
            position: fixed;
            top: 110px; /* Height of the header */
            bottom: 0;
            overflow-y: auto;
            box-shadow: 2px 0 5px rgba(0,0,0,0.05);
        }
        .sidebar h3 {
            margin-bottom: 20px;
            color: #007bff;
            font-size: 1.2rem;
        }
        .sidebar a {
            display: block;
            padding: 12px 16px;
            margin: 8px 0;
            color: #007bff;
            text-decoration: none;
            border-radius: 8px;
            transition: background-color 0.3s, color 0.3s;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: #007bff;
            color: white;
        }

        /* Content Styles */
        .content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            margin-top: 110px; /* Height of the header */
            margin-left: 270px; /* Width of the sidebar + margin */
        }
        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        .image-card {
            text-align: center;
            background: #fff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .image-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
        }
        .image-card img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
        }

        /* Modal Styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 200; /* Sit on top */
            padding-top: 60px; /* Location of the box */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgba(0,0,0,0.8); /* Black w/ opacity */
        }
        .modal-content {
            margin: auto;
            display: block;
            max-width: 90%;
            max-height: 80%;
            border-radius: 10px;
            animation-name: zoom;
            animation-duration: 0.6s;
        }
        @keyframes zoom {
            from {transform:scale(0)}
            to {transform:scale(1)}
        }
        .close {
            position: absolute;
            top: 30px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            transition: color 0.3s;
            cursor: pointer;
        }
        .close:hover,
        .close:focus {
            color: #bbb;
            text-decoration: none;
            cursor: pointer;
        }

        /* Login Styles */
        .login-container {
            background: #fff;
            padding: 40px 30px;
            max-width: 400px;
            width: 90%;
            margin: 100px auto;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            text-align: center;
            position: relative;
        }
        .login-container .logo {
            width: 100px;
            margin-bottom: 20px;
        }
        .login-container h2 {
            margin-bottom: 20px;
            color: #007bff;
            font-size: 1.8rem;
        }
        .login-container .error {
            background-color: #f8d7da;
            color: #842029;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 0.95rem;
        }
        .login-container label {
            display: block;
            margin-bottom: 5px;
            text-align: left;
            color: #555;
            font-size: 0.95rem;
        }
        .login-container input {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        .login-container input:focus {
            border-color: #007bff;
            outline: none;
        }
        .login-container button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            border: none;
            color: white;
            font-size: 1rem;
            border-radius: 25px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .login-container button:hover {
            background-color: #0056b3;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }
            .content {
                margin-left: 220px;
            }
        }
        @media (max-width: 576px) {
            .sidebar {
                display: none;
            }
            .content {
                margin-left: 0;
            }
            .header {
                flex-direction: column;
                align-items: flex-start;
            }
            .header .logout-button {
                margin-top: 10px;
            }
        }

        /* Add new styles */
        .alerts-panel, .processes-panel, .services-panel {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .alert {
            background-color: #fff3cd;
            color: #856404;
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 5px;
            border-left: 4px solid #ffeeba;
        }

        .process-item, .service-item {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            margin-bottom: 5px;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .process-item span, .service-item span {
            padding: 0 10px;
        }

        .update-button {
            width: 100%;
            padding: 12px 16px;
            margin: 8px 0;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: 1rem;
        }

        .update-button:hover {
            background-color: #218838;
        }

        .update-button:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }

        #updateStatus {
            margin-top: 10px;
            padding: 10px;
            border-radius: 4px;
            display: none;
        }

        .status-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Estilo para el mensaje de actualización */
        .update-message {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #28a745;
            color: white;
            padding: 15px 25px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        .update-message.hiding {
            animation: slideOut 0.5s ease-in;
        }

        /* Estilos actualizados para el mensaje */
        .update-message {
            position: fixed;
            top: 120px;
            right: 20px;
            background-color: #28a745;
            color: white;
            padding: 15px 25px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 9999;
            display: none;
            opacity: 0;
            transform: translateX(100%);
        }

        .update-message.show {
            display: block;
            opacity: 1;
            transform: translateX(0);
            transition: all 0.5s ease-out;
        }

        .update-message.hide {
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.5s ease-in;
        }

        /* Estilos actualizados para el mensaje */
        .update-message {
            position: fixed;
            top: 120px;
            right: 20px;
            background-color: #28a745;
            color: white;
            padding: 15px 25px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 9999;
            display: none;
            transition: all 0.3s ease;
        }

        .update-message.visible {
            display: block;
            animation: slideIn 0.5s forwards;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Estilos para la última actualización */
        .last-update {
            color: white;
            font-size: 0.9rem;
            font-weight: 500;
            text-align: center;
            padding: 5px 15px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            margin: 0 20px;
        }

        /* Actualizar el estilo del header para acomodar el nuevo elemento */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
        }

        .header .app-name {
            flex: 1;
        }

        .header .logout-button {
            flex: 0 0 auto;
        }

        /* Ajustes responsive */
        @media (max-width: 768px) {
            .last-update {
                font-size: 0.8rem;
                padding: 3px 10px;
            }
        }

        @media (max-width: 576px) {
            .header {
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }
            
            .last-update {
                order: 2;
                margin: 10px 0;
            }
            
            .header .logout-button {
                order: 3;
            }
        }
    </style>
</head>
<body>
    <?php if ($loggedIn): ?>
        <!-- Header -->
        <div class="header">
            <div class="app-name"><img src="assent/topaz1.png">Pcpro | Topaz</div>
            <div class="last-update">
                <?php 
                $metrics = readJsonMetrics();
                if ($metrics && isset($metrics['timestamp'])) {
                    $timestamp = new DateTime($metrics['timestamp']);
                    echo "Última actualización: " . $timestamp->format('d/m/Y H:i:s');
                }
                ?>
            </div>
            <a href="index.php?action=logout" class="logout-button">Cerrar Sesión</a>
        </div>

        <!-- Sidebar -->
        <div class="sidebar">
            <h3>Panel de Control</h3>
            <a href="index.php?type=hourly" class="<?php echo $selectedType === 'hourly' ? 'active' : ''; ?>">Métricas por Hora</a>
            <a href="index.php?view=processes">Procesos Activos</a>
            <a href="index.php?view=services">Servicios Web</a>
            <a href="index.php?view=alerts">Alertas del Sistema</a>
            <a href="metrics_export.json" target="_blank">Exportar JSON</a>
            <a href="metrics.db" download>Descargar Base de Datos</a>
            <button id="updateMetrics" class="update-button">Actualizar Métricas</button>
        </div>

        <!-- Main Content -->
        <div class="content">
            <?php
            // Mostrar alertas si existen
            $alerts = getSystemAlerts();
            if (!empty($alerts)): ?>
                <div class="alerts-panel">
                    <h3>Alertas Activas</h3>
                    <?php foreach($alerts as $alert): ?>
                        <div class="alert"><?php echo htmlspecialchars($alert); ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['view'])): ?>
                <?php if ($_GET['view'] === 'processes'): ?>
                    <div class="processes-panel">
                        <h3>Procesos Monitorizados</h3>
                        <?php
                        $metrics = readJsonMetrics();
                        if ($metrics && isset($metrics['processes']) && !empty($metrics['processes'])):
                            foreach ($metrics['processes'] as $process): ?>
                                <div class="process-item">
                                    <span>PID: <?php echo htmlspecialchars($process['pid']); ?></span>
                                    <span>Name: <?php echo htmlspecialchars($process['name']); ?></span>
                                    <span>CPU: <?php echo htmlspecialchars($process['cpu_percent']); ?>%</span>
                                    <span>Memory: <?php echo htmlspecialchars($process['memory_percent']); ?>%</span>
                                </div>
                            <?php endforeach;
                        else: ?>
                            <div class="alert alert-info">
                                No se encontraron procesos monitorizados. Verifica que XAMPP esté en ejecución.
                            </div>
                        <?php endif; ?>
                    </div>
                <?php elseif ($_GET['view'] === 'services'): ?>
                    <div class="services-panel">
                        <h3>Estado de Servicios Web</h3>
                        <?php
                        $metrics = readJsonMetrics();
                        if ($metrics && isset($metrics['web_services'])):
                            foreach ($metrics['web_services'] as $service): ?>
                                <div class="service-item">
                                    <span>URL: <?php echo htmlspecialchars($service['url']); ?></span>
                                    <span>Status: <?php echo htmlspecialchars($service['status']); ?></span>
                                    <?php if (isset($service['response_time'])): ?>
                                        <span>Response Time: <?php echo htmlspecialchars($service['response_time']); ?>s</span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach;
                        endif; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="dashboard">
                    <?php if (!empty($images)): ?>
                        <?php foreach ($images as $image): ?>
                            <div class="image-card" onclick="openModal('<?php echo htmlspecialchars($chartFolders[$selectedType] . '/' . $image); ?>')">
                                <img src="<?php echo htmlspecialchars($chartFolders[$selectedType] . '/' . $image); ?>" alt="Chart">
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No hay gráficas disponibles en la carpeta seleccionada.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Agregar justo después del div content -->
        <div id="updateMessage" class="update-message">
            <span class="message-text"></span>
        </div>

        <!-- The Modal -->
        <div id="myModal" class="modal">
            <span class="close" onclick="closeModal()">&times;</span>
            <img class="modal-content" id="modalImage" alt="Chart Enlarged">
        </div>

        <!-- JavaScript for Modal -->
        <script>
            function openModal(src) {
                var modal = document.getElementById("myModal");
                var modalImg = document.getElementById("modalImage");
                modal.style.display = "block";
                modalImg.src = src;
            }

            function closeModal() {
                var modal = document.getElementById("myModal");
                modal.style.display = "none";
            }

            // Close the modal when clicking outside the image
            window.onclick = function(event) {
                var modal = document.getElementById("myModal");
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }

            // Un solo event listener para el botón
            document.getElementById('updateMetrics').addEventListener('click', function() {
                const button = this;
                button.disabled = true;
                button.textContent = 'Actualizando...';

                fetch('execute_script.php')
                    .then(response => response.text())
                    .then(text => {
                        try {
                            const data = JSON.parse(text);
                            showUpdateMessage('Datos actualizados correctamente', 'success');
                        } catch (e) {
                            console.log('Respuesta del servidor:', text);
                            showUpdateMessage('Datos actualizados correctamente', 'success');
                        }
                    })
                    .catch(error => {
                        console.log('Error completo:', error);
                        showUpdateMessage('Error al actualizar los datos', 'error');
                        button.disabled = false;
                        button.textContent = 'Actualizar Métricas';
                    });
            });

            // Función única para mostrar el mensaje
            function showUpdateMessage(message = 'Datos actualizados correctamente', type = 'success') {
                const messageEl = document.getElementById('updateMessage');
                const messageText = messageEl.querySelector('.message-text') || messageEl;
                messageText.textContent = message;
                messageEl.style.backgroundColor = type === 'success' ? '#28a745' : '#dc3545';
                messageEl.classList.add('visible');
                
                // Actualizar la hora mostrada
                const lastUpdate = document.querySelector('.last-update');
                if (lastUpdate) {
                    const now = new Date();
                    lastUpdate.textContent = 'Última actualización: ' + 
                        now.toLocaleDateString() + ' ' + now.toLocaleTimeString();
                }
                
                setTimeout(() => {
                    messageEl.classList.remove('visible');
                    setTimeout(() => {
                        if (type === 'success') {
                            location.reload();
                        }
                    }, 500);
                }, 2000);
            }
        </script>
    <?php else: ?>
        <div class="login-container">
            <img src="assent/topaz1.png" alt="Topaz Logo" class="logo">
            <h2>Iniciar Sesión</h2>
            <?php if (isset($error)): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form action="index.php" method="post">
                <label for="username">Usuario</label>
                <input type="text" id="username" name="username" required placeholder="Ingresa tu usuario">
                
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required placeholder="Ingresa tu contraseña">
                
                <button type="submit" name="login">Entrar</button>
            </form>
        </div>
    <?php endif; ?>
</body>
</html>

