<?php
// ENABLE CORS
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Access-Control-Allow-Origin');
header("Content-type: application/json");


class Connection {

    private $host = "localhost";
    private $port = "5432";
    private $user = "postgres";
    private $pass = "masterkey";
    private $db   = "manga";

    private $conn = null;
    private $res = null;

    public function Connection() {
        $this->conn = pg_connect("
            host=$this->host 
            port=$this->port 
            user=$this->user 
            password=$this->pass 
            dbname=$this->db
        ")
        or die(
            json_encode(
                array(
                    "error"=> "No se pudo conectar a la base de datos: " . pg_last_error()
                )
            )
        );
    }

    public function execute( $sql, $params ) {
        $this->res = pg_query_params($this->conn, $sql, $params)
        or die(
            json_encode(
                array(
                    "error"=> "No se pudo realizar la consulta: " . pg_last_error()
                )
            )
        );
    }

    public function executeQuery( $sql, $params ) {
        $this->res = pg_query_params($this->conn, $sql, $params)
        or die(
            json_encode(
                array(
                    "error"=> "No se pudo realizar la consulta: " . pg_last_error()
                )
            )
        );
        return $this->res;
    }

    public function __destruct() {
        // Liberamos los datos
        if( $this->res )
            pg_free_result($this->res)
            or die(
                json_encode(
                    array(
                        "error"=> "No se pudo liberar los resultados: " . pg_last_error()
                    )
                )
            );
        // Cerramos la conexion
        if( $this->conn )
            pg_close($this->conn)
            or die(
                json_encode(
                    array(
                        "error"=> "No se pudo cerrar la conexion: " . pg_last_error()
                    )
                )
            );
    }

}