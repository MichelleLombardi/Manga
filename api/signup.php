<?php
include "./config/Connection.php";

switch ($_SERVER["REQUEST_METHOD"]) {
    case "POST":

        session_start();

        $conn = new Connection();
        $query = '';
        $values = array();

        $user = (object) $_POST
        or die(
            json_encode(
                array(
                    "error"=> "Los valores pasados como parametro son erroneos"
                )
            )
        );

        if(key_exists("name", $user) &&
            key_exists("email", $user) && 
            key_exists("pass", $user) && 
            key_exists("birthday", $user) && 
            key_exists("facebook", $user) && 
            key_exists("biography", $user))
        {
            $query = '
                SELECT 
                  email_app_user 
                FROM 
                  app_user 
                WHERE 
                  email_app_user = $1;
            ';

            $values = array(
                $user->email
            );

            // Buscamos si ya existe un usuario con ese email
            $res = $conn->executeQuery($query, $values);

            $count = pg_num_rows($res);

            // Si no existe entonces tratamos de crearlo
            if (!$count) {
                $query = "
                INSERT INTO
                  app_user (
                    name_app_user,
                    email_app_user,
                    password_app_user,
                    birthday_app_user,
                    created_at_app_user,
                    facebook_app_user,
                    biography_app_user
                  ) VALUES (
                    $1,
                    $2,
                    $3,
                    $4,
                    $5,
                    $6,
                    $7
                  )
                ";

                $values = array(
                    $user->name,
                    $user->email,
                    $user->pass,
                    $user->birthday,
                    date("Y-M-d H:m:s.u"),
                    $user->facebook,
                    $user->biography
                );

                // Creamos un usuario nuevo
                $conn->execute($query, $values);

                $query = '
                  SELECT
                    id_app_user AS id
                  FROM  
                    app_user  
                  WHERE  
                    email_app_user = $1 AND  
                    password_app_user = $2 
                ';

                $values = array(
                    $user->email,
                    $user->pass
                );

                $res = $conn->executeQuery($query, $values);

                $id = pg_fetch_object($res)->id;

                $user = array(
                    "token"=>session_id(),
                    "id"=>$id,
                    "nombre"=>$user->name
                );

                $_SESSION["user"] = $user;

                echo json_encode($user);

            }
            else {
                echo json_encode(
                    array(
                        "error"=> "El email ya existe"
                    )
                );
                exit;
            }
        }
        else {
            echo json_encode(
                array(
                    "error"=> "Faltan parametros"
                )
            );
        }

        break;

}