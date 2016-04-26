<?php
include "./config/Connection.php";


$headers = apache_request_headers();
if(isset($headers['Authorization'])){

echo print_r(header("A"));


