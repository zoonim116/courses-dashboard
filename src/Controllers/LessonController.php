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

    public function add(Request $request, Response $response, $args) {
        $this->title = 'Add new item';
        $route = $request->getAttribute('route');
        $courseId = $route->getArgument('id');

        if($request->isPost()) {
            $courseValidator = Validator::key('name', Validator::stringType()->length(2,255));

            try{
                $courseValidator->assert($request->getParsedBody());
            } catch (NestedValidationException $e) {
                $errors = $e->findMessages(array(
                    'name'     => '{{name}} is required',
                ));
            }
            if($courseValidator->validate($request->getParsedBody()) && isset($courseId) && !empty($courseId) && intval($courseId)) {
                $data = $request->getParsedBody();
                $this->container['db']->insert('lessons', [
                    'name' => $data['name'],
                    'course_id' => intval($courseId),
                    'slides_cnt' => 0,
                ]);
                $this->container['flash']->addMessage('success', 'Lesson successfully saved.');
            } else {
                $data['errors'] = $errors;
            }
        }

        $data['course'] = $this->container['db']->get('courses', ['id', 'name', 'category', 'img', 'author', 'description'],
            ['id' => $courseId]);

        $this->render($response, 'lesson/add.twig', $data);
    }

}