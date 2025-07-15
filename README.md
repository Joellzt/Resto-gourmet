# Resto Gourmet 🍽️

**Resto Gourmet** es una aplicación web desarrollada en PHP que permite la gestión de un restaurante, incluyendo vistas públicas e internas para la administración. Este proyecto utiliza XAMPP como entorno local de desarrollo.

## 🚀 Requisitos

- PHP 8.x o superior
- XAMPP instalado (incluye Apache y MySQL)
- Navegador web moderno

## 📦 Instalación y configuración

1. **Clonar o descargar este repositorio:**

   ```bash
   git clone https://github.com/Joellzt/Resto-gourmet.git
Mover el proyecto a htdocs:

Copiá la carpeta del proyecto dentro del directorio htdocs de XAMPP:

makefile
Copiar
Editar
C:\xampp\htdocs\Resto-gourmet
Iniciar Apache y MySQL:

Abrí el Panel de Control de XAMPP y asegurate de que los servicios Apache y MySQL estén corriendo.

Importar la base de datos:

Abrí phpMyAdmin

Creá una nueva base de datos (por ejemplo, resto_gourmet)

Importá el archivo resto_db.sql que se encuentra en el proyecto

Acceder a la aplicación:

Abrí tu navegador y entrá a:

arduino
Copiar
Editar
http://localhost/Resto-gourmet
📁 Estructura del proyecto
index.php: Página principal

assets/: Recursos como imágenes y estilos

includes/: Archivos comunes (header, footer, conexión a base de datos, etc.)

pages/: Páginas internas del sitio

resto_db.sql: Archivo para crear la base de datos

package.json: Información del entorno y dependencias (si aplica)

✅ Funcionalidades
Visualización del menú

Reserva de mesas

Panel administrativo (CRUD básico)

Diseño responsivo

📌 Notas
Este proyecto es para uso local. Si querés publicarlo en producción, deberías migrarlo a un entorno compatible (ej. hosting con soporte PHP y MySQL).

Podés modificar los datos de conexión en el archivo includes/db.php (o similar).

🤝 Autor
Joel Lorenzetti
📎 Repositorio GitHub
