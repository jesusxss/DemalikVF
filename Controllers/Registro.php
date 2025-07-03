<?php
class Registro extends Controller
{
    public function __construct()
    {
        parent::__construct();
        session_start();
    }
    public function save()
{

file_put_contents('prueba.txt', 'ejecutado');

    ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

    if (
        isset($_POST['dni']) &&
        isset($_POST['nombre']) &&
        isset($_POST['apellido']) &&
        isset($_POST['direccion']) &&
        isset($_POST['email']) &&
        isset($_POST['clave'])
    ) {
        $dni = strClean($_POST['dni']);
        $nombre = strClean($_POST['nombre']);
        $apellido = strClean($_POST['apellido']);
        $direccion = strClean($_POST['direccion']);
        $email = strClean($_POST['email']);
        $clave = strClean($_POST['clave']);

        if (
            empty($dni) || empty($nombre) || empty($apellido) || empty($direccion) || empty($email) || empty($clave)
        ) {
            $respuesta = array('msg' => 'Todos los campos son requeridos', 'icono' => 'warning');
        } else if (!preg_match('/^[0-9]{8}$/', $dni)) {
            $respuesta = array('msg' => 'El DNI debe tener exactamente 8 dígitos numéricos', 'icono' => 'warning');
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $respuesta = array('msg' => 'El correo no tiene un formato válido', 'icono' => 'warning');
        } else {
            $consulta = $this->model->getUsuario($email);
            if (empty($consulta)) {
                $hash = password_hash($clave, PASSWORD_DEFAULT);
                $tipo = 2;
                $data = $this->model->registrar($dni, $email, $nombre, $apellido, $hash, $direccion, $tipo);
                if ($data > 0) {
                    $_SESSION['id_usuario'] = $data;
                    $_SESSION['email'] = $email;
                    $_SESSION['nombre_usuario'] = $nombre;
                    $respuesta = array('msg' => 'Usuario registrado', 'icono' => 'success');
                } else {
                    $respuesta = array('msg' => 'Error al registrarse', 'icono' => 'error');
                }
            } else {
                $respuesta = array('msg' => 'El correo ya existe', 'icono' => 'warning');
            }
        }
    } else {
        $respuesta = array('msg' => 'Error desconocido', 'icono' => 'error');
    }
    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    die();
}
    //registrar pedidos
    public function registrarPedido()
{
    if (!empty($_SESSION['address'])) {
        $datos = file_get_contents('php://input');
        $json = json_decode($datos, true);
        $productos = $json['productos'];
        $pedidos = $json['pedidos'];
        if (is_array($productos)) {
            $transaccion = $pedidos['id'];
            $monto = $pedidos['purchase_units'][0]['amount']['value'];
            $cliente = $_SESSION['address'];
            $envio = 0;

            // 👇 Corrección de zona horaria y fecha real de Perú
            date_default_timezone_set('America/Lima');
            $fecha = date('Y-m-d H:i:s');

            // 👇 Asegúrate de pasar $fecha al modelo (debes ajustar el modelo también)
            $data = $this->model->registrarPedido(
                $transaccion,
                $monto,
                $cliente['nombre'],
                $cliente['apellido'],
                $cliente['direccion'],
                $cliente['ciudad'],
                $cliente['cod'],
                $cliente['pais'],
                $cliente['telefono'],
                $envio,
                $_SESSION['id_usuario'],
                $fecha // 👈 esto es nuevo
            );

            if ($data > 0) {
                foreach ($productos as $producto) {
                    $temp = $this->model->getProducto($producto['id']);
                    $this->model->registrarDetalle($temp['nombre'], $temp['precio'], $producto['cantidad'], $producto['id'], $data);
                    $nuevaCantidad = $temp['cantidad'] - $producto['cantidad'];
                    $nuevaVenta = $temp['ventas'] + $producto['cantidad'];
                    $this->model->actualizarStock($nuevaCantidad, $nuevaVenta, $temp['id']);
                }
                $mensaje = array('msg' => 'Pedido registrado', 'icono' => 'success');
                unset($_SESSION['address']);
            } else {
                $mensaje = array('msg' => 'Error al registrar el pedido', 'icono' => 'error');
            }
        } else {
            $mensaje = array('msg' => 'Error fatal con los datos', 'icono' => 'error');
        }
    } else {
        $mensaje = array('msg' => 'Datos de envio no encontrado', 'icono' => 'error');
    }
    echo json_encode($mensaje);
    die();
}
}
