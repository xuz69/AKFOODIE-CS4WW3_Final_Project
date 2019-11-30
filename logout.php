<?php
    // logout php file
    session_start();
    unset($_SESSION["ID"]); //frees all session variables currently registered.
    header("Location: https://{$_SERVER['HTTP_HOST']}/login.html"); //redirection to login page
?>
