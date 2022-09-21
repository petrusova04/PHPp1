<?php

namespace wfm;

class Router
{

    protected static array $routes = [];
    protected static array $route = [];

    // Для функций add:
    // тут в $regexp попадает шаблон рег выражению который будет описывать тот или инной URL-адрес
    // в $route попадает тот controller и тот action который необходимо соотнести с данным шаблоном адресом

    public static function add($regexp, $route = [])
    {
        self::$routes[$regexp] = $route;
    }

    public static function getRoutes(): array
    {
        return self::$routes;  // возвр все наши маршруты
    }

    public static function getRoute(): array
    {
        return self::$route;  // возвр конкретный маршрут с которым найденно соответсвие
    }

    public static function dispatch($url)
    {
        if (self::matchRoute($url)) {
            $controller = 'app\controllers\\' . self::$route['admin_prefix'] . self::$route['controller'] . 'Controller';
            if (class_exists($controller)) {
                $controllerObject = new $controller(self::$route);
                $action = self::lowerCamelCase(self::$route['action'] . 'Action');
                if (method_exists($controllerObject, $action)) {
                    $controllerObject->$action();
                } else {
                    throw new \Exception("Метод {$controller}::{$action} не найден", 404);
                }
            } else {
                throw new \Exception("Контроллер {$controller} не найден", 404);
            }

        } else {
            throw new \Exception("Страница не найдена", 404);
        }
    }

    public static function matchRoute($url): bool
    {
        // в $pattern попадает шаблон рег выражения а в $route попадает массив с значениями
        // preg_match — Выполняет проверку на соответствие регулярному выражению
        // в preg_match мы указываем 1 аргументом что мы и мы ищем, 2 - в каком месте мы ишем, 3 - куда мы записываем соответсвие если оно присутсвует

        foreach (self::$routes as $pattern => $route) {
            if (preg_match("#{$pattern}#", $url, $matches)) {
                foreach ($matches as $k => $v) {
                    if (is_string($k)) {
                        $route[$k] = $v;
                    }
                }
                if (empty($route['action'])) {
                    $route['action'] = 'index';
                }
                if (!isset($route['admin_prefix'])) {  // !isset - если у нас не сушествует ...
                    $route['admin_prefix'] = '';
                } else {
                    $route['admin_prefix'] .= '\\';  // \\ - нужны для пространства имен
                }
                $route['controller'] = self::upperCamelCase($route['controller']);
                self::$route = $route;
                return true;
            }
        }
        return false;
    }

    protected static function upperCamelCase($name): string
    {
        // new-product => new product
        $name = str_replace('-', ' ', $name);
        // new product => New Product
        $name = ucwords($name); // upperCaseWords - Каждое слово начинается с большой буквы
        $name = str_replace(' ', '', $name);
        // New Product => NewProduct
        return $name;
    }

    protected static function lowerCamelCase($name): string
    {
        return lcfirst(self::upperCamelCase($name)); // lcfirst - делает первую букву в нижнем регистре
    }


}