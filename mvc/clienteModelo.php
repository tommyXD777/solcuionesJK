<?php
session_start();

class UsuarioModelo {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function registrar($nombre, $apellido, $correo, $username, $telefono, $contraseña) {
    $hash = password_hash($contraseña, PASSWORD_DEFAULT);
    $stmt = $this->conexion->prepare(
        "INSERT INTO clientes (nombre, apellido, correo, username, telefono, contraseña_hash)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("ssssss", $nombre, $apellido, $correo, $username, $telefono, $hash);
    return $stmt->execute();
}

}
?>
