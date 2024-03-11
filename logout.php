<?php 
include("utilities/db.php");
include("utilities/functions.php");
include("utilities/session.php");
if(!isset($_SESSION["user_public_id"])){
    header("Location: /index.php");
    exit();
}

unset($_SESSION["user_public_id"]);
unset($user);
header("Location: /index.php");
exit();