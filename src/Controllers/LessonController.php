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
            $lessons = $this->container['db']->select('lessons', ['id', 'name', 'slides_cnt(slides)'], ['course_id' => $courseId]);
            foreach ($lessons as $key => $lesson) {
                $lessons[$key]['questions'] = $this->container['db']->count('slides',['AND' => [
                    'answer[!]' => '',
                    'lesson_id' => $lesson['id']
                ]]);
            }
            die(json_encode($lessons));
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

    public function edit(Request $request, Response $response, $args) {
        $route = $request->getAttribute('route');
        $lessonID = $route->getArgument('id');
        $data['lesson'] = $this->container['db']->get('lessons', ['id', 'name', 'course_id', 'slides_cnt'],
                                                    ['id' => $lessonID]);
        if($request->isPost()) {
            $lessonValidator = Validator::key('name', Validator::stringType()->length(2,255));

            try{
                $lessonValidator->assert($request->getParsedBody());
            } catch (NestedValidationException $e) {
                $errors = $e->findMessages(array(
                    'name'     => '{{name}} is required',
                ));
            }
            if($lessonValidator->validate($request->getParsedBody()) && isset($lessonID) && !empty($lessonID) && intval($lessonID)) {
                $input = $request->getParsedBody();
                $this->container['db']->update('lessons', [
                    'name' => $input['name'],
                ], [
                    "id[=]" => $lessonID
                ]);
                $data['lesson']['name'] = $input['name'];
                $this->container['flash']->addMessage('success', 'Lesson successfully saved.');
            } else {
                $data['errors'] = $errors;
            }
        }

        $this->title = 'Edit: '.$data['course']['name'];
        $data['course'] = $this->container['db']->get('courses', ['id', 'name', 'category', 'img', 'author', 'description'],
            ['id' => $data['lesson']['course_id']]);

        $this->render($response, 'lesson/edit.twig', $data);
    }

    public function delete(Request $request, Response $response, $args) {
        $route = $request->getAttribute('route');
        $lessonID = $route->getArgument('id');
        $this->container['db']->delete('lessons', ['AND' => ['id' => $lessonID]]);
        return $response->withRedirect('');
    }

}