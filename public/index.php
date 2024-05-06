<?php

use ATB\middlewares\DataBaseMiddleware;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();
$app->setBasePath('/base-api'); // * IMPORTANTE

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

$app->add(new DataBaseMiddleware());

// Middleware para manejar errores
//TODO: cambiar a false en produccion
$app->addErrorMiddleware(true, true, true);

//* Importar las rutas
require_once __DIR__ . '/../src/router/Routes.php';

$app->run();