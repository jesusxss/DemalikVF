<?php
error_reporting(E_ALL & ~E_NOTICE);

class Usuarios extends Controller
{
    public function __construct()
    {$nombre = isset($_POST['nombre']) ? strClean($_POST['nombre']) : '';
        parent::__construct();
        session_start();
        if (empty($_SESSION['tipo']) || $_SESSION['tipo'] == 2) {
            header('Location: '. BASE_URL . 'admin');
            exit;
        }
    }
    public function index()
{
    // PRUEBA DE AUDITORÍA MANUAL
    $sql = "INSERT INTO auditoria (usuario_id, nombre_usuario, tipo_usuario, accion, fecha)
            VALUES (?, ?, ?, ?, ?)";
    $params = array(1, 'Prueba', 1, 'Prueba auditoría manual', date('Y-m-d H:i:s'));
    $this->model->registrarAuditoria($sql, $params);

    // Lo que ya tenías
    $data['title'] = 'usuarios';
    $this->views->getView('admin/usuarios', "index", $data);
}


   public function listar()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $esAdmin = isset($_SESSION['tipo']) && $_SESSION['tipo'] == 1;

    /* ── trae SOLO administradores (1) y personal (3) ── */
    //  Si tu modelo aún espera un parámetro (1) pásaselo;
    //  si ya lo quitaste, usa simplemente getUsuarios().
    $usuarios = $this->model->getUsuarios(1);   // o getUsuarios(1)

    $data = [];
    foreach ($usuarios as $i => $row) {

        /* item autoincremental para la tabla */
        $fila = [];
        $fila['item'] = $i + 1;

        /* columnas que vienen tal cual de la BD */
        $fila['id']        = $row['id'];
        $fila['nombre']    = $row['nombre'];
        $fila['correo']    = $row['correo'];
        $fila['direccion'] = $row['direccion'];

        /* mapeo del tipo numérico → texto para mostrar */
        $fila['rol']  = ($row['tipo'] == 1) ? 'ADMINISTRADOR' : 'PERSONAL';

        /* dejamos también el tipo numérico –lo necesita editUser()*/
        $fila['tipo'] = $row['tipo'];

        /* botones de acción */
        if ($esAdmin) {
            $fila['accion'] = '
              <div class="d-flex">
                <button class="btn btn-primary" type="button"
                        onclick="editUser('.$row['id'].')">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-danger" type="button"
                        onclick="eliminarUser('.$row['id'].')">
                    <i class="fas fa-trash"></i>
                </button>
              </div>';
        } else {
            $fila['accion'] = '<span class="text-muted">Sin permisos</span>';
        }

        $data[] = $fila;
    }

    /*  IMPORTANTE:  sin espacios antes ni después  */
    header('Content-Type: application/json');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    die();
}



    public function registrar()
    {
        file_put_contents(__DIR__ . '/../../log.txt', date('Y-m-d H:i:s') . " - POST: " . print_r($_POST, true) . "\n", FILE_APPEND);

        
        if (
    isset($_POST['nombre']) &&
    isset($_POST['apellido']) &&
    isset($_POST['correo']) &&
    isset($_POST['clave']) &&
    isset($_POST['direccion']) &&
    isset($_POST['id'])
    ) {
               if ($_SESSION['tipo'] != 1) {
    $respuesta = array('msg' => 'No tienes permiso para registrar usuarios', 'icono' => 'warning');
    echo json_encode($respuesta);
    die();
}


            $nombre = isset($_POST['nombre']) ? strClean($_POST['nombre']) : '';

            $apellido = strClean($_POST['apellido']); // sigue siendo útil para mostrarlo en la tabla

if (isset($_POST['rol'])) {
    $rolTexto = strtoupper(strClean($_POST['rol']));
    if ($rolTexto === 'ADMINISTRADOR') {
        $tipo = 1;
    } elseif ($rolTexto === 'PERSONAL') {
        $tipo = 3;
    } else {
        $respuesta = array('msg' => 'Rol inválido.', 'icono' => 'warning');
        echo json_encode($respuesta);
        exit;
    }
} else {
    $respuesta = array('msg' => 'Rol no enviado.', 'icono' => 'warning');
    echo json_encode($respuesta);
    exit;
}


            $clave = (empty($_POST['id'])) ? strClean($_POST['clave']) : '0000000';
            $correo = strClean($_POST['correo']);
            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $respuesta = array('msg' => 'El correo no tiene un formato válido', 'icono' => 'warning');
    echo json_encode($respuesta);
    exit;
}
            $direccion = strClean($_POST['direccion']);
           
            $id = strClean($_POST['id']);
            $hash = password_hash($clave, PASSWORD_DEFAULT);

            if (
                   empty($nombre) || empty($apellido) || empty($correo) || empty($direccion) ||
                  (empty($id) && empty($clave)) // Solo se exige clave si es nuevo
                 ) {
                $respuesta = array('msg' => 'todo los campos son requeridos', 'icono' => 'warning');
            } else {
                if (empty($id)) {
                    $result = $this->model->verificarCampo('correo', $correo, 0);
                    if (empty($result)) {
                        $data = $this->model->registrar($correo, $nombre, $apellido, $hash, $direccion, $tipo);
                        if ($data > 0) {
                            $respuesta = array('msg' => 'usuario registrado', 'icono' => 'success');
                            $this->registrarAuditoria("Registró un nuevo usuario: $nombre $apellido ($correo)");
                         }else {
                            $respuesta = array('msg' => 'error al registrar', 'icono' => 'error');
                        }
                    } else {
                        $respuesta = array('msg' => 'correo ya existe', 'icono' => 'warning');
                    }
                } else {
                    $result = $this->model->verificarCampo('correo', $correo, $id);
                    if (empty($result)) {
                        $data = $this->model->modificar($correo, $nombre, $apellido, $direccion, $id);
                        if ($data == 1) {
                        $respuesta = array('msg' => 'usuario modificado', 'icono' => 'success');
                        $this->registrarAuditoria("Modificó el usuario ID: $id ($correo)");
                        } else {
                            $respuesta = array('msg' => 'error al modificar', 'icono' => 'error');
                        }
                    } else {
                        $respuesta = array('msg' => 'correo ya existe', 'icono' => 'warning');
                    }
                }
            }
            echo json_encode($respuesta);
        }
        die();
    }
    //eliminar user
    public function delete($idUser)
{
    // Verificamos si el usuario actual es PERSONAL (tipo = 2)
    if ($_SESSION['tipo'] != 1) {
        $respuesta = array('msg' => 'No tienes permiso para eliminar usuarios', 'icono' => 'warning');
        echo json_encode($respuesta);
        die();
    }

    if (is_numeric($idUser)) {
        $data = $this->model->eliminar($idUser);
        if ($data == 1) {
             $respuesta = array('msg' => 'usuario dado de baja', 'icono' => 'success');
             $this->registrarAuditoria("Eliminó/bajó el usuario ID: $idUser");
        } else {
            $respuesta = array('msg' => 'error al eliminar', 'icono' => 'error');
        }
    } else {
        $respuesta = array('msg' => 'error desconocido', 'icono' => 'error');
    }
    echo json_encode($respuesta);
    die();
}
    //editar user
    public function editar($idUser)
    {
        if (is_numeric($idUser)) {
            $data = $this->model->getUsuario($idUser);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function perfil()
    {
        $data['title'] = 'Tus datos';
        $data['usuario'] = $this->model->getUsuario($_SESSION['id_usuario']);
        $this->views->getView('admin/usuarios', "perfil", $data);
    }
    public function actualizarPerfil()
    {
        if (isset($_POST['nombre']) && isset($_POST['correo']) && isset($_POST['apellido'])) {
            $nombre = isset($_POST['nombre']) ? strClean($_POST['nombre']) : '';
            $apellido = strClean($_POST['apellido']);
            $correo = strClean($_POST['correo']);
            $direccion = strClean($_POST['direccion']);
            if (
                empty($nombre) || empty($apellido) || empty($correo)
                || empty($direccion)
            ) {
                $respuesta = array('msg' => 'todo los campos son requeridos', 'icono' => 'warning');
            } else {
                    $result = $this->model->verificarCampo('correo', $correo, $_SESSION['id_usuario']);
                    if (empty($result)) {
                        $data = $this->model->modificar($correo, $nombre, $apellido, $direccion, $_SESSION['id_usuario']);
                        if ($data == 1) {
    $respuesta = array('msg' => 'datos modificado', 'icono' => 'success');
    $this->registrarAuditoria("Actualizó su propio perfil");
}else {
                            $respuesta = array('msg' => 'error al modificar', 'icono' => 'error');
                        }
                    } else {
                        $respuesta = array('msg' => 'correo ya existe', 'icono' => 'warning');
                    }
            }
            echo json_encode($respuesta);
        }
        die();
    }

    public function actualizarPassword()
    {
        if (isset($_POST['nueva']) && isset($_POST['actual']) && isset($_POST['confirmar'])) {
            $nueva = strClean($_POST['nueva']);
            $confirmar = strClean($_POST['confirmar']);
            $actual = strClean($_POST['actual']);
            if (
                empty($nueva) || empty($confirmar)
                || empty($actual)
            ) {
                $respuesta = array('msg' => 'Todo los campos son requeridos', 'icono' => 'warning');
            } else if ($nueva != $confirmar) {
                $respuesta = array('msg' => 'Las contraseñas no coinciden', 'icono' => 'warning');
            } else {
                $result = $this->model->getUsuario($_SESSION['id_usuario']);
                if (password_verify($actual, $result['clave'])) {
                    $hash = password_hash($nueva, PASSWORD_DEFAULT);
                    $data = $this->model->modificarClave($hash, $_SESSION['id_usuario']);
                    if ($data == 1) {
    $respuesta = array('msg' => 'Contraseña modificada', 'icono' => 'success');
    $this->registrarAuditoria("Cambió su contraseña");
} else {
                        $respuesta = array('msg' => 'error al modificar', 'icono' => 'error');
                    }
                } else {
                    $respuesta = array('msg' => 'contraseña actual incorrecta', 'icono' => 'warning');
                }
            }
            echo json_encode($respuesta);
        }
        die();
    }

   public function registrarAuditoria($accion)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Esto confirma si entra a esta función
    file_put_contents(__DIR__ . '/debug_registro.txt', "[" . date('Y-m-d H:i:s') . "] Entró a registrarAuditoria\n", FILE_APPEND);

    if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['nombre_usuario']) || !isset($_SESSION['tipo'])) {
        file_put_contents(__DIR__ . '/debug_registro.txt', "[" . date('Y-m-d H:i:s') . "] Sesión incompleta: " . json_encode($_SESSION) . "\n", FILE_APPEND);
        return false;
    }

    $id_usuario = $_SESSION['id_usuario'];
    $nombre     = $_SESSION['nombre_usuario'];
    $tipo       = $_SESSION['tipo'];
    $fecha      = date('Y-m-d H:i:s');

    $sql    = "INSERT INTO auditoria (usuario_id, nombre_usuario, tipo_usuario, accion, fecha)
               VALUES (?, ?, ?, ?, ?)";
    $params = array($id_usuario, $nombre, $tipo, $accion, $fecha);

    // Agrega un log para ver si intenta ejecutar la consulta
    file_put_contents(__DIR__ . '/debug_registro.txt', "[" . date('Y-m-d H:i:s') . "] Ejecutando SQL\n", FILE_APPEND);

    try {
        $this->model->registrarAuditoria($sql, $params);
        file_put_contents(__DIR__ . '/debug_registro.txt', "[" . date('Y-m-d H:i:s') . "] Insert ejecutado correctamente\n", FILE_APPEND);
    } catch (Exception $e) {
        file_put_contents(__DIR__ . '/debug_registro.txt', "[" . date('Y-m-d H:i:s') . "] ERROR SQL: " . $e->getMessage() . "\n", FILE_APPEND);
    }

    return true;
}
    
}
