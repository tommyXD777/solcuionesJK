<?php
session_start();

class UsuarioModelo {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    // Generar username desde correo asegurando que sea único
   private function generarUsernameDesdeCorreo($correo) {
    $base = explode('@', $correo)[0];
    $usuario = $base;
    $contador = 1;

    // Verificar si ya existe ese username
    $stmt = $this->conexion->prepare("SELECT COUNT(*) as total FROM clientes WHERE username = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $fila = $resultado->fetch_assoc();
    $existe = $fila['total'];
    $stmt->close();

    // Si existe, intentar con sufijos numéricos
    while ($existe > 0) {
        $usuario = $base . $contador;
        $contador++;

        $stmt = $this->conexion->prepare("SELECT COUNT(*) as total FROM clientes WHERE username = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $fila = $resultado->fetch_assoc();
        $existe = $fila['total'];
        $stmt->close();
    }

    return $usuario;
}


    public function registrar($correo, $contraseña) {
        $hash = password_hash($contraseña, PASSWORD_DEFAULT);
        $username = $this->generarUsernameDesdeCorreo($correo);

        $stmt = $this->conexion->prepare(
            "INSERT INTO clientes (correo, contraseña_hash, username)
             VALUES (?, ?, ?)"
        );
        $stmt->bind_param("sss", $correo, $hash, $username);
        return $stmt->execute();
    }
}
?>
