<?php
require_once "../models/User.php";

class UserService
{

    static function register(User $user){
        $name = $user->getName();
        $email = $user->getEmail();
        $phone = $user->getPhone();
        $password = $user->getPassword();

        if ($name && $email && $phone && $password) {
            $hashedPassword = password_hash($password,PASSWORD_DEFAULT);
            $user->setPassword($hashedPassword);
            $modelResult = User::registerUser($user);
            

            if ($modelResult) {
                return [
                    'status' => 200,
                    'message' => 'User Registered',
                ];

            } else {
                return [
                    'status' => 500,
                    'message' => 'Registration failed',
                ];
            }

        } else {
            return [
                'status' => 417,
                'message' => 'Missing Credencials',
            ];
        }
    }

    static function login(User $user){
        $email = $user->getEmail();
        $password = $user->getPassword();

        if ($email && $password) {

            if ($user && password_verify($password, password_hash($password,PASSWORD_DEFAULT))) {
                return [
                    'status' => 200,
                    'message' => 'Login Successful',
                    'user' => $user,
                ];
            } else {
                return [
                    'status' => 401,
                    'message' => 'Invalid credentials',
                ];
            }
        } else {
            return [
                'status' => 417,
                'message' => 'Missing Credentials',
            ];
        }
    }
}
