<?php
require_once("database/connect.php");
if($_POST['mail']){
$id=$_POST['id'];
$status=$_POST['status'];
$mail=$_POST['mail'];

echo $db->Execute("update","update wp_order SET status='$status' where ID='$id'");

}





?>