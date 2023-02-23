<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\Models\Tasks;
use App\Jobs\MailJob;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use App\Rules\StatusValidation;
use App\Rules\UserExistsValidation;
use App\Rules\OwnerValidation;

class TaskController extends Controller
{
    protected $user;
 
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function select(Request $request)
    {
        $filters = [];

        if ($request->query('status')) {
            $filters["status"] = $request->query('status');
        }

        if ($request->query('owner')) {
            $filters["owner"] = $request->query('owner');
        }

        $validator = Validator::make($filters, [
            'status' => [
                'string',
                new StatusValidation
            ],
            'owner' => [
                'string',
                new OwnerValidation
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        if (empty($filters)) {
            $tasks = Tasks::with('user')->get();
        } else {
            $queryFilters = [];

            if (!empty($filters["status"])){
                $queryFilters[] = ["current_status", $filters["status"]];
            }

            if (!empty($filters["owner"])){
                if ($filters["owner"] == "me"){
                    $queryFilters[] = [ "user_id", $this->user->id ];
                }

                if ($filters["owner"] == "others"){
                    $queryFilters[] = [ "user_id", "!=", $this->user->id ];
                }
            }

            $tasks = Tasks::with('user')->where($queryFilters)->get();
        }

        $response = [
            'success' => true,
            'message' => 'No task found with matching id'
        ];

        if ($tasks) {
            unset($response['message']);
            $response['data'] = $tasks;
        }

        return response()->json($response, ($tasks) ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
    }

    public function create(Request $request)
    {
        $data = $request->only('title', 'description');

        $validator = Validator::make($data, [
            'title' => 'required|string',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        $task = Tasks::create([
            'title' => $request->title,
            'description' => $request->description,
            'started_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'user_id' => $this->user->id,
            'current_status' => Tasks::STATUS_ON_GOING
        ]);

        return response()->json([
            'success' => true,
            'data' => $task
        ], Response::HTTP_OK);
    }
    
    public function update(Request $request, int $taskId)
    {
        $data = $request->only('title', 'description', 'status', 'user_id');

        $validator = Validator::make($data, [
            'title' => 'string',
            'description' => 'string',
            'status' => [
                'string',
                new StatusValidation
            ],
            'user_id' => [
                'integer',
                new UserExistsValidation
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        $columns = [];

        if ($request->title) {
            $columns['title'] = $request->title;
        }

        if ($request->description) {
            $columns['description'] = $request->description;
        }

        if ($request->user_id) {
            $columns['user_id'] = $request->user_id;
        }

        if ($request->status) {
            $columns['current_status'] = $request->status;
        }

        $task = Tasks::where('id', $taskId)
            ->update($columns);

        $response = [
            'success' => true,
            'message' => 'No task found with matching id'
        ];

        if ($task) {
            $response['message'] = 'Task updated';

            if ($request->user_id) {     
                $task = Tasks::with('user')->where('id', $taskId)->first();

                $emailJobs = new MailJob($task->user->email, $taskId);
                $this->dispatch($emailJobs);
            }

            if (($request->status) && $request->status == Tasks::STATUS_CLOSED ) {     
                $task = Tasks::with('user')->where('id', $taskId)->first();

                $emailJobs = new MailJob($task->user->email, $taskId, true);
                $this->dispatch($emailJobs);
            }
        }

        return response()->json($response, ($task) ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
    }
    
    public function delete($taskId)
    {
        $task = Tasks::where('id', $taskId)
            ->delete();

        $response = [
            'success' => true,
            'message' => 'No task found with matching id'
        ];

        if ($task) {
            $response['message'] = 'Task deleted';
        }

        return response()->json($response, ($task) ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
    }
}
