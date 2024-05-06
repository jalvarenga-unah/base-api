<?php
global $app;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


$app->get('/hello/{name}', [EjemploController::class, "getEjemplo"]);
