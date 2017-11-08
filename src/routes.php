<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes
$app->get('/', \App\Src\Controllers\CourseController::class. ':index');
$app->get('/course/all', \App\Src\Controllers\CourseController::class. ':all');
$app->map(['GET', 'POST'],'/course/add', \App\Src\Controllers\CourseController::class. ':add');
$app->map(['GET', 'POST'], '/course/edit/{id}', \App\Src\Controllers\CourseController::class. ':edit');
$app->post('/course/upload', \App\Src\Controllers\CourseController::class. ':upload');



