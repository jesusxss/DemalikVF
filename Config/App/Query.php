<?php
class Query extends Conexion{
    private $pdo, $con, $sql, $datos;
    public function __construct() {
        $this->pdo = new Conexion();
        $this->con = $this->pdo->conect();
    }
    public function select(string $sql)
    {
        $this->sql = $sql;
        $resul = $this->con->prepare($this->sql);
        $resul->execute();
        $data = $resul->fetch(PDO::FETCH_ASSOC);
        return $data;
    }
    public function selectAll(string $sql)
    {
        $this->sql = $sql;
        $resul = $this->con->prepare($this->sql);
        $resul->execute();
        $data = $resul->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }

    public function ejecutarAuditoria($sql, $datos)
    {
    $this->conexion = new Conexion();
    $this->con = $this->conexion->conectar();
    $query = $this->con->prepare($sql);
    return $query->execute($datos);
    }

    public function save(string $sql, array $datos)
    {
        $this->sql = $sql;
        $this->datos = $datos;
        $insert = $this->con->prepare($this->sql);
        $data = $insert->execute($this->datos);
        if ($data) {
            $res = 1;
        }else{
            $res = 0;
        }
        return $res;
    }
    public function insertar(string $sql, array $datos)
{
    $this->sql = $sql;
    $this->datos = $datos;

    try {
        $insert = $this->con->prepare($this->sql);
        $data = $insert->execute($this->datos);

        if ($data) {
            return $this->con->lastInsertId();
        } else {
            file_put_contents('auditoria_error.txt', json_encode($insert->errorInfo()) . "\n", FILE_APPEND);
            return 0;
        }
    } catch (PDOException $e) {
        file_put_contents('auditoria_error.txt', $e->getMessage() . "\n", FILE_APPEND);
        return 0;
    }
}

    public function quoteValueQuery($value) {
        return $this->con->quote($value);
    }

    public function selectColum($sql)
    {
        $result = $this->con->prepare($sql);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_COLUMN);
    }
}
?>