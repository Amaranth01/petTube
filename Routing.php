<?php

use App\Controller\AbstractController;
use App\Controller\ErrorController;

class Routing
{
    /**
     * Getting parameters from $_GET
     * @param string $key
     * @return string|null
     */
    private static function getParam(string $key, $default = null): ?string
    {
        if (isset($_GET[$key])) {
            return filter_var($_GET[$key], FILTER_SANITIZE_STRING);
        }
        return $default;
    }

    /**
     * Avoid errors in URL parameters
     * @param AbstractController $controller
     * @param string|null $action
     * @return string|null
     */
    private static function method(AbstractController $controller, ?string $action): ?string
    {
        //Replace spaces with thirds
        if (strpos($action, '-') !== -1) {
            $action = array_reduce(explode('-', $action), function ($ac, $a){
               return $ac . ucfirst($a);
            });
        }

        //change uppercase letters to lowercase
        $action = lcfirst($action);
        if (method_exists($controller, $action)) {
            return $action;
        }
        return null;
    }

    /**
     * Check that the controllers are correct, if not, redirect to an error page.
     * @param string $controller
     * @return ErrorController|mixed
     */
    private static function controller(string $controller)
    {
        $controller = "App\Controller\\" . ucfirst($controller) . 'Controller';
        if (class_exists($controller)) {
            return new $controller();
        }
        return new ErrorController();
    }

    public static function route()
    {
        //Initialize tne 'c' parameter
        $paramController = self::getParam('c', 'home');
        $action = self::getParam('a');
        $id = self::getParam('id');
        $token = self::getParam('token');
        $controller = self::controller($paramController);

        //Returns the error page if the controller is not found, and we quit the script
        if($controller instanceof ErrorController) {
            $controller->error404();
            exit();
        }

        //Verification of the presence of controller
        $action = self::method($controller, $action);
        //Checks if a controller id is needed
        if($action !== null) {
            if ($id !== null) {
                if($token !== null) {
                    $controller->$action($id, $token);
                }
                else {
                    $controller->$action($id);
                }
            }
            else {
                $controller->$action();

            }
        }
        else {
            $controller->index();
        }
    }
}