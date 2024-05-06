<?php

namespace BaseApi\Controllers;


use PDOException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class EjemploController extends BaseController
{
    public function getEjemplo(Request $req, Response $res, $args)
    {
        parent::init($req, $res, $args);

        $con = $req->getAttribute('dbConnection');

        try {
            $data = $con->ejecutarSQL(" ");

            return parent::success('Listado de ejemplo', $data);
        } catch (PDOException $e) {
            return parent::error($e->getMessage(), 500);
        }
    }
}