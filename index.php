<?php
    session_start();
    
    if (!isset($_SESSION['active']) || $_SESSION['active'] == false)
    {
        header('location: public/index.html');
    } else {
        header('location: public/main.html');
    }
	
	// if ( extension_loaded('pdo') ) {
	// 	echo "pdo exist!";
	// }

	// if ( extension_loaded('pdo_mysql') ) { // e.g., pdo_mysql
	// 	echo "pdo_mysql exist!";
	// }
?>
