<?php

require '../vendor/autoload.php';
require '../src/config/env.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Firebase\JWT\JWT;
use \Tuupola\Base62;

$app = new \Slim\App([
  'settings' => [
    'displayErrorDetails' => true
  ]
]);

// $app->add(new \Slim\Middleware\JwtAuthentication([
//     "path" => "/api",
//     "passthrough" => "/api/auth",
//     "secret" => "bobesponja56"
// ]));

$app->add(function ($request, $response, $next) {
    $response = $next($request, $response);
    return $response->withHeader('Content-Type', 'application/json')
  ->withHeader('Access-Control-Allow-Origin', '*')
  ->withHeader('Access-Control-Allow-Headers', array('Content-Type', 'X-Requested-With', 'Authorization'))
  ->withHeader('Access-Control-Allow-Methods', array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'));
});

require '../src/routes/tables.php';
$app->run();
