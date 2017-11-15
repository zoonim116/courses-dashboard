<?php

namespace App\Src\Controllers;


use Slim\Http\Request;
use Slim\Http\Response;

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
        if($request->isPost()) {
            $route = $request->getAttribute('route');
            $lessonID = $route->getArgument('lesson_id');
            $id = $route->getArgument('id');
            $prevSlide = $this->container['db']->get('slides', 'r_order', ['id' => $newID]);
//            $this->reorder($lessonID, $id, $prevSlide);
            $this->create($prevSlide, $request->getParsedBody());

        }
        $this->render($response, 'slide/add.twig', $data);
    }


    /**
     * Reoder slides according to the new insert ID
     * @param $lessonID
     * @param $newID
     */
    private function reorder($lessonID, $newID, $prev) {
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

    private function create($prevPosition, $data) {
        echo "<pre>";
        die(var_dump($data));
    }

    private function getAboveImg() {

    }

    private function isQuestion($data) {
        if(!empty($data['question']) && !empty($data['answer'])) {
            $data['img'] = '';
        }
        return $data;
    }

}