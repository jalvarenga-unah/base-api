<?php

namespace ATB\controllers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Exception;

abstract class BaseController
{
    /**
     * Objeto Response almacenado a nivel de clase, para operar metodos heredados
     * @var Response
     */
    protected Response $response;
    /**
     * Objeto Request almacenado a nivel de clase, para operar metodos heredados
     * @var Request
     */
    protected Request $request;


    public function init(Request $req, Response $res, $args): void
    {
        $this->request = $req;
        $this->response = $res;
    }

    public function getOneByID(Request $req, Response $res, $args)
    {
        $this->request = $req;
        $this->response = $res;
    }

    public function getAll(Request $req, Response $res, $args)
    {
        $this->request = $req;
        $this->response = $res;
    }

    public function createOne(Request $req, Response $res, $args)
    {
        $this->request = $req;
        $this->response = $res;
    }

    public function createMany(Request $req, Response $res, $args)
    {
        $this->request = $req;
        $this->response = $res;
    }

    public function updateOne(Request $req, Response $res, $args)
    {
        $this->request = $req;
        $this->response = $res;
    }

    public function deleteOne(Request $req, Response $res, $args)
    {
        $this->request = $req;
        $this->response = $res;
    }


    /**
     * Retorna una respuesta positiva con un código de estatus 200, y una estructura JSON
     *
     * @param mixed|null $data Puede ser un Array, Object o cualquier tipo de datos primitivo
     * @param string $message Mensaje que se desea enviar como respuesta
     * @param int $statusCode codigo http default 200
     *
     */

    protected function success(?string $message, mixed $data = null, int $statusCode = 200): Response
    {
        $this->response->getBody()->write(json_encode([
            "success" => true,
            "message" => $message,
            "data" => $data
        ]));

        return $this->response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
    }


    /**
     * Retorna una respuesta negativao con un código de estatus 4xx o 5xx, y una estructura JSON
     *
     * @param string $message String Mensaje de error que se desea enviar
     * @param int $statusCode codigo de error http default 400
     *
     */
    protected function error(?string $message, int $statusCode = 400): Response
    {
        $this->response->getBody()->write(json_encode([
            "success" => false,
            "message" => $message,
            "data" => null
        ]));

        return $this->response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
    }

    protected function getIP(): bool|array|string
    {
        if (getenv("HTTP_CLIENT_IP"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if (getenv("HTTP_X_FORWARDED_FOR"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if (getenv("REMOTE_ADDR"))
            $ip = getenv("REMOTE_ADDR");
        else
            $ip = "UNKNOWN";

        return $ip;
    }

    /**
     * Valida que los datos enviados en la petición sean igual a los campos permitidos
     * @param array $allowedFields Listado de campos permtidos
     * @param array|null $body Arreglo clave=>Valor con los campos enviados en la petición
     *
     * @return boolean TRUE: Los campos son validos. FALSE: Faltan campos o vienen campos extra
     */
    protected function validateFileds(array $allowedFields, array $body)
    {
        return count(array_intersect($allowedFields, array_keys($body))) == count($allowedFields) && count($allowedFields) == count($body);
    }

    protected function getQueryFilters(array $allowedFields): array
    {
        if (is_null($this->request)) {
            throw new Exception("No se ha cargado el objeto Request a nivel global");
        }
        $queryParams = $this->request->getQueryParams();

        return array_filter($queryParams, function ($key) use ($allowedFields) {
            return in_array($key, $allowedFields);
        }, ARRAY_FILTER_USE_KEY);
    }

    protected function getDataFromToken(Request $request): ?\stdClass
    {
        $header = $request->getHeaderLine('Authorization');
        if (preg_match('/Bearer\s+(.*)$/i', $header, $matches)) {
            return JWT::decode($matches[1], new Key(SECRET_KEY, 'HS256'));
        }
        return null;
    }
}