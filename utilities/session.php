<?php
session_start();
if(!empty($_SESSION['msg']) && is_array($_SESSION['msg'])){
    $msg_type = $_SESSION['msg']['type'];
    $msg_content = $_SESSION['msg']['content'];
    $_SESSION['msg'] = false;
}
