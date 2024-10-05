<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

$tipo_usuario = isset($_SESSION['tipoUsuario']) ? $_SESSION['tipoUsuario'] : 'No registrado';
$_SESSION['tipoUsuario'] = $tipo_usuario;

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$_SESSION['user_id'] = $user_id;

$categoriaCliente = isset($_SESSION['categoriaCliente']) ? $_SESSION['categoriaCliente'] : null;
$_SESSION['categoriaCliente'] = $categoriaCliente;
