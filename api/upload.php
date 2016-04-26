<?php

include "config/Connection.php";

switch ($_SERVER["REQUEST_METHOD"]) {
    case "GET":
        $file = fopen('users.txt', 'r');
        $users = [];
        while ($line = fgets($file)) {
            $fields = explode("&&", trim($line));
            if (count($fields) > 1) {
                array_push($users, [
                    "id" => $fields[0],
                    "name" => $fields[1],
                    "lastName" => $fields[2],
                    "age" => $fields[3],
                    "profession" => $fields[4]
                ]);
            }
        }
        fclose($file);
        echo json_encode([
            "status" => 200,
            "users" => $users
        ]);
        break;
    case "POST":
        if (isset($_POST["id"]) && isset($_POST["name"]) &&
            isset($_POST["age"]) && isset($_POST["lastName"]) &&
            isset($_POST["profession"])) {
            $id = $_POST["id"];
            $name = $_POST["name"];
            $age = $_POST["age"];
            $lastName = $_POST["lastName"];
            $profession = $_POST["profession"];
            $user_data = $id."&&".$name."&&".$lastName."&&".$age."&&".$profession;
            if ($user_data == "&&&&&&&&") {
                echo json_encode([
                    "status" => 406,
                    "response" => "Some fields is not passed"
                ]);
            } else {
                $file = fopen("users.txt", "a") or die("Unable to open file!");
                fseek($file, -1, SEEK_END);
                fwrite($file, "\n".$user_data);
                fclose($file);
                echo json_encode([
                    "status" => 200,
                    "user" => [
                        "id" => $id,
                        "name" => $name,
                        "lastName" => $lastName,
                        "age" => $age,
                        "profession" => $profession
                    ],
                    "response" => "User added"
                ]);
            }
        } else {
            echo json_encode([
                "status" => 406,
                "response" => "Some fields is not passed"
            ]);
        }
}