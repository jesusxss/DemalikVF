<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/Config/Config.php';

class Conexion {
    private $conect;

    public function __construct()
    {
        $pdo = "mysql:host=" . HOST . ";dbname=" . DB . ";" . CHARSET;
        try {
            $this->conect = new PDO($pdo, USER, PASS);
            $this->conect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // 🔥 Línea nueva: fijamos la zona horaria para TODA la sesión MySQL
            $this->conect->query("SET time_zone = '-05:00'");   // GMT-5 (Perú)

        } catch (PDOException $e) {
            echo "❌ Error en la conexión: " . $e->getMessage();
            exit;
        }
    }

    public function conect()
    {
        return $this->conect;
    }
}
?>
