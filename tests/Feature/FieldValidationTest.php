<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Database\Factories\UserFactory;

class FieldValidationTest extends TestCase
{
    protected static $JwtToken;

    protected static $createdTask;

    protected static $userId;

    public function test_title_and_description_are_required_fields_on_post()
    {
        $bearerToken = $this->getJwtToken();

        $postTaskResponse = $this->withHeader('Authorization', $bearerToken)
            ->post('/api/task', []);

        $postTaskResponse->assertStatus(400);
    }

    public function test_status_rule_is_enforced_on_get()
    {
        $bearerToken = $this->getJwtToken();

        $getTaskResponse = $this->withHeader('Authorization', $bearerToken)
            ->get('/api/tasks?status=foobar');

        $getTaskResponse->assertStatus(400);
    }

    public function test_owner_rule_is_enforced_on_get()
    {
        $bearerToken = $this->getJwtToken();

        $getTaskResponse = $this->withHeader('Authorization', $bearerToken)
            ->get('/api/tasks?owner=foobar');

        $getTaskResponse->assertStatus(400);
    }

    public function test_status_rule_is_enforced_on_put()
    {
        $bearerToken = $this->getJwtToken();

        $createdTask = $this->getCreatedTask();

        $putTaskResponse = $this->withHeader('Authorization', $bearerToken)
            ->put('/api/task/' . $createdTask['data']['id'], [ 'status' => 'foobar' ] );

        $putTaskResponse->assertStatus(400);
    }

    public function test_user_id_rule_is_enforced_on_put()
    {
        $bearerToken = $this->getJwtToken();

        $createdTask = $this->getCreatedTask();

        $putTaskResponse = $this->withHeader('Authorization', $bearerToken)
            ->put('/api/task/' . $createdTask['data']['id'], [ 'user_id' => random_int(1, 10000) ] );

        $putTaskResponse->assertStatus(400);
    }

    private function getCreatedTask()
    {
        if (self::$createdTask) {
            return self::$createdTask;
        } else {
            $bearerToken = $this->getJwtToken();

            $postTaskResponse = $this->withHeader('Authorization', $bearerToken)
                ->post('/api/task', [ 'title' => 'Task title', 'description' => 'Task description' ]);

            self::$createdTask = $postTaskResponse;

            return self::$createdTask;
        }

        
    }

    private function getJwtToken()
    {
        if (self::$JwtToken) {
            return self::$JwtToken;
        } else {
            $userFactory = new UserFactory();
            $user = (object) $userFactory->definition();
            $this->post('/api/register', [ 'name' => $user->name, 'email' => $user->email, 'password' => 'password' ]);

            $loginResponse = $this->post('/api/login', [ 'email' => $user->email, 'password' => 'password' ]);
            self::$JwtToken = "Bearer " . $loginResponse['token'];

            return self::$JwtToken;
        }
    }
}
