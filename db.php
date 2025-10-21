<?php
$cfg = require __DIR__ . '/config.php';
$pdo = new PDO('mysql:host='.$cfg['db_host'].';dbname='.$cfg['db_name'].';charset=utf8mb4',$cfg['db_user'],$cfg['db_pass'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
session_start();
function is_logged(){return isset($_SESSION['uid']);}
function is_admin(){return isset($_SESSION['role']) && $_SESSION['role']==='admin';}
function redirect($u){header('Location: '.$u);exit;}
?>
