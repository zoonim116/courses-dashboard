<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes
$app->get('/', \App\Src\Controllers\CourseController::class. ':index');
$app->get('/course/all', \App\Src\Controllers\CourseController::class. ':all');
$app->get('/course/add', \App\Src\Controllers\CourseController::class. ':add');
$app->get('/course/edit/{id}', \App\Src\Controllers\CourseController::class. ':edit');

//$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
//    // Sample log message
//    $this->logger->info("Slim-Skeleton '/' route");
//
//
//
//    return $response->write(json_encode($data));
//
//    // Render index view
//    return $this->renderer->render($response, 'index.phtml', $args);
//});


