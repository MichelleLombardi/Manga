<?php

    $host_db = "localhost";
    $port_db = "5432";
    $user_db = "postgres";
    $pass_db = "masterkey";
    $dbname_db= "manga";
 
	//$conexion = mysql_connect($host_db, $user_db, $pass_db);
    $dbconn = pg_connect("host=localhost port=5432 user=postgres password=masterkey dbname=manga");
	 
	$sqlSUser = "SELECT * FROM app_user WHERE email_app_user = '$_POST[emailca]' ";
 
	$res = pg_query($dbconn, $sqlSUser) or die('ERROR AL INSERTAR DATOS: ' . pg_last_error());
	 
	$count = pg_num_rows($res);
	if ($count == 1){    
        echo ("email-Email ya existente");
        exit;
	 }
	 else{
          
         $fecha_actual=date("Y/m/d");
         $query = "INSERT INTO app_user (password_app_user, name_app_user, email_app_user, birthday_app_user, created_at_app_user, facebook_app_user, biography_app_user) VALUES ('".$_POST["passca"]."', '".$_POST["nameca"]."','".$_POST["emailca"]."','".$_POST["bca"]."','".$fecha_actual."','".$_POST["fbca"]."','".$_POST["bioca"]."')";

        if (!pg_query($dbconn, $query)){
            die('Error: ' . pg_last_error());
            echo "Error al crear el usuario." . "<br />";
        }

        else{
            echo "<br />" . "<h2>" . "Usuario Creado Exitosamente!" . "</h2>";
            
        }
     }
	 
    pg_free_result($res); //liberar los datos
    pg_close($dbconn);
 
?>