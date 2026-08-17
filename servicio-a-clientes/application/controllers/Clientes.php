<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Clientes extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Cliente_model');
        $this->output->set_content_type('application/json');
    }

    public function index()
    {
        $clientes = $this->Cliente_model->obtenerTodos();

        $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'ok' => true,
                'data' => $clientes
            ]));
    }

    public function ver($id)
    {
        $cliente = $this->Cliente_model->obtenerPorId($id);

        if (!$cliente) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    'ok' => false,
                    'mensaje' => 'Cliente no encontrado.'
                ]));
        }

        $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'ok' => true,
                'data' => $cliente
            ]));
    }

    public function crear()
    {
        $datos = json_decode($this->input->raw_input_stream, true);

        if (
            empty($datos['nombre']) ||
            empty($datos['correo'])
        ) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'ok' => false,
                    'mensaje' => 'Nombre y correo son obligatorios.'
                ]));
        }

        $cliente = [
            'nombre' => trim($datos['nombre']),
            'correo' => trim($datos['correo']),
            'telefono' => isset($datos['telefono'])
                ? trim($datos['telefono'])
                : null
        ];

        if (!$this->Cliente_model->crear($cliente)) {
            return $this->output
                ->set_status_header(500)
                ->set_output(json_encode([
                    'ok' => false,
                    'mensaje' => 'No se pudo crear el cliente.'
                ]));
        }

        $id = $this->db->insert_id();

        $this->output
            ->set_status_header(201)
            ->set_output(json_encode([
                'ok' => true,
                'mensaje' => 'Cliente creado correctamente.',
                'id_cliente' => $id
            ]));
    }

    public function actualizar($id)
    {
        $cliente = $this->Cliente_model->obtenerPorId($id);

        if (!$cliente) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    'ok' => false,
                    'mensaje' => 'Cliente no encontrado.'
                ]));
        }

        $datos = json_decode($this->input->raw_input_stream, true);

        if (
            empty($datos['nombre']) ||
            empty($datos['correo'])
        ) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'ok' => false,
                    'mensaje' => 'Nombre y correo son obligatorios.'
                ]));
        }

        $actualizacion = [
            'nombre' => trim($datos['nombre']),
            'correo' => trim($datos['correo']),
            'telefono' => isset($datos['telefono'])
                ? trim($datos['telefono'])
                : null
        ];

        $this->Cliente_model->actualizar($id, $actualizacion);

        $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'ok' => true,
                'mensaje' => 'Cliente actualizado correctamente.'
            ]));
    }

    public function eliminar($id)
    {
        $cliente = $this->Cliente_model->obtenerPorId($id);

        if (!$cliente) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    'ok' => false,
                    'mensaje' => 'Cliente no encontrado.'
                ]));
        }

        $this->Cliente_model->eliminar($id);

        $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'ok' => true,
                'mensaje' => 'Cliente eliminado correctamente.'
            ]));
    }
}