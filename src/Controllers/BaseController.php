<?php

namespace App\Src\Controllers;


use Psr\Http\Message\ResponseInterface;
use Slim\Views\Twig;

class BaseController
{
    protected $title = "";
    protected $container;

    public function __construct($container){
        $this->container = $container;
    }

    public function render(ResponseInterface $response, $template, $data = []) {
        $output = $data;
        $output['title'] = $this->title;
        $this->container['view']->render($response, $template, $output);
    }
}