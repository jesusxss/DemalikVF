<?php

class AuditoriaModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getRegistros()
    {
        $sql = "SELECT a.id, u.nombre AS usuario, a.accion, DATE(a.fecha) AS fecha, TIME(a.fecha) AS hora
                FROM auditoria a
                INNER JOIN usuarios u ON a.usuario_id = u.id
                ORDER BY a.id DESC";
        return $this->selectAll($sql);
    }
}