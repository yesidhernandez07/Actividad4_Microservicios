<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Venta_model extends CI_Model
{
    public function obtenerTodas()
    {
        return $this->db
            ->order_by('id_venta', 'DESC')
            ->get('ventas')
            ->result();
    }

    public function obtenerPorId($id)
    {
        return $this->db
            ->where('id_venta', $id)
            ->get('ventas')
            ->row();
    }

    public function crear($data)
    {
        return $this->db->insert('ventas', $data);
    }

    public function actualizar($id, $data)
    {
        return $this->db
            ->where('id_venta', $id)
            ->update('ventas', $data);
    }

    public function eliminar($id)
    {
        return $this->db
            ->where('id_venta', $id)
            ->delete('ventas');
    }
}