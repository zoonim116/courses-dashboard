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

    /**
     * Moves the uploaded file to the upload directory and assigns it a unique name
     * to avoid overwriting an existing uploaded file.
     *
     * @param string $directory directory to which the file is moved
     * @param UploadedFile $uploaded file uploaded file to move
     * @return string filename of moved file
     */
    function moveUploadedFile($directory, UploadedFile $uploadedFile)
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }

}