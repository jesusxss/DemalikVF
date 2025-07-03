<?php

class Auditoria extends Controller
{
    public function __construct()
    {
        session_start();
        //if (empty($_SESSION['activo'])) {
        //    header("location: " . BASE_URL);
        //    exit;
        //}
        parent::__construct();
    }

    public function index()
    {
        //if ($_SESSION['tipo'] != 1) {
        //    header("location: " . BASE_URL . "admin/home");
        //    exit;
        //}
        $this->views->getView("admin/auditoria", "index");
    }

    public function listar()
    {
        $data = $this->model->getRegistros();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }
}