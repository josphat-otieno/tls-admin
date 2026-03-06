<?php
session_start();
session_destroy();

header("Location: auth_logout.php")
?>