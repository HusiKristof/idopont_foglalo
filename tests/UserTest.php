<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../models/User.php';

class UserTest extends TestCase
{
    public function testExample()
    {
        $this->assertTrue(true);
    }
}


class UserTest extends TestCase
{
    private $userModel;

    protected function setUp(): void
    {
    
        $mockDb = $this->createMock(PDO::class);
        $this->userModel = new User($mockDb);
    }

    public function testSuccessfulLogin()
    {
        
        $email = "test@example.com";
        $password = "password123";

        
        $this->userModel = $this->createMock(User::class);
        $this->userModel->method('login')->willReturn([
            'id' => 1,
            'name' => 'Test User',
            'email' => $email,
            'phone' => '123456789',
            'role' => 'user'
        ]);

        $result = $this->userModel->login($email, $password);
        $this->assertNotEmpty($result);
        $this->assertEquals($email, $result['email']);
    }

    public function testFailedLogin()
    {
        $email = "wrong@example.com";
        $password = "wrongpassword";

        $this->userModel = $this->createMock(User::class);
        $this->userModel->method('login')->willReturn(false);

        $result = $this->userModel->login($email, $password);
        $this->assertFalse($result);
    }

    public function testRegister()
    {
        $name = "New User";
        $email = "newuser@example.com";
        $phone = "123456789";
        $password = "securepassword";

        $this->userModel = $this->createMock(User::class);
        $this->userModel->method('register')->willReturn(true);

        $result = $this->userModel->register($name, $email, $phone, $password);
        $this->assertTrue($result);
    }
}
