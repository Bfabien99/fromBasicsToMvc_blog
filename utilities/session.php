<?php
session_start();
if(!empty($_SESSION['msg']) && is_array($_SESSION['msg'])){
    $msg_type = $_SESSION['msg']['type'];
    $msg_content = $_SESSION['msg']['content'];
    $_SESSION['msg'] = false;
}

if(!empty($_SESSION['user_public_id'])){
    if(!getUserByPublicID($pdo,$_SESSION['user_public_id'])){
        $_SESSION['msg']['type'] = 'error';
        $_SESSION['msg']['content'] = 'Invalid User Credentials!';
        unset($_SESSION['user_public_id']);
        header('Location: /index.php');
        exit();
    }else{
        $user = getUserByPublicID($pdo,$_SESSION['user_public_id']);
    }
}
