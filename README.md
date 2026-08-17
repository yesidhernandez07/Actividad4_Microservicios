# Actividad 4 - Diseño y comunicación entre microservicios

## 1. Descripción del proyecto

En esta actividad se desarrollaron dos microservicios utilizando PHP y CodeIgniter 3. El objetivo principal es mostrar cómo dos servicios pueden funcionar de manera independiente y comunicarse entre sí cuando necesitan información de otro servicio.

Los microservicios desarrollados son:

* **Servicio A:** gestión de clientes.
* **Servicio B:** gestión de ventas.

El Servicio B consulta al Servicio A para verificar que el cliente exista antes de registrar una venta.

---

## 2. Tecnologías utilizadas

Para el desarrollo del proyecto se utilizaron las siguientes tecnologías:

* PHP 8.2
* CodeIgniter 3
* MySQL/MariaDB
* XAMPP
* Apache
* Postman
* cURL
* JSON

---

## 3. Organización del proyecto

Los dos servicios se encuentran separados para mantener su independencia:

```text
Actividad4_Microservicios/
│
├── servicio-a-clientes/
│
├── servicio-b-ventas/
│
├── README.md
│
└── Postman/
```

Cada servicio tiene sus propios archivos, controladores, modelos y configuración.

---

## 4. Bases de datos

Cada microservicio utiliza una base de datos diferente, siguiendo el principio de **Database per Service**.

Para el Servicio A se utiliza:

```text
db_microservicio_clientes
```

Esta base de datos contiene la información relacionada con los clientes.

Para el Servicio B se utiliza:

```text
db_microservicio_ventas
```

Esta base de datos contiene la información relacionada con las ventas.

Las dos bases de datos son independientes y no se comparten entre los servicios.

---

## 5. Requisitos para ejecutar el proyecto

Para ejecutar el proyecto localmente se necesita tener instalado:

* XAMPP
* PHP 8.2 o una versión compatible
* MySQL o MariaDB
* Postman

También es necesario que la extensión cURL de PHP esté habilitada, ya que se utiliza para la comunicación entre el Servicio B y el Servicio A.

---

## 6. Instalación

Primero se debe copiar la carpeta del proyecto dentro de la carpeta `htdocs` de XAMPP:

```text
C:\xampp\htdocs\Actividad4_Microservicios
```

Después se deben iniciar desde XAMPP los servicios de:

* Apache
* MySQL

En phpMyAdmin se deben crear las siguientes bases de datos:

```text
db_microservicio_clientes
db_microservicio_ventas
```

Luego se deben importar las tablas correspondientes a cada servicio.

---

## 7. Servicio A - Clientes

El Servicio A se encarga de realizar las operaciones relacionadas con los clientes.

URL base:

```text
http://localhost/Actividad4_Microservicios/servicio-a-clientes/
```

Endpoint principal:

```text
http://localhost/Actividad4_Microservicios/servicio-a-clientes/index.php/api/clientes
```

### Operaciones disponibles

**Crear cliente**

```text
POST /api/clientes
```

**Consultar todos los clientes**

```text
GET /api/clientes
```

**Consultar un cliente**

```text
GET /api/clientes/{id}
```

**Actualizar un cliente**

```text
PUT /api/clientes/{id}
```

**Eliminar un cliente**

```text
DELETE /api/clientes/{id}
```

---

## 8. Servicio B - Ventas

El Servicio B se encarga de registrar y administrar las ventas.

URL base:

```text
http://localhost/Actividad4_Microservicios/servicio-b-ventas/
```

Endpoint principal:

```text
http://localhost/Actividad4_Microservicios/servicio-b-ventas/index.php/api/ventas
```

### Operaciones disponibles

**Crear venta**

```text
POST /api/ventas
```

**Consultar todas las ventas**

```text
GET /api/ventas
```

**Consultar una venta**

```text
GET /api/ventas/{id}
```

**Actualizar una venta**

```text
PUT /api/ventas/{id}
```

**Eliminar una venta**

```text
DELETE /api/ventas/{id}
```

---

## 9. Comunicación entre los servicios

La comunicación se realiza desde el Servicio B hacia el Servicio A mediante una petición HTTP.

Cuando se intenta crear una venta, el Servicio B primero consulta al Servicio A utilizando el ID del cliente.

Por ejemplo:

```text
Servicio B
    ↓
Consulta al Servicio A
    ↓
¿Existe el cliente?
    ↓
 ┌───────────────┐
 │               │
Sí              No
 │               │
 ↓               ↓
Crear venta    Rechazar venta
```

Esto permite evitar que se registre una venta con un cliente que no existe.

---

## 10. Validación de clientes

Cuando se registra una venta con un cliente existente, el Servicio B permite continuar con la operación.

En las pruebas realizadas, utilizando el cliente con `id_cliente = 2`, la venta fue creada correctamente.

Cuando se utilizó un cliente inexistente, por ejemplo `id_cliente = 9999`, el Servicio B respondió:

```json
{
    "ok": false,
    "mensaje": "El cliente no existe en el Servicio A."
}
```

De esta forma se comprueba que el Servicio B está validando la información del Servicio A antes de registrar una venta.

---

## 11. Manejo de errores y resiliencia

También se implementó un manejo básico de errores para cuando el Servicio A no se encuentre disponible.

Para la comunicación mediante cURL se configuró un tiempo máximo de conexión de 2 segundos y un tiempo máximo de espera de 5 segundos.

```php
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
```

Si el Servicio A no responde, el Servicio B no registra la venta y devuelve una respuesta controlada:

```json
{
    "ok": false,
    "mensaje": "El Servicio A no está disponible. No se puede validar el cliente."
}
```

La respuesta utiliza el código HTTP:

```text
503 Service Unavailable
```

Esto permite manejar de una mejor manera la caída o indisponibilidad del servicio dependiente.

---

## 12. Pruebas realizadas

Las pruebas principales se realizaron utilizando Postman.

En el Servicio B se probaron las siguientes operaciones:

* Crear una venta.
* Consultar todas las ventas.
* Consultar una venta específica.
* Actualizar una venta.
* Comprobar que la actualización se realizó.
* Eliminar una venta.
* Comprobar que la venta fue eliminada.

También se realizaron pruebas de comunicación entre los servicios:

1. Se registró una venta utilizando un cliente existente.
2. Se intentó registrar una venta utilizando un cliente inexistente.
3. Se simuló la indisponibilidad del Servicio A.
4. Se comprobó que el Servicio B respondiera con un error controlado.

---

## 13. Colección de Postman

Se incluye una colección de Postman para realizar las pruebas de los dos microservicios.

La colección contiene las peticiones necesarias para comprobar:

* CRUD del Servicio A.
* CRUD del Servicio B.
* Comunicación entre ambos servicios.
* Validación de clientes.
* Manejo de errores cuando el Servicio A no está disponible.

El archivo se encuentra en la carpeta:

```text
Postman/
```

---

## 14. Conclusión

Con el desarrollo de esta actividad se implementaron dos microservicios independientes utilizando CodeIgniter 3.

Cada servicio tiene su propia base de datos y se puede ejecutar de manera independiente. Además, se logró establecer una comunicación entre el Servicio B y el Servicio A para validar los clientes antes de registrar las ventas.

Finalmente, se agregó un timeout y un manejo de errores para controlar la situación en la que el Servicio A no esté disponible. Las diferentes pruebas realizadas en Postman permitieron comprobar el funcionamiento de los servicios y la comunicación entre ellos.
