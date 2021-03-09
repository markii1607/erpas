<?php
    session_start();
    
    if (!isset($_SESSION['active']) || $_SESSION['active'] == false)
    {
        header('location: ../public/index.php');
    } else {
        header('location: ../public/main.php');
    }
?>
