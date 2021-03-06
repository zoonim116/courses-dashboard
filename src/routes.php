<?php

// Routes
$app->get('/', \App\Src\Controllers\CourseController::class. ':index');
$app->get('/course/all', \App\Src\Controllers\CourseController::class. ':all');
$app->map(['GET', 'POST'],'/course/add', \App\Src\Controllers\CourseController::class. ':add');
$app->map(['GET', 'POST'], '/course/edit/{id}', \App\Src\Controllers\CourseController::class. ':edit');
$app->post('/course/upload', \App\Src\Controllers\CourseController::class. ':upload');
$app->get('/course/view/{id}', \App\Src\Controllers\CourseController::class. ':view');
$app->get('/lesson/by-course/{id}', \App\Src\Controllers\LessonController::class. ':getLessonByCourseId');
$app->map(['GET', 'POST'], '/lesson/add/{id}', \App\Src\Controllers\LessonController::class. ':add');
$app->map(['GET', 'POST'], '/lesson/edit/{id}', \App\Src\Controllers\LessonController::class. ':edit');
$app->map( ['GET', 'POST'], '/slides/add/{lesson_id}/[{id}]', \App\Src\Controllers\SlideController::class. ':add');
$app->get( '/slides/{id}', \App\Src\Controllers\SlideController::class. ':index');
$app->map(['GET', 'POST'], '/slides/edit/{id}', \App\Src\Controllers\SlideController::class. ':edit');
$app->get('/slides/delete/{id}', \App\Src\Controllers\SlideController::class. ':delete');
$app->post( '/slides/new-order', \App\Src\Controllers\SlideController::class. ':updateSlidesOrder');
$app->get( '/slides/by-lesson/{id}', \App\Src\Controllers\SlideController::class. ':getSlidesByLessonId');




