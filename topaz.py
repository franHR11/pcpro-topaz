import matplotlib.pyplot as plt
import psutil
import time
import subprocess
import os
import smtplib
import sqlite3
import json
import requests
from datetime import datetime, timedelta
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart

# Obtener el directorio base del script actual
base_dir = os.path.dirname(os.path.abspath(__file__))

# Path for data files
data_paths = {
    "hourly": os.path.join(base_dir, "carga_hourly.txt"),
}

# Path for plot folders
plot_folders = {
    "hourly": os.path.join(base_dir, "img/hourly"),
}

# Create the plot folders if they don't exist
for folder in plot_folders.values():
    os.makedirs(folder, exist_ok=True)

# Function to trim data based on a time window
def trim_data(data, time_window_seconds):
    now = datetime.now()
    return [entry for entry in data if (now - entry[0]).total_seconds() <= time_window_seconds]

# Load existing data
def load_data(file_path):
    try:
        with open(file_path, 'r') as f:
            data = []
            for line in f:
                if line.strip():  # Ignorar líneas vacías
                    try:
                        row = line.strip().split(',')
                        if len(row) > 1:  # Asegurarse de que hay suficientes datos
                            timestamp = datetime.fromisoformat(row[0])
                            # Solo tomar los primeros 8 valores numéricos
                            # (timestamp, cpu, ram, disk, download, upload, temp, connections)
                            values = []
                            for val in row[1:8]:  # Limitar a los primeros 7 valores después del timestamp
                                try:
                                    values.append(float(val))
                                except ValueError:
                                    values.append(0.0)  # Valor por defecto si hay error
                            if values:  # Solo añadir si hay valores válidos
                                data.append((timestamp, *values))
                    except (ValueError, IndexError) as e:
                        print(f"Error procesando línea: {line.strip()}")
                        print(f"Error: {e}")
                        continue
            return data
    except FileNotFoundError:
        # Crear el archivo si no existe
        with open(file_path, 'w') as f:
            pass
        return []
    except Exception as e:
        print(f"Error leyendo archivo {file_path}: {e}")
        return []

# Save data to file
def save_data(file_path, data):
    with open(file_path, 'w') as f:
        for row in data:
            # Solo guardar los primeros 8 valores (timestamp + 7 métricas)
            values_to_save = list(row[:8])  # Timestamp + primeras 7 métricas
            f.write(','.join(map(str, values_to_save)) + '\n')

# Configuración de alertas
ALERT_THRESHOLDS = {
    'cpu': 90,  # Alerta si CPU > 90%
    'ram': 90,  # Alerta si RAM > 90%
    'disk': 90,  # Alerta si Disco > 90%
}

# Función para enviar alertas por email
def send_alert(subject, message):
    smtp_server = "smtp.gmail.com"
    smtp_port = 587
    sender_email = "your_email@gmail.com"
    receiver_email = "admin@example.com"
    password = "your_app_password"

    msg = MIMEMultipart()
    msg['From'] = sender_email
    msg['To'] = receiver_email
    msg['Subject'] = subject
    msg.attach(MIMEText(message, 'plain'))

    try:
        server = smtplib.SMTP(smtp_server, smtp_port)
        server.starttls()
        server.login(sender_email, password)
        server.send_message(msg)
        print(f"Alerta enviada: {subject}")
    except Exception as e:
        print(f"Error al enviar alerta: {e}")
    finally:
        server.quit()

# Función para monitorizar procesos específicos
def monitor_processes(process_list=['httpd.exe', 'mysqld.exe', 'php-cgi.exe', 'php.exe', 'apache.exe', 'xampp-control.exe']):
    processes = []
    for proc in psutil.process_iter(['pid', 'name', 'cpu_percent', 'memory_percent']):
        try:
            # Hacer la comparación de nombres case-insensitive
            if any(p.lower() in proc.info['name'].lower() for p in process_list):
                # Actualizar CPU y memoria antes de guardar
                proc.cpu_percent()
                proc.memory_percent()
                time.sleep(0.1)  # Pequeña pausa para mediciones más precisas
                processes.append({
                    'pid': proc.pid,
                    'name': proc.info['name'],
                    'cpu_percent': round(proc.cpu_percent(), 2),
                    'memory_percent': round(proc.memory_percent(), 2)
                })
        except (psutil.NoSuchProcess, psutil.AccessDenied, psutil.ZombieProcess):
            continue
    return processes

# Función para verificar servicios web
def check_web_services(urls=['http://localhost', 'http://localhost:8080']):
    results = []
    for url in urls:
        try:
            response = requests.get(url, timeout=5)
            results.append({
                'url': url,
                'status': response.status_code,
                'response_time': response.elapsed.total_seconds()
            })
        except requests.RequestException as e:
            results.append({
                'url': url,
                'status': 'error',
                'error': str(e)
            })
    return results

# Función para guardar datos en SQLite
def save_to_database(metrics):
    db_path = os.path.join(base_dir, 'metrics.db')
    conn = sqlite3.connect(db_path)
    c = conn.cursor()
    
    # Crear tabla si no existe
    c.execute('''CREATE TABLE IF NOT EXISTS metrics
                 (timestamp TEXT, cpu REAL, ram REAL, disk REAL, 
                  download REAL, upload REAL, temp REAL, connections INTEGER)''')
    
    # Insertar datos
    c.execute('''INSERT INTO metrics VALUES (?, ?, ?, ?, ?, ?, ?, ?)''', metrics)
    conn.commit()
    conn.close()

# Measure system metrics
def measure_metrics():
    carga_cpu = psutil.cpu_percent(interval=1)
    carga_ram = psutil.virtual_memory().percent
    uso_disco = psutil.disk_usage('/').percent
    data_inicio = psutil.net_io_counters()
    time.sleep(1)
    data_final = psutil.net_io_counters()
    descarga_mbps = (data_final.bytes_recv - data_inicio.bytes_recv) / (1024 * 1024)
    subida_mbps = (data_final.bytes_sent - data_inicio.bytes_sent) / (1024 * 1024)
    num_conexiones = len(psutil.net_connections())
    temperaturas = list(obtener_temperaturas())
    temperatura_promedio = sum(temperaturas) / len(temperaturas) if temperaturas else 0

    # Verificar umbrales y enviar alertas
    if carga_cpu > ALERT_THRESHOLDS['cpu']:
        send_alert("Alerta CPU", f"Uso de CPU alto: {carga_cpu}%")
    if carga_ram > ALERT_THRESHOLDS['ram']:
        send_alert("Alerta RAM", f"Uso de RAM alto: {carga_ram}%")
    if uso_disco > ALERT_THRESHOLDS['disk']:
        send_alert("Alerta Disco", f"Uso de Disco alto: {uso_disco}%")

    # Monitorizar procesos importantes
    process_info = monitor_processes()
    
    # Verificar servicios web
    web_services = check_web_services()
    
    # Guardar datos en base de datos
    metrics = (datetime.now().isoformat(), carga_cpu, carga_ram, uso_disco,
              descarga_mbps, subida_mbps, temperatura_promedio, num_conexiones)
    save_to_database(metrics)
    
    return (
        datetime.now(),
        carga_cpu,
        carga_ram,
        uso_disco,
        descarga_mbps,
        subida_mbps,
        temperatura_promedio,
        num_conexiones,
        process_info,
        web_services
    )

# Function to obtain CPU temperatures (requires lm-sensors)
def obtener_temperaturas():
    # Uncomment and implement if lm-sensors is available
    # try:
    #     sensores = subprocess.check_output(['sensors'], encoding='utf-8')
    #     for linea in sensores.splitlines():
    #         if 'Core' in linea:
    #             yield float(linea.split()[1].strip('+').strip('°C'))
    # except Exception as e:
    #     print(f"Error al obtener temperaturas: {e}")
    #     return []
    return []

# Load current data
data_buffers = {key: load_data(path) for key, path in data_paths.items()}

# Inicializar los datos si el archivo está vacío
if not any(data_buffers.values()):
    # Tomar una medida inicial
    initial_entry = measure_metrics()[:8]  # Solo tomar los primeros 8 valores (sin process_info y web_services)
    for key in data_buffers:
        data_buffers[key] = [initial_entry]
        save_data(data_paths[key], [initial_entry])

# Measure metrics
new_entry = measure_metrics()
# Solo tomar los primeros 8 valores para el archivo de datos
metrics_for_buffer = new_entry[:8]  # timestamp + 7 métricas

# Update data buffer
data_buffers["hourly"].append(metrics_for_buffer)

# Trim data to the last 1 hour (3600 seconds)
data_buffers["hourly"] = trim_data(data_buffers["hourly"], 3600)  # Last 1 hour

# Save updated data
for key, path in data_paths.items():
    save_data(path, data_buffers[key])

# Function to generate plots
def generate_plot(data, index, title, ylabel, save_path, ylim=None):
    if not data:
        print(f"No data available for {title}. Skipping plot.")
        return
    timestamps = [row[0].strftime('%Y-%m-%d %H:%M:%S') for row in data]  # Formatear la hora correctamente
    values = [row[index] for row in data]
    plt.figure(figsize=(10, 6))
    plt.plot(timestamps, values, label=title, marker='o')
    plt.grid(True)
    if ylim:
        plt.ylim(ylim)
    plt.title(title)
    plt.xlabel('Tiempo')
    plt.ylabel(ylabel)
    plt.xticks(rotation=45)  # Rotar las etiquetas del eje x para mejor legibilidad
    plt.legend()
    plt.tight_layout()
    plt.savefig(save_path)
    plt.close()

# Plot settings
plot_configs = [
    (1, 'Uso de CPU', 'Porcentaje de Uso', (0, 100)),
    (2, 'Uso de RAM', 'Porcentaje de Uso', (0, 100)),
    (3, 'Uso de Disco', 'Porcentaje de Uso', (0, 100)),
    (4, 'Descarga', 'Mbps', None),
    (5, 'Subida', 'Mbps', None),
    (6, 'Temperatura', 'Temperatura (°C)', None),
    (7, 'Conexiones Activas', 'Conexiones', None),
]

# Generate plots for hourly data
for index, title, ylabel, ylim in plot_configs:
    generate_plot(
        data_buffers["hourly"],
        index,
        f'{title} (Hourly)',
        ylabel,
        os.path.join(plot_folders["hourly"], f'{title.lower().replace(" ", "_")}_hourly.jpg'),
        ylim,
    )

# Exportar datos a JSON
def export_to_json(data, filename='metrics_export.json'):
    export_path = os.path.join(base_dir, filename)
    with open(export_path, 'w') as f:
        json.dump(data, f, indent=4, default=str)

# Generar reporte y exportar datos
if __name__ == "__main__":
    metrics_data = measure_metrics()
    export_to_json({
        'timestamp': datetime.now().isoformat(),
        'metrics': metrics_data,
        'processes': metrics_data[8],
        'web_services': metrics_data[9]
    })

print("Métricas actualizadas, gráficas generadas y datos exportados correctamente.")

