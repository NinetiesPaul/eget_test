<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
use Database\Factories\UserFactory;

class UserTest extends TestCase
{
    protected static $user;

    public function test_user_can_register()
    {
        $userFactory = new UserFactory();
        $user = (object) $userFactory->definition();
        self::$user = $user;

        $registerResponse = $this->post('/api/register', [ 'name' => $user->name, 'email' => $user->email, 'password' => 'password' ]);
        $registerResponse->assertStatus(200);
    }

    public function test_user_can_register_and_can_login()
    {
        $user = self::$user;
        $this->post('/api/register', [ 'name' => $user->name, 'email' => $user->email, 'password' => 'password' ]);

        $loginResponse = $this->post('/api/login', [ 'email' => $user->email, 'password' => 'password' ]);
        $loginResponse->assertStatus(200);
    }
}
