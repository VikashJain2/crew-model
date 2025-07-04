<?php
session_start();

function requireAuth(){
    if(!isset($_SESSION['employee_id'])){
        header("location: index.php");
        exit();
    }
}

function hashPassword($password){
    return password_hash($password, PASSWORD_DEFAULT);
}

function verifyPassword($password, $hash){
    return password_verify($password, $hash);
}
?>
