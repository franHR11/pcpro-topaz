# Sistema de Monitorización PCPRO-TOPAZ

## Descripción
PCPRO-TOPAZ es un sistema de monitorización avanzado que permite realizar un seguimiento en tiempo real de varios aspectos del sistema, incluyendo CPU, RAM, disco, red y más.

## Características
- Monitorización en tiempo real de:
  - Uso de CPU
  - Uso de RAM
  - Uso de Disco
  - Velocidad de Descarga/Subida
  - Temperatura del Sistema
  - Conexiones Activas
  - Procesos de XAMPP
  - Estado de Servicios Web

## Nuevas Funcionalidades
- Panel de control web con interfaz mejorada
- Botón de actualización manual de métricas
- Monitorización específica de procesos XAMPP
- Visualización mejorada de gráficas
- Interfaz responsive
- Sistema de autenticación
- Exportación de datos en múltiples formatos

## Requisitos

### Python
```bash
pip install matplotlib==3.7.1
pip install psutil==5.9.5
pip install requests==2.31.0
```

### PHP
- PHP 7.4 o superior
- Extensión PHP exec habilitada
- Permisos de escritura en las carpetas del proyecto

### Servidor Web
- XAMPP (Apache)
- Permisos de ejecución para scripts Python
- Python en el PATH del sistema

## Instalación

1. Clonar el repositorio en la carpeta htdocs de XAMPP:
```bash
git clone https://github.com/tuuser/pcpro-topaz.git
```

2. Instalar dependencias de Python:
```bash
cd pcpro-topaz
pip install -r requirements.txt
```

3. Configurar permisos:
```bash
chmod 755 topaz.py
chmod -R 777 img
```

## Configuración del Panel Web

### Credenciales por defecto
```php
$VALID_USERNAME = 'franHR';
$VALID_PASSWORD = 'franHR';
```

### Procesos monitorizados (Windows)
```python
process_list = [
    'httpd.exe',
    'mysqld.exe',
    'php-cgi.exe',
    'php.exe',
    'apache.exe',
    'xampp-control.exe'
]
```

## Uso

### Acceso al Panel
1. Iniciar XAMPP
2. Abrir navegador: `http://localhost/pcpro-topaz`
3. Iniciar sesión con las credenciales

### Funciones Principales
- **Métricas por Hora**: Visualización de gráficas
- **Procesos Activos**: Monitoreo de procesos XAMPP
- **Servicios Web**: Estado de servicios HTTP
- **Alertas**: Sistema de notificaciones
- **Exportar Datos**: JSON y Base de datos
- **Actualización Manual**: Botón de actualización

## Solución de Problemas

### Error de Matplotlib
Si aparece el error "module not found":
```bash
pip install --upgrade matplotlib
python -m pip install --upgrade pip
```

### Error de Permisos
En Windows, ejecutar CMD como administrador:
```bash
python -m pip install --user matplotlib psutil requests
```

### Error de XAMPP
Verificar que los servicios estén activos:
- Apache
- MySQL
- PHP

## Estructura de Archivos
```
pcpro-topaz/
├── index.php           # Panel de control
├── topaz.py           # Script de monitorización
├── execute_script.php # Controlador de ejecución
├── requirements.txt   # Dependencias Python
├── img/              # Gráficas generadas
├── metrics.db        # Base de datos SQLite
└── README.md         # Documentación
```

## Licencia
Este proyecto está bajo licencia MIT.
