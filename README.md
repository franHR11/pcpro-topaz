# 🔥 Sistema de Monitorización PCPRO-TOPAZ

<div align="center">
  <img src="assent/topaz1.png" alt="PCPRO-TOPAZ Logo" width="200"/>
  <br><br>
  <strong>🚀 Sistema Avanzado de Monitorización de Servidores en Tiempo Real</strong>
  <br><br>
  <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/Python-3776AB?style=for-the-badge&logo=python&logoColor=white" alt="Python">
  <img src="https://img.shields.io/badge/SQLite-07405E?style=for-the-badge&logo=sqlite&logoColor=white" alt="SQLite">
  <img src="https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black" alt="JavaScript">
</div>

---

## 📋 Descripción del Proyecto

**PCPRO-TOPAZ** es un sistema profesional de monitorización de servidores desarrollado para empresas que necesitan supervisar el rendimiento de sus sistemas en tiempo real. Esta solución integral permite monitorear métricas críticas del sistema, generar gráficas automáticas y recibir alertas cuando los recursos alcanzan niveles críticos.

### 🎯 ¿Para quién está diseñado?
- **Administradores de sistemas** que necesitan supervisión continua
- **Empresas de hosting** que gestionan múltiples servidores
- **Desarrolladores** que requieren monitoreo de aplicaciones web
- **Equipos de DevOps** que buscan optimizar el rendimiento

---

## ✨ Características Destacadas

### 🛠️ **Monitorización Integral**
- 📊 **CPU, RAM y Disco**: Monitoreo en tiempo real de recursos del sistema
- 🌡️ **Temperatura**: Control térmico del hardware
- 🌐 **Red**: Medición de velocidad de descarga y subida
- 🔗 **Conexiones**: Seguimiento de conexiones activas
- 🔍 **Procesos**: Monitoreo de servicios críticos (Apache, MySQL, PHP)
- 🌍 **Servicios Web**: Verificación de disponibilidad de URLs

### 📈 **Visualización Avanzada**
- 📊 Gráficas automáticas generadas con Matplotlib
- 🕐 Datos históricos por horas
- 🖼️ Interfaz web responsive y moderna
- 🔍 Zoom en gráficas con modal interactivo

### 🔐 **Seguridad y Control**
- 🔒 Sistema de autenticación seguro
- 👤 Panel de administración protegido
- 🚪 Gestión de sesiones
- 📱 Interfaz responsive para móviles

### 🚨 **Sistema de Alertas**
- ⚠️ Alertas automáticas por umbrales críticos
- 📧 Notificaciones por email (configurables)
- 🔔 Alertas visuales en el dashboard

---

## ⚙️ Funcionalidades

### 🖥️ **Dashboard Web**
- **Panel principal** con métricas en tiempo real
- **Visualización de gráficas** organizadas por categorías
- **Actualización manual** de métricas con un clic
- **Descarga de datos** en formato JSON y base de datos SQLite
- **Sistema de login** con credenciales seguras

### 🐍 **Motor Python**
- **Recolección automática** de métricas del sistema
- **Generación de gráficas** en formato JPG
- **Almacenamiento en SQLite** para persistencia de datos
- **Exportación JSON** para integración con otros sistemas
- **Monitoreo de procesos** específicos del servidor web

### 🔄 **Automatización**
- **Script de ejecución** integrado en PHP
- **Verificación automática** de dependencias Python
- **Instalación automática** de módulos faltantes
- **Manejo de errores** robusto

---

## 🔧 Tecnologías Utilizadas

### 🌐 **Frontend**
- 🟨 **JavaScript** - Interactividad y AJAX
- 🎨 **CSS3** - Diseño responsive y moderno
- 📱 **HTML5** - Estructura semántica
- 🖼️ **Google Fonts** - Tipografía Ubuntu

### ⚙️ **Backend**
- 🐘 **PHP** - Lógica del servidor y autenticación
- 🐍 **Python 3** - Motor de monitorización
- 🗄️ **SQLite** - Base de datos embebida
- 📊 **Matplotlib** - Generación de gráficas

### 📦 **Librerías Python**
- `psutil` - Métricas del sistema
- `matplotlib` - Visualización de datos
- `requests` - Verificación de servicios web
- `sqlite3` - Gestión de base de datos
- `smtplib` - Envío de alertas por email

---

## 📁 Estructura del Proyecto

```
pcpro-topaz/
├── 📄 index.php              # Dashboard principal y sistema de login
├── 🔧 execute_script.php     # Ejecutor del script Python desde web
├── 🐍 topaz.py              # Motor principal de monitorización
├── 🔄 manual.py             # Script para ejecución manual continua
├── 📋 requirements.txt      # Dependencias Python
├── 🗄️ metrics.db            # Base de datos SQLite con métricas
├── 📊 metrics_export.json   # Exportación de datos en JSON
├── 🖼️ assent/               # Recursos gráficos
│   ├── topaz.png
│   └── topaz1.png
└── 📈 img/                   # Gráficas generadas
    └── hourly/               # Gráficas por horas
        ├── uso_de_cpu_hourly.jpg
        ├── uso_de_ram_hourly.jpg
        ├── uso_de_disco_hourly.jpg
        ├── descarga_hourly.jpg
        ├── subida_hourly.jpg
        ├── temperatura_hourly.jpg
        └── conexiones_activas_hourly.jpg
```

---

## 🚀 Instrucciones de Uso

### 📋 **Requisitos Previos**
- 🐘 PHP 7.4 o superior
- 🐍 Python 3.7 o superior
- 📦 pip (gestor de paquetes Python)
- 🌐 Servidor web (Apache, Nginx, XAMPP, Laragon)

### 🔧 **Instalación**

1. **Clonar o descargar el proyecto**
   ```bash
   git clone https://github.com/tu-usuario/pcpro-topaz.git
   cd pcpro-topaz
   ```

2. **Instalar dependencias Python**
   ```bash
   pip install -r requirements.txt
   ```
   
   O instalar manualmente:
   ```bash
   pip install matplotlib psutil requests
   ```

3. **Configurar servidor web**
   - Colocar el proyecto en la carpeta del servidor web
   - Asegurar que PHP tenga permisos de escritura en la carpeta `img/`

4. **Configurar credenciales** (opcional)
   - Editar `index.php` líneas 5-6:
   ```php
   $VALID_USERNAME = 'tu_usuario';
   $VALID_PASSWORD = 'tu_contraseña';
   ```

### 🎯 **Uso del Sistema**

1. **Acceder al dashboard**
   - Abrir navegador en `http://localhost/pcpro-topaz/`
   - Credenciales por defecto: `franHR` / `franHR`

2. **Actualizar métricas**
   - Hacer clic en "Actualizar Métricas" en el dashboard
   - Las gráficas se generarán automáticamente

3. **Ejecución automática** (opcional)
   ```bash
   python manual.py
   ```

4. **Ejecución manual del motor**
   ```bash
   python topaz.py
   ```

### ⚙️ **Configuración de Alertas**

Editar umbrales en `topaz.py`:
```python
ALERT_THRESHOLDS = {
    'cpu': 90,   # Alerta si CPU > 90%
    'ram': 90,   # Alerta si RAM > 90%
    'disk': 90,  # Alerta si Disco > 90%
}
```

---

## 🧪 Ejemplos de Uso

### 📊 **Métricas Disponibles**
```json
{
  "timestamp": "2025-02-05T00:23:35.673666",
  "metrics": {
    "cpu_percent": 3.5,
    "ram_percent": 44.1,
    "disk_percent": 15.2,
    "download_mbps": 0.063,
    "upload_mbps": 0.005,
    "temperature": 0,
    "connections": 143
  }
}
```

### 🔍 **Procesos Monitoreados**
- `httpd.exe` - Servidor Apache
- `mysqld.exe` - Base de datos MySQL
- `php-cgi.exe` - Procesador PHP
- `xampp-control.exe` - Panel de control XAMPP

### 🌐 **Servicios Web Verificados**
- `http://localhost` - Servidor principal
- `http://localhost:8080` - Servicios adicionales

---

## 📞 Soporte y Contacto

### 🆘 Obtener Ayuda

Si necesitas soporte técnico, personalización o tienes alguna consulta sobre el sistema:

📅 **Año**: 2025  
📨 **Autor**: Francisco José Herreros (franHR)  
📧 **Email**: [desarrollo@pcprogramacion.es](mailto:desarrollo@pcprogramacion.es)  
🌐 **Web**: [https://www.pcprogramacion.es](https://www.pcprogramacion.es)  
💼 **LinkedIn**: [Francisco José Herreros](https://linkedin.com/in/francisco-jose-herreros)  

---

## 🖼️ Capturas del Proyecto

<div align="center">
  <img src="assent/topaz.png" alt="Dashboard PCPRO-TOPAZ" width="600"/>
  <br>
  <em>Dashboard principal con métricas en tiempo real</em>
</div>

---

## 🛡️ Licencia

### 🇪🇸 Español

**Copyright (c) 2025 Francisco José Herreros (franHR) / PCProgramación**

Todos los derechos reservados.

Este software es propiedad de Francisco José Herreros (franHR), desarrollador de PCProgramación ([https://www.pcprogramacion.es](https://www.pcprogramacion.es)). No está permitido copiar, modificar, distribuir o utilizar este código, ni total ni parcialmente, sin una autorización expresa y por escrito del autor.

El acceso a este repositorio tiene únicamente fines de revisión, auditoría o demostración, y no implica la cesión de ningún derecho de uso o explotación.

Para solicitar una licencia o permiso de uso, contacta con: [desarrollo@pcprogramacion.es](mailto:desarrollo@pcprogramacion.es)

### 🇺🇸 English

**Copyright (c) 2025 Francisco José Herreros (franHR) / PCProgramación**

All rights reserved.

This software is the property of Francisco José Herreros (franHR), developer of PCProgramación ([https://www.pcprogramacion.es](https://www.pcprogramacion.es)). It is not allowed to copy, modify, distribute or use this code, either totally or partially, without express and written authorization from the author.

Access to this repository has only review, audit or demonstration purposes, and does not imply the transfer of any right of use or exploitation.

To request a license or permission to use, contact: [desarrollo@pcprogramacion.es](mailto:desarrollo@pcprogramacion.es)

---

## 🔝 Hashtags Recomendados para LinkedIn

```
#MonitorizacionServidores #PHP #Python #DevOps #SistemaMonitoreo #WebDevelopment 
#ServerMonitoring #Dashboard #RealTimeMetrics #PCProgramacion #TechSolutions 
#SystemAdministration #WebDashboard #ServerHealth #ITSolutions #Monitoring
```

---

<div align="center">
  <strong>🚀 ¿Te gusta este proyecto? ¡Dale una estrella ⭐ y compártelo!</strong>
  <br><br>
  <em>Desarrollado con ❤️ por Francisco José Herreros (franHR) - PCProgramación</em>
</div>