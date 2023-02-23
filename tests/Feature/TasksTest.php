<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Database\Factories\UserFactory;

class TasksTest extends TestCase
{
    protected static $JwtToken;

    protected static $createdTask;

    public function test_user_can_post_new_task()
    {
        $bearerToken = $this->getJwtToken();

        $postTaskResponse = $this->withHeader('Authorization', $bearerToken)
            ->post('/api/task', [ 'title' => 'Task title', 'description' => 'Task description' ]);

        self::$createdTask = $postTaskResponse;

        $postTaskResponse->assertStatus(200);
    }

    public function test_user_can_query_tasks()
    {
        $bearerToken = $this->getJwtToken();

        $getTasksResponse = $this->withHeader('Authorization', $bearerToken)
            ->get('/api/tasks');

        $getTasksResponse->assertStatus(200);
    }

    public function test_user_can_update_task()
    {
        $bearerToken = $this->getJwtToken();

        $postTaskResponse = self::$createdTask;

        $putTaskResponse = $this->withHeader('Authorization', $bearerToken)
            ->put('/api/task/' . $postTaskResponse['data']['id'], [ 'title' => 'New title' ]);

        $putTaskResponse->assertStatus(200);
    }

    public function test_user_can_delete_a_task()
    {
        $bearerToken = $this->getJwtToken();

        $postTaskResponse = self::$createdTask;

        $deleteTaskResponse = $this->withHeader('Authorization', $bearerToken)
            ->delete('/api/task/' . $postTaskResponse['data']['id']);

        $deleteTaskResponse->assertStatus(200);
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
