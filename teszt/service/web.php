<?php
require_once "Router.php";
require_once '../controlller/UserController.php';

Router::get("/",function($request,$response){
    //We are home
    require_once '../view/landing.php';
});

Router::post("/register",function($request,$response){
    $response = UserController::registerUser($request);
    $response = Router::handleRequest();
});

$response = Router::handleRequest();

