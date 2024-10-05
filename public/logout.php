<?php
session_start();
$_SESSION['tipoUsuario'] = 'No registrado';
$_SESSION['user_id'] = 0;
$_SESSION['categoriaCliente'] = null;
$_SESSION['nombreUsuario'] = null;

header('Location: home.php');
exit();
?>