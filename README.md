# StockFlow - Actividad 4
## Diseño y comunicación entre microservicios

## Descripción

**StockFlow** es un sistema web orientado a la gestión de inventario y ventas para ferreterías. Permite llevar el control de productos, clientes, inventario y ventas.

Para esta actividad se separó una parte del sistema en dos microservicios utilizando **PHP y CodeIgniter 3**:

- **Servicio A:** Gestión de clientes.
- **Servicio B:** Gestión de ventas.

Cada servicio tiene su propio código, configuración y base de datos. El Servicio B se comunica con el Servicio A para verificar que un cliente exista antes de registrar una venta.

La comunicación se realiza mediante peticiones HTTP utilizando **cURL** y **JSON**.

---

## Tecnologías utilizadas

- PHP
- CodeIgniter 3
- MySQL
- XAMPP
- Apache
- cURL
- JSON
- Postman

---

## Estructura del proyecto

```text
Actividad4_Yesid_Hernandez/
│
├── ServicioA/
├── ServicioB/
├── BaseDatos/
│   ├── db_microservicio_clientes.sql
│   └── db_microservicio_ventas.sql
├── Postman/
│   └── Actividad4_Microservicios.postman_collection.json
└── README.md
```

---

## Bases de datos

Cada microservicio utiliza una base de datos independiente.

### Servicio A - Clientes

```text
Base de datos: db_microservicio_clientes
Archivo: BaseDatos/db_microservicio_clientes.sql
```

Esta base almacena la información de los clientes.

### Servicio B - Ventas

```text
Base de datos: db_microservicio_ventas
Archivo: BaseDatos/db_microservicio_ventas.sql
```

Esta base almacena la información de las ventas.

No se comparte una misma base de datos entre los servicios. El Servicio B no consulta directamente las tablas del Servicio A, sino que utiliza su API para obtener la información necesaria.

---

## Instalación

### 1. Iniciar XAMPP

Iniciar los servicios:

```text
Apache
MySQL
```

### 2. Crear las bases de datos

Entrar a:

```text
http://localhost/phpmyadmin
```

Importar:

```text
BaseDatos/db_microservicio_clientes.sql
```

y:

```text
BaseDatos/db_microservicio_ventas.sql
```

Al finalizar deben existir:

```text
db_microservicio_clientes
db_microservicio_ventas
```

### 3. Configurar las conexiones

El Servicio A debe conectarse a:

```text
Host: localhost
Usuario: root
Contraseña: 
Base de datos: db_microservicio_clientes
```

El Servicio B debe conectarse a:

```text
Host: localhost
Usuario: root
Contraseña:
Base de datos: db_microservicio_ventas
```

Si el usuario `root` tiene contraseña en MySQL, se debe colocar en la configuración correspondiente.

### 4. Ubicación del proyecto

Colocar la carpeta del proyecto dentro de:

```text
C:\xampp\htdocs\
```

Con Apache y MySQL activos, los servicios quedan disponibles desde `localhost`.

---

## URLs

### Servicio A

```text
http://localhost/Actividad4_Yesid_Hernandez/ServicioA/
```

### Servicio B

```text
http://localhost/Actividad4_Yesid_Hernandez/ServicioB/
```

La ruta depende del nombre de la carpeta utilizada dentro de `htdocs`.

---

# Servicio A - Clientes

El Servicio A se encarga de administrar los clientes.

## Endpoints

```http
GET     /clientes
GET     /clientes/{id}
POST    /clientes
PUT     /clientes/{id}
DELETE  /clientes/{id}
```

### Crear cliente

```http
POST /clientes
```

Ejemplo:

```json
{
    "nombre": "Pedro Martinez",
    "correo": "pedro@gmail.com",
    "telefono": "3001112233",
    "direccion": "Apartado"
}
```

---

# Servicio B - Ventas

El Servicio B se encarga de administrar las ventas.

## Endpoints

```http
GET     /ventas
GET     /ventas/{id}
POST    /ventas/guardar
PUT     /ventas/{id}
DELETE  /ventas/{id}
```

### Registrar venta

```http
POST /ventas/guardar
```

Ejemplo:

```json
{
    "id_cliente": 2,
    "total": 150000
}
```

---

# Comunicación entre los servicios

Antes de registrar una venta, el Servicio B consulta al Servicio A para comprobar que el cliente exista.

El proceso es:

```text
Servicio B
    |
    | HTTP + cURL
    v
Servicio A
    |
    v
Base de datos de clientes
```

Si el cliente existe, el Servicio B continúa y registra la venta.

Si el cliente no existe, la venta es rechazada.

### Cliente existente

```json
{
    "id_cliente": 2,
    "total": 150000
}
```

Resultado:

```json
{
    "ok": true,
    "mensaje": "Venta registrada correctamente."
}
```

### Cliente inexistente

```json
{
    "id_cliente": 9999,
    "total": 150000
}
```

Resultado:

```json
{
    "ok": false,
    "mensaje": "El cliente no existe en el Servicio A."
}
```

---

# Manejo de errores

Se implementó un manejo básico de errores para controlar problemas durante la comunicación entre los servicios.

La petición mediante cURL utiliza tiempos de espera:

```php
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
```

Si el Servicio A no responde, el Servicio B controla la situación y no registra la venta.

Ejemplo:

```json
{
    "ok": false,
    "mensaje": "No se pudo comunicar con el Servicio A."
}
```

---

# Pruebas con Postman

La colección utilizada para las pruebas se encuentra en:

```text
Postman/Actividad4_Microservicios.postman_collection.json
```

Para probar el proyecto:

1. Iniciar Apache y MySQL.
2. Verificar que las dos bases de datos estén creadas.
3. Abrir Postman.
4. Importar la colección.
5. Ejecutar las peticiones del Servicio A y Servicio B.

Se deben comprobar principalmente los siguientes casos:

### 1. Cliente existente

Crear o utilizar un cliente existente y registrar una venta con su `id_cliente`.

La venta debe registrarse correctamente.

### 2. Cliente inexistente

Enviar una venta utilizando un `id_cliente` que no exista.

La venta debe ser rechazada y mostrar:

```json
{
    "ok": false,
    "mensaje": "El cliente no existe en el Servicio A."
}
```

### 3. Servicio A no disponible

Detener temporalmente el Servicio A e intentar registrar una venta desde el Servicio B.

El Servicio B debe controlar el error y evitar registrar la venta.

---

# Configuración

### Servicio A

```text
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=
DB_NAME=db_microservicio_clientes
```

### Servicio B

```text
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=
DB_NAME=db_microservicio_ventas
```

El Servicio B también utiliza la URL del Servicio A para realizar la validación del cliente.

```text
SERVICIO_A_URL=http://localhost/Actividad4_Yesid_Hernandez/ServicioA
```

---

# Resultado

Se implementaron dos microservicios independientes, cada uno con su propia base de datos y sus respectivos endpoints.

El Servicio B se comunica con el Servicio A mediante HTTP y cURL para validar los clientes antes de registrar una venta.

También se implementó manejo de errores cuando el cliente no existe o cuando el Servicio A no está disponible.

Las operaciones y la comunicación entre los servicios se pueden comprobar mediante la colección de Postman incluida en el proyecto.