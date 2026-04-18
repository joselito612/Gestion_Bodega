# Módulo de Gestión de Bodegas

Aplicación web para administrar bodegas con asignación de encargado

##  Requisitos

- PHP 7.x
- PostgreSQL 12+
- Apache (XAMPP u otro servidor web)

---

##  Estructura del Proyecto

```
bodega/
├── config/
│   └── db.php           # Conexión a PostgreSQL usando PDO
├── database/
│   ├── dump.sql       # Definición de tablas y constraints + Datos de prueba (encargados)
├── models/
│   └── Bodega.php       # Modelo: consultas a la base de datos
├── controllers/
│   └── BodegaController.php  # Lógica de negocio y validaciones
├── views/bodega/
│   ├── index.php        # Listado de bodegas (CRUD - Read)
│   ├── create.php       # Formulario crear bodega (CRUD - Create)
│   └── edit.php         # Formulario editar bodega (CRUD - Update)
├── public/
│   └── index.php        # Entry point: ruteo de acciones
│   └── css/
│     └───styles.css
├── docs/
│   └── Bodega-db_schema.pdf  # Diagrama ER de la base de datos
├── .env.example         # Plantilla de configuración
└── README.md           # Este archivo
```

---

##  Instalación

### 1. Base de datos

Crear la base de datos y ejecutar los scripts SQL:

```sql
-- Crear base de datos
psql -U postgres -c "CREATE DATABASE sys_bodega;"

-- Ejecutar schema (tablas y constraints)
psql -U postgres -d sys_bodega -f database/dump.sql


```

### 2. Configuración

Copiar el archivo de entorno y editar con tus credenciales:

```bash
cp .env.example .env
```

Editar `.env`:

```
DB_HOST=localhost
DB_PORT=5432
DB_NAME=sys_bodega
DB_USER=postgres
DB_PASS=tu_password_aqui
```

### 3. Servidor web

- **XAMPP**: Copiar proyecto a `C:\xampp\htdocs\bodega`
- **Apache directo**: Configurar VirtualHost apuntando a `public/`

### 4. Ejecutar

Acceder a: `http://localhost/bodega/public/`

---

##  Modelo de Datos

### Tabla: bodega
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | SERIAL | Primary Key |
| codigo | VARCHAR(5) | Código único (formato: B + 1 a 4 caracteres alfanuméricos, ej: B1, B12, B123) |
| nombre | VARCHAR(100) | Nombre de la bodega |
| direccion | VARCHAR(255) | Ubicación (solo: Estación Central, Pudahuel, Las Condes) |
| dotacion | INTEGER | Número de personas |
| estado | VARCHAR(20) | 'Activada' o 'Desactivada' |
| fecha_creacion | TIMESTAMP | Fecha y hora de creación |

### Tabla: encargado
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | SERIAL | Primary Key |
| run | VARCHAR(20) | RUN único del encargado |
| nombre | VARCHAR(50) | Primer nombre |
| apellido1 | VARCHAR(50) | Apellido paterno |
| apellido2 | VARCHAR(50) | Apellido materno (opcional) |
| direccion | VARCHAR(255) | Dirección particular |
| telefono | VARCHAR(20) | Teléfono de contacto |

### Tabla: bodega_encargado (relación N:M)
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | SERIAL | Primary Key |
| bodega_id | INTEGER | FK a bodega |
| encargado_id | INTEGER | FK a encargado |
| fecha_asignacion | TIMESTAMP | Fecha de asignación |
| estado | VARCHAR(20) | 'Activo' o 'Desactivado' |

---

##  Reglas de Negocio

### Código de Bodega
- **Obligatorio**: Debe comenzar con letra mayúscula "B"
- **Longitud**: Mínimo 2 caracteres, máximo 5 (B + 1 a 4 caracteres adicionales)
- **Formato**: Solo letras y números (alfanumérico)
- **Ejemplos válidos**: B1, B12, B123, B001, B9A2, BABC1
- **Ejemplos inválidos**: B (solo la letra), b123, B-123, B12345, A123
- **Validación**: 3 capas (Frontend JS, Backend PHP, Base de datos CHECK)

### Nombre
- Máximo 100 caracteres
- Alfanumérico

### Dirección
- Solo se permiten 3 opciones (dropdown):
  - Estación Central
  - Pudahuel
  - Las Condes

### Dotación
- Número entero positivo (mínimo 1)

### Estado
- Al crear: siempre "Activada"
- Al editar: puede cambiarse entre "Activada" y "Desactivada"

---

##  Funcionalidades del Sistema

### 1. Listar Bodegas (index.php)
- Muestra todas las bodegas en una tabla
- Columnas: Código, Nombre, Dirección, Dotación, Encargados, Estado, Fecha/Hora Creación, Acciones
- **Filtro**: Por estado (Todas, Activadas, Desactivadas)
- **Acciones**: Editar, Desactivar (si está activada) o Eliminar (si está desactivada)

### 2. Crear Bodega (create.php)
- Formulario con validaciones en tiempo real (JS)
- Campos:
  - Código (con prefijo "B" automático en validación)
  - Nombre
  - Dirección (dropdown)
  - Dotación
  - Encargados (checkbox múltiple)
- **Validación de código duplicado**: Muestra modal de error si el código ya existe

### 3. Editar Bodega (edit.php)
- Formulario para modificar datos existentes
- El código no es editable (readonly)
- Permite cambiar: Nombre, Dirección, Dotación, Estado, Encargados

### 4. Desactivar Bodega
- Soft delete: cambia estado de "Activada" a "Desactivada"
- No elimina datos de la base de datos
- La bodega queda visible en el listado pero inactiva

### 5. Eliminar Bodega
- Hard delete: elimina permanentemente de la base de datos
- **Solo disponible** para bodegas en estado "Desactivada"
- Elimina primero los registros de la tabla intermedia `bodega_encargado`
- Luego elimina la bodega

---

##  Validaciones

### Frontend (JavaScript)
- Código: regex `^B[a-zA-Z0-9]+$` (mínimo 2, máximo 5 caracteres)
- Nombre: máximo 100 caracteres
- Dirección: debe seleccionar una opción válida
- Dotación: número positivo
- Mensajes de alerta antes de enviar formulario

### Backend (PHP)
- Código: regex `^B[a-zA-Z0-9]+$` (mínimo 2, máximo 5 caracteres)
- Mismas validaciones que frontend
- Sanitización de datos (trim, strtoupper para código)
- Verificación de código duplicado antes de insertar
- Manejo de excepciones con transacciones para hard delete

### Base de Datos (PostgreSQL)
- CHECK constraint en código: `^B[a-zA-Z0-9]{1,4}$`
- UNIQUE en código de bodega
- UNIQUE en RUN de encargado
- FOREIGN KEYs para integridad referencial

---

##  Flujo de Usuario

```
1. Usuario entra a index.php
   └── Ve listado de todas lasbodegas

2. Usuario hace clic en "Nueva Bodega"
   └── Llena el formulario
   └── JS valida en tiempo real
   └── Submit → PHP valida y guarda
   └── Si código ya existe → muestra modal de error
   └── Si todo OK → redirect a index con mensaje de éxito

3. Usuario hace clic en "Editar"
   └── Se cargan los datos actuales en el formulario
   └── Puede modificar todos los campos excepto código

4. Usuario hace clic en "Desactivar"
   └── Confirma la acción
   └── Estado cambia a "Desactivada"
   └── Botón cambia a "Eliminar" (hard delete)

5. Usuario hace clic en "Eliminar" (solo si está desactivada)
   └── Confirma que desea eliminar permanentemente
   └── Se borra la bodega y sus relaciones
```

---

##  Excepciones y Errores

| Escenario | Comportamiento |
|-----------|----------------|
| Código duplicado | Modal rojo/negro: "Código de Bodega ya existe. Por favor agregar otro" |
| Validación fallida | Muestra errores en alerta roja arriba del formulario |
| Conexión a DB fallida | Die con mensaje de error PDO |
| Hard delete falla | Rollback de transacción, muestra error |

---

##  Tecnologías Usadas

- **PHP 7.x** (sin frameworks)
- **PostgreSQL** (base de datos relacional)
- **PDO** (conexión a base de datos)
- **HTML5** (estructura)
- **CSS3** (estilos embebidos)
- **JavaScript** (validaciones frontend)

---

##  Notas Importantes

- No se usa ningún framework (Laravel, Symfony, etc.)
- No se usan ORMs
- Código limpio y estructurado (Modelo-Vista-Controlador simple)
- Validaciones en las 3 capas (defensa en profundidad)
- Relaciones N:M implementadas correctamente con tabla intermedia
- Soft delete para desactivar, hard delete para eliminar permanentemente

---

##  Tests Unitarios

### Estructura

```
tests/
├── TestCase.php        # Clase base con métodos de aserción
└── ValidacionTest.php  # Tests de validaciones del sistema
```

### Ejecución

```bash
php tests/ValidacionTest.php
```

### Cobertura de Tests

| Categoría | Descripción | Tests |
|-----------|-------------|-------|
| Código válido | B1, B12, B123, B1234, B1A2, BCDEF | 6 |
| Código inválido | B, b123, B-123, B12345, A123, vacío | 7 |
| Nombre válido | Normal, 1 letra, 100 caracteres | 3 |
| Nombre inválido | Vacío, 101 caracteres | 2 |
| Dirección válida | Estación Central, Pudahuel, Las Condes | 3 |
| Dirección inválida | Vacío, no en lista, case sensitive | 3 |
| Dotación válida | 1, 10, entero | 3 |
| Dotación inválida | Vacío, 0, negativo, texto | 4 |
| Estado válido | Activada, Desactivada | 2 |
| Estado inválido | Vacío, Inactivo, Activo, otro | 4 |

**Total: 37 tests**

### Resultados

Todos los tests pasaron exitosamente, verificando que las validaciones del sistema funcionan correctamente:
- Código: formato B + 1-4 caracteres alfanuméricos
- Nombre: máximo 100 caracteres
- Dirección: solo opciones válidas del dropdown
- Dotación: número entero positivo
- Estado: solo "Activada" o "Desactivada"
