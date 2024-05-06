<?php

namespace ATB\config;

use PDO;
use Exception;
use PDOException;

require_once __DIR__ . '/Credenciales.php';

class Database extends PDO
{
    public function __construct()
    {
        try {
            
            //Para conectar a SQL Server
            // parent::__construct("sqlsrv:Server=".HOST.";Database=".DATABASE, USERNAME, PASSWORD);

            //Para conectar a MySQL
            parent::__construct("mysql:host=" . HOST . ";dbname=".DATABASE, USERNAME, PASSWORD);

            $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            // $this->exec("set names utf8");
        } catch (PDOException $e) {
            throw new PDOException("Error de conexión, verifique los datos enviados en la solicitud");
        }
    }

    /**
     * @return bool|array
     *
     * @throws Exception, segun el problema que pueda surgir en la consulta
     * @var string $sql sentencia de SQL para ejecutar
     * @params array $params parametros para la sentencia
     */
    public function ejecutarSQL($sql, $params = []): bool|array
    {
        try {
            $stmt = self::prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception($this->errorHandler($e));
        }
    }

    public function obtenerEscalar($sql, $params = [])
    {
        try {
            $rs = parent::prepare($sql);
            $rs->execute($params);

            //ARRAY DE ARREGLOS ASOCIATIVOS
            $arr = $rs->fetchAll(PDO::FETCH_COLUMN);
            $rows = count($arr);

            if ($rows > 0) {
                return $arr[0];
            } else {
                return null;
            }
        } catch (PDOException $e) {
            throw new Exception($this->errorHandler($e));
        }
    }


    public function escape_string($string): string
    {
        $string = $this->quote($string);
        return substr($string, 1, -1);
    }


    private function errorHandler($e, $error_crudo = false): string
    {
        if ($error_crudo)
            return $e->getMessage();

        $errors = explode(" ", $e->getMessage());
        switch ($e->getCode()) {
            case "23000":
                if (in_array("1451", $errors)) {
                    return "El registro está asociado a otra tabla maestra, no se puede eliminar";
                }
                if (in_array("1452", $errors)) {
                    return "Hay un problema de integridad referencial, verifique que los datos enviados, se puedan relacionar con la información que desea guardar.";
                }
                if (in_array("1048", $errors)) {
                    return "El identificador primario de esta información no puede ser nulo";
                } else if (in_array("1062", $errors)) {
                    $errors = explode("'", $e->getMessage());
                    return "Ya existe un registro con el valor: '{$errors[1]}' para el campo: '{$errors[3]}', este valor debe ser unico";
                } else {
                    return $e->getMessage();
                }
            case "HY093":
                return "Compruebe los datos enviados, hacen falta algunos parametros - " . $e->getMessage();
            case "42S22":
                return "No se reconoce uno o mas de los campos en la consulta- " . $e->getMessage();
            case "21S01":
                return "Los parametros enviados, no corresponden al numero de columas esperado";
            default:
                return $e->getMessage();
        }
    }
}