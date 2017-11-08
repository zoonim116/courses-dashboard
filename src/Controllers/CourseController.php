<?php
namespace App\Src\Controllers;
use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Respect\Validation\Validator;


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
            return $response->withStatus(405);
        }
    }

    public function edit(Request $request, Response $response, $args) {
        $route = $request->getAttribute('route');
        $courseId = $route->getArgument('id');
        $data['course'] = $this->container['db']->get('courses', ['id', 'name', 'category', 'img', 'author', 'description'], ['id' => $courseId]);
        $data['categories'] = $this->container['db']->select('categories', ['id', 'name']);
        $this->title = 'Edit: '.$data['course']['name'];
        if($request->isPost()) {
            $courseValidator = Validator::key('name', Validator::stringType()->length(100,255))
                                ->key('description', Validator::stringType()->length(200,255))
                                ->key('category', Validator::numeric());
            try{
                $courseValidator->assert($request->getParsedBody());
            } catch (\InvalidArgumentException $e) {
                $errors = $e->findMessages(array(
                    'name'     => '{{name}} is required',
                    'description'     => '{{name}} is required',
                ));
            }
            if($courseValidator->validate()) {
                die('ok');
            } else {
                $data['errors'] = $errors;
            }
        }
        $this->render($response, 'course/edit.twig', $data);
    }

    public function upload(Request $request, Response $response, $args) {
        $directory = $this->container['upload_directory'];

        $uploadedFiles = $request->getUploadedFiles();

        // handle single input with single file upload
        $uploadedFile = $uploadedFiles['img'];
        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $filename = $this->moveUploadedFile($directory, $uploadedFile);
            $url = $request->getUri()->getBaseUrl().'/uploads/'.$filename;
            $response->write(json_encode(['url' => $url]));
        }
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