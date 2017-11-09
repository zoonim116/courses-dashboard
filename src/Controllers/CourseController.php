<?php
namespace App\Src\Controllers;
use Respect\Validation\Exceptions\NestedValidationException;
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
        $this->render($response,'course/index.twig');
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return static
     */
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

    /**
     * Edit Course
     * @param Request $request
     * @param Response $response
     * @param $args
     */
    public function edit(Request $request, Response $response, $args) {
        $route = $request->getAttribute('route');
        $courseId = $route->getArgument('id');
        $data['course'] = $this->container['db']->get('courses', ['id', 'name', 'category', 'img', 'author', 'description'], ['id' => $courseId]);
        $data['categories'] = $this->container['db']->select('categories', ['id', 'name']);
        $this->title = 'Edit: '.$data['course']['name'];
        if($request->isPost()) {
            $courseValidator = Validator::key('name', Validator::stringType()->length(2,255))
                                ->key('description', Validator::stringType()->length(2,255))
                                ->key('author', Validator::stringType()->length(2,255))
                                ->key('img_url', Validator::url())
                                ->key('category', Validator::numeric());
            try{
                $courseValidator->assert($request->getParsedBody());
            } catch (\InvalidArgumentException $e) {
                $errors = $e->findMessages(array(
                    'name'     => '{{name}} is required',
                    'description' => '{{name}} is required',
                    'author' => '{{name}} is required',
                    'img_url' => '{{name}} is required',
                    'category' => '{{name}} is required',
                ));
            }
            if($courseValidator->validate()) {
                $this->container['db']->insert('courses', [

                ]);
            } else {
                $data['errors'] = $errors;
            }
        }
        $this->render($response, 'course/edit.twig', $data);
    }


    /**
     * Upload img for add/edit action
     * @param Request $request
     * @param Response $response
     * @param $args
     */
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

    /**
     * Add Course
     * @param Request $request
     * @param Response $response
     * @param $args
     */

    public function add(Request $request, Response $response, $args) {
        $this->title = 'Add new item';
        if($request->isPost()) {
//            echo "<pre>";
//            die(var_dump($request->getParsedBody()));
            $courseValidator = Validator::key('name', Validator::stringType()->length(2,255))
                                ->key('description', Validator::stringType()->length(1,255))
                                ->key('author', Validator::stringType()->length(1,100))
                                ->key('img_url', Validator::url())
                                ->key('category', Validator::numeric());

            try{
                $courseValidator->assert($request->getParsedBody());
            } catch (NestedValidationException $e) {
                echo "<pre>";
                die(var_dump($e->getMessages()));
                $errors = $e->findMessages(array(
                    'name'     => '{{name}} is required',
                    'description' => '{{name}} is required',
                    'author' => '{{name}} is required',
                    'img_url' => '{{name}} is required',
                    'category' => '{{name}} is required',
                ));
            }
            if($courseValidator->validate($request->getParsedBody())) {
                $data = $request->getParsedBody();
                $this->container['db']->insert('courses', [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'author' => $data['author'],
                    'img' => $data['img_url'],
                    'category' => $data['category']
                ]);
            } else {
                $data['errors'] = $errors;
            }
            $data['course'] = $request->getParsedBody();
        } else {
            $data['course'] = ['img' => $request->getUri()->getBaseUrl().'/img/no_image.jpeg'];
        }

        $data['categories'] = $this->container['db']->select('categories', ['id', 'name']);

        $this->render($response, 'course/add.twig', $data);
    }

    public function __invoke($request, $response, $args) {
        return $response;
    }


}