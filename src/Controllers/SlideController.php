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

}