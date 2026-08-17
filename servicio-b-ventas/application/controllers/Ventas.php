<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ventas extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Venta_model');
        $this->output->set_content_type('application/json');
    }

    /**
     * Valida si el cliente existe en el Servicio A.
     *
     * Retorna:
     * true  = cliente existe
     * false = cliente no existe
     * null  = Servicio A no está disponible
     */
    private function clienteExiste($id_cliente)
    {
$url = 'http://localhost/Actividad4_Microservicios/servicio-a-clientes/index.php/api/clientes/' . $id_cliente;

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Tiempo máximo para establecer conexión
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);

        // Tiempo máximo total de la petición
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);

        $respuesta = curl_exec($ch);

        // Guardamos información de la petición
        $codigoHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errorCurl = curl_errno($ch);

        curl_close($ch);

        // El Servicio A no está disponible
        if ($respuesta === false || $errorCurl !== 0) {
            return null;
        }

        // El Servicio A respondió correctamente
        if ($codigoHttp === 200) {
            $datos = json_decode($respuesta, true);

            if (isset($datos['ok']) && $datos['ok'] === true) {
                return true;
            }

            return false;
        }

        // El Servicio A respondió que el cliente no existe
        if ($codigoHttp === 404) {
            return false;
        }

        // Cualquier otro error del Servicio A
        return null;
    }

    public function index()
    {
        $ventas = $this->Venta_model->obtenerTodas();

        $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'ok' => true,
                'data' => $ventas
            ]));
    }

    public function ver($id)
    {
        $venta = $this->Venta_model->obtenerPorId($id);

        if (!$venta) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    'ok' => false,
                    'mensaje' => 'Venta no encontrada.'
                ]));
        }

        $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'ok' => true,
                'data' => $venta
            ]));
    }

    public function crear()
    {
        $datos = json_decode($this->input->raw_input_stream, true);

        if (
            empty($datos['id_cliente']) ||
            empty($datos['producto']) ||
            empty($datos['cantidad']) ||
            empty($datos['precio'])
        ) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'ok' => false,
                    'mensaje' => 'Todos los campos de la venta son obligatorios.'
                ]));
        }

        // Validar que el cliente exista en el Servicio A
        $cliente = $this->clienteExiste($datos['id_cliente']);

        // Servicio A no disponible
        if ($cliente === null) {
            return $this->output
                ->set_status_header(503)
                ->set_output(json_encode([
                    'ok' => false,
                    'mensaje' => 'El Servicio A no está disponible. No se puede validar el cliente.'
                ]));
        }

        // Cliente inexistente
        if ($cliente === false) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    'ok' => false,
                    'mensaje' => 'El cliente no existe en el Servicio A.'
                ]));
        }

        $cantidad = (int) $datos['cantidad'];
        $precio = (float) $datos['precio'];

        if ($cantidad <= 0 || $precio <= 0) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'ok' => false,
                    'mensaje' => 'La cantidad y el precio deben ser mayores a cero.'
                ]));
        }

        $venta = [
            'id_cliente' => (int) $datos['id_cliente'],
            'producto' => trim($datos['producto']),
            'cantidad' => $cantidad,
            'precio' => $precio,
            'total' => $cantidad * $precio
        ];

        if (!$this->Venta_model->crear($venta)) {
            return $this->output
                ->set_status_header(500)
                ->set_output(json_encode([
                    'ok' => false,
                    'mensaje' => 'No se pudo crear la venta.'
                ]));
        }

        $id = $this->db->insert_id();

        $this->output
            ->set_status_header(201)
            ->set_output(json_encode([
                'ok' => true,
                'mensaje' => 'Venta creada correctamente.',
                'id_venta' => $id
            ]));
    }

    public function actualizar($id)
    {
        $venta = $this->Venta_model->obtenerPorId($id);

        if (!$venta) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    'ok' => false,
                    'mensaje' => 'Venta no encontrada.'
                ]));
        }

        $datos = json_decode($this->input->raw_input_stream, true);

        if (
            empty($datos['id_cliente']) ||
            empty($datos['producto']) ||
            empty($datos['cantidad']) ||
            empty($datos['precio'])
        ) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'ok' => false,
                    'mensaje' => 'Todos los campos de la venta son obligatorios.'
                ]));
        }

        // Validar que el nuevo cliente exista en el Servicio A
        $cliente = $this->clienteExiste($datos['id_cliente']);

        // Servicio A no disponible
        if ($cliente === null) {
            return $this->output
                ->set_status_header(503)
                ->set_output(json_encode([
                    'ok' => false,
                    'mensaje' => 'El Servicio A no está disponible. No se puede validar el cliente.'
                ]));
        }

        // Cliente inexistente
        if ($cliente === false) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    'ok' => false,
                    'mensaje' => 'El cliente no existe en el Servicio A.'
                ]));
        }

        $cantidad = (int) $datos['cantidad'];
        $precio = (float) $datos['precio'];

        if ($cantidad <= 0 || $precio <= 0) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'ok' => false,
                    'mensaje' => 'La cantidad y el precio deben ser mayores a cero.'
                ]));
        }

        $actualizacion = [
            'id_cliente' => (int) $datos['id_cliente'],
            'producto' => trim($datos['producto']),
            'cantidad' => $cantidad,
            'precio' => $precio,
            'total' => $cantidad * $precio
        ];

        if (!$this->Venta_model->actualizar($id, $actualizacion)) {
            return $this->output
                ->set_status_header(500)
                ->set_output(json_encode([
                    'ok' => false,
                    'mensaje' => 'No se pudo actualizar la venta.'
                ]));
        }

        $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'ok' => true,
                'mensaje' => 'Venta actualizada correctamente.'
            ]));
    }

    public function eliminar($id)
    {
        $venta = $this->Venta_model->obtenerPorId($id);

        if (!$venta) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    'ok' => false,
                    'mensaje' => 'Venta no encontrada.'
                ]));
        }

        if (!$this->Venta_model->eliminar($id)) {
            return $this->output
                ->set_status_header(500)
                ->set_output(json_encode([
                    'ok' => false,
                    'mensaje' => 'No se pudo eliminar la venta.'
                ]));
        }

        $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'ok' => true,
                'mensaje' => 'Venta eliminada correctamente.'
            ]));
    }
}