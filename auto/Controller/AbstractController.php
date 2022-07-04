<?php

namespace App\Controller;

abstract class AbstractController
{

    abstract public function index();

    public function render(string $template, array $data = [])
    {
        ob_start();
        require __DIR__ . '/../../View' . $template . '.html.php';
        $html = ob_get_clean();
        require __DIR__ . '/../../View/base.html.php';
        exit;
    }
}