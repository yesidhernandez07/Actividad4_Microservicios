<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cliente_model extends CI_Model
{
    public function obtenerTodos()
    {
        return $this->db
            ->order_by('id_cliente', 'DESC')
            ->get('clientes')
            ->result();
    }

    public function obtenerPorId($id)
    {
        return $this->db
            ->where('id_cliente', $id)
            ->get('clientes')
            ->row();
    }

    public function crear($data)
    {
        return $this->db->insert('clientes', $data);
    }

    public function actualizar($id, $data)
    {
        return $this->db
            ->where('id_cliente', $id)
            ->update('clientes', $data);
    }

    public function eliminar($id)
    {
        return $this->db
            ->where('id_cliente', $id)
            ->delete('clientes');
    }
}