<?php
class Conexion {

    public static function Conectar() {
        $host    = 'zdnqij.h.filess.io';
        $db      = 'devolutionsync_sunhurried';
        $user    = 'devolutionsync_sunhurried';
        $pass    = '844c2b0071fdc6c2439a307b647d61e282906c54';
        $charset = 'utf8';
        $port    = '3306';

        $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";

        $opciones = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, $user, $pass, $opciones);
            return $pdo;
        } catch (PDOException $e) {
            die("Error de conexión (PDO): " . $e->getMessage());
        }
    }
}
?>
