<?php
    require_once('keys.php');
    try
    {
        $pdo = new PDO("mysql:host=".DB_HOST.":".DB_PORT.";dbname=".DB_NAME,DB_USERNAME,DB_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    }
    catch(PDOException $e)
    {
        echo $e->getMessage();
        die();
    }
