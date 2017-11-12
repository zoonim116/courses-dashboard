<?php

namespace App\Src\Controllers;
use Respect\Validation\Exceptions\NestedValidationException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Respect\Validation\Validator;

class LessonController extends BaseController {

    public function getLessonByCourseId(Request $request, Response $response, $args) {
        if($request->isXhr()) {
            $route = $request->getAttribute('route');
            $courseId = $route->getArgument('id');
            $result = $this->container['db']->select('lessons', ['id', 'name', 'slides_cnt(slides)'], ['course_id' => $courseId]);
            die(json_encode($result));
        } else {
            return $response->withStatus(405);
        }
    }

}