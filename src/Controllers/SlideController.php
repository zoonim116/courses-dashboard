<?php

namespace App\Src\Controllers;


use Slim\Http\Request;
use Slim\Http\Response;
use Respect\Validation\Validator;
use Respect\Validation\Exceptions\NestedValidationException;

class SlideController extends BaseController
{

    public function index(Request $request, Response $response, $args){
        $this->render($response, 'lesson/view.twig');
    }

    public function getSlidesByLessonId(Request $request, Response $response, $args) {
        if($request->isXhr()) {
            $route = $request->getAttribute('route');
            $lessonID = $route->getArgument('id');
            $slides = $this->container['db']->select('slides', ['id', 'txt', 'img', 'answer', 'option_1', 'option_2', 'option_3'],
                                                    ['lesson_id' => intval($lessonID), 'ORDER' => ['r_order' => 'ASC']]);
            die(json_encode($slides));
        } else {
            return $response->withStatus(405);
        }
    }

    public function updateSlidesOrder(Request $request, Response $response , $args) {
        if($request->isXhr()) {
            $rows = $request->getParsedBody()['data'];
            foreach ($rows as $row) {
                $this->container['db']->update('slides', [
                    'r_order' => $row['new'],
                ], [
                    "id[=]" => $row['id']
                ]);
            }
            return $response->withStatus(200);
        }
    }


    public function add(Request $request, Response $response, $args) {
        $this->title = 'Add new item';
        $data = [];
        $data['slide'] = ['img' => $request->getUri()->getBaseUrl().'/img/no_image.jpeg'];
        $route = $request->getAttribute('route');
        $lessonID = $route->getArgument('lesson_id');
        $id = $route->getArgument('id') ? $route->getArgument('id') : 0;
        $data['lesson'] = $this->container['db']->get('lessons', ['id', 'name', 'course_id', 'slides_cnt'],
            ['id' => $lessonID]);
        if($request->isPost()) {

            $slideValidator = Validator::key('name', Validator::stringType()->length(2,255));
            try{
                $slideValidator->assert($request->getParsedBody());
            } catch (NestedValidationException $e) {
                $errors = $e->findMessages(array(
                    'name'     => '{{name}} is required',
                ));
            }
            if($slideValidator->validate($request->getParsedBody()) && isset($lessonID) && isset($id)) {
                $prevSlide = $this->container['db']->get('slides', ['r_order', 'img'], ['id' => $id]);
                if ($prevSlide) {
                    $this->reorder($lessonID, $prevSlide['r_order']);
                    $this->create($prevSlide, $lessonID, $request->getParsedBody());
                    $this->container['flash']->addMessage('success', 'Lesson successfully saved.');
                } else {
                    $this->create(0, $lessonID, $request->getParsedBody());
                    $this->container['flash']->addMessage('success', 'Item successfully saved.');
                }
                $data['slide'] = $request->getParsedBody();

            } else {
                $data['errors'] = $errors;
            }
        }
        $this->render($response, 'slide/add.twig', $data);
    }


    /**
     * Reorder r_order column accoring to the new insert
     * @param $lessonID
     * @param $prev
     */
    private function reorder($lessonID, $prev) {
        $slides = $this->container['db']->select('slides', 'id', ["AND" => [ "lesson_id" => $lessonID, "r_order[>]" => $prev ]]);
        $nextPosition = $prev + 2;
        foreach ($slides as $slide) {
            $this->container['db']->update('slides', [
                'r_order' => $nextPosition,
            ], [
                "id[=]" => $slide
            ]);
            $nextPosition++;
        }
    }

    private function create($prevPosition, $lessonID, $data) {
        $data = $this->isQuestion($data);
        $position = 0;
        if($prevPosition['r_order'] == 0) {
            $position = $prevPosition['r_order'] + 1;
        }
        if(isset($data['above'])) {
            $data['img_url'] = $prevPosition['img'] ? $prevPosition['img'] : '';
        }
        $this->container['db']->insert('slides', [
            'txt' => $data['name'],
            'lesson_id' => intval($lessonID),
            'r_order' => intval($position),
            'img' => $data['img_url'],
            'answer' => $data['answer'],
            'option_1' => $data['option_1'],
            'option_2' => $data['option_2'],
            'option_3' => $data['option_3'],
        ]);

    }

    private function isQuestion($data) {
        if(!empty($data['answer'])) {
            $data['img'] = '';
        }
        return $data;
    }

}