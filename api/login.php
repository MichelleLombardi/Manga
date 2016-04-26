<?php
include "./config/Connection.php";

session_start();
switch ($_SERVER["REQUEST_METHOD"]) {
    case "GET":
        $headers = apache_request_headers();
        if(isset($headers['Authorization'])) {
            $token = $headers['Authorization'];
            $user = isset($_SESSION["user"]) ? $_SESSION["user"] : null;

            if (($user != null) && ($user["token"] == $token)) {
                // Enviamos los datos
                echo json_encode($user);
            } else {
                logout();
                echo json_encode(
                    array(
                        "error" => "La session ya no sigue activa"
                    )
                );
            }
        }
        break;

    case "POST":
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

        if(key_exists("email", $user) &&
            key_exists("pass", $user)) {

            $query = '
                  SELECT
                    id_app_user AS id,
                    name_app_user as name
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

            $obj = pg_fetch_object($res);

            $user = array(
                "token"=>session_id(),
                "id"=>$obj->id,
                "nombre"=>$obj->name
            );

            $_SESSION["user"] = $user;

            echo json_encode($user);

        }
        else {
            echo json_encode(
                array(
                    "error"=> "Faltan parametros"
                )
            );
        }
        
        break;

    case "DELETE":
        logout();
        echo json_encode(array());
        break;
}

function logout() {
    // Vaciamos la variable user de la session
    $_SESSION["user"] = null;

    // Regeneramos el token
    session_regenerate_id();
}

