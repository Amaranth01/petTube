<?php

namespace App\Controller;

class ErrorController extends AbstractController
{
    public function index()
    {
        $this->render('error/404');
    }

    public function error404()
    {
        self::index();
    }
}