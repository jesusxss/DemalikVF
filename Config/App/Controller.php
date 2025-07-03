<?php
class Controller{
    protected $views, $model;
    public function __construct()
    {
        $this->views = new Views();
        $this->cargarModel();
    }
    public function cargarModel()
    {
        $model = get_class($this)."Model";
        $ruta = "Models/".$model.".php";
        if (file_exists($ruta)) {
            require_once $ruta;
            $this->model = new $model();
        }
    }

    public function registrarAuditoria($accion)
{
    if (!empty($_SESSION['id_usuario'])) {
        $usuario = $_SESSION['id_usuario'];
        $fecha = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR']; // opcional, si quieres guardar IP

        $sql = "INSERT INTO auditoria (usuario_id, accion, fecha, ip) VALUES (?, ?, ?, ?)";
        $this->model->ejecutarAuditoria($sql, [$usuario, $accion, $fecha, $ip]);
    }
}

}
 
?>