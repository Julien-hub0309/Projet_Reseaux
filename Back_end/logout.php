<?php
session_start();
session_destroy();
header('Location: ../index.php'); // Redirige vers le login dans le même dossier
exit();
?>