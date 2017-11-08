<?php
namespace App\Src\Controllers;
use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;


class CourseController extends BaseController
{

    /**
     * Get list of courses
     * @param Request $request
     * @param Response $response
     * @param $args
     */
    public function index(Request $request, Response $response, $args) {
        $this->title = "List of courses";
        $data['categories'] = $this->container['db']->select('courses', '*');

        $this->render($response,'course/index.twig', $data);
    }

    public function all(Request $request, Response $response, $args) {
        if($request->isXhr()) {
            $data = $this->container['db']->select('courses', ["[>]categories" => ["category" => "id"]], [
                'courses.id',
                'courses.name',
                'categories.name(category)',
                'courses.author',
            ]);
            die(json_encode($data));
        } else {
            return $response->withStatus(404);
        }


    }

    public function edit(Request $request, Response $response, $args) {

        $route = $request->getAttribute('route');
        $courseId = $route->getArgument('id');
        $data['course'] = $this->container['db']->get('courses', ['name', 'category', 'img', 'author', 'description'], ['id' => $courseId]);
        $data['categories'] = $this->container['db']->select('categories', ['id', 'name']);
        $this->title = 'Edit: '.$data['course']['name'];
        $this->render($response, 'course/edit.twig', $data);
    }

    public function add(Request $request, Response $response, $args) {
        if($request->isPost()) {

        }
        $data = [];
        $this->render($response, 'course/edit.twig', $data);
    }

    public function __invoke($request, $response, $args) {
        return $response;
    }
}