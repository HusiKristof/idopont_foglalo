<?php

require_once "Request.php";
require_once "Response.php";

class Router{

    private static $routes = [];
    
    public static function get($endpoint,$callback){
        self::$routes["GET"][$endpoint] = $callback;
    }

    public static function post($endpoint,$callback){
        self::$routes["POST"][$endpoint] = $callback;
    }

    public static function delete($endpoint,$callback){
        self::$routes["DELETE"][$endpoint] = $callback;
    }

    public static function handleRequest(){
        $method = $_SERVER["REQUEST_METHOD"];
        $uri = $_SERVER["REQUEST_URI"];
        $parsedUrl = parse_url($uri);

        $request = Request::getInstance($parsedUrl["path"],$method);
        $response = Response::getInstance();
       
        var_dump($request->getQueryParams());
        if(self::$routes[$method][$parsedUrl["path"]]){
            $callback = self::$routes[$method][$parsedUrl["path"]];
            $callback($request,$response);
        }else{
            $response->setHtmlStatus(404);
            $response->setMessage("Endpoint not found");
        }
        
        return $response;
    }


}