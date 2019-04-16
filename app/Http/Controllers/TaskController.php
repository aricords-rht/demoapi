<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Task;
use App\TaskType;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        // Return all tasks owned by the current user, with the most recent tasks first.
        return response()->json(['status' => 'success', 'message' => '', 'payload' => Task::latest()->get()]);
    }

    public function create(Request $request)
    {
        // Verify we got a valid task type.
        $validator = Validator::make($request->all(), [
            'type' => "required|exists:task_types,name",
        ]);
        if ($validator->fails())
        {
            return response()->json(['status' => 'failure', 'message' => 'Invalid value for parameter type', 'payload' => []], 400);
        }

        // Grab that task type, so we can use its ID in the new resource.
        $taskTypes = TaskType::where('task_types.name', '=', $request->type);
        $task = Task::create([
            'user_id'          => $request->user()->id,
            'task_type_id'     => $taskTypes->first()->task_type_id,
            'status'           => 'pending',
            'request_details'  => json_encode($request->request_details, JSON_FORCE_OBJECT),
            'response_details' => null,
        ]);

        // Return the task we just made as a collection.
        return response()->json(['status' => 'success', 'message' => 'Resource Created', 'payload' => [$task]], 201);
    }

    public function read(Request $request, Task $task)
    {
        // The validation inside the Task model will prevent the user from touching anything except their own tasks.
        // Return the task requested as a collection.
        return response()->json(['status' => 'success', 'message' => '', 'payload' => [$task]]);
    }

    public function update(Request $request, Task $task)
    {
        // The validation inside the Task model will prevent the user from touching anything except their own tasks.

        // Start applying requested changes before potential execution.
        if ($request->request_details)
        {
            $task->request_details = json_encode($request->request_details, JSON_FORCE_OBJECT);
        }
        if ($request->status === 'ready')
        {
            // User is requesting to execute the current task.
            $function_name = 'task_' . $task->taskType->name;
            $result = $this->$function_name($task);
            $task->status           = $result['status'];
            $task->response_details = $result['response_details'];
        }

        // Update the model.
        $task->save();

        // Return the updated task as a collection.
	return response()->json(['status' => 'success', 'message' => 'Resource Updated', 'payload' => [$task]], 200);
    }

    public function delete(Request $request, Task $task)
    {
        // The validation inside the Task model will prevent the user from touching anything except their own tasks.
        $task->delete();

        // Return an empty collection.
        return response()->json(['status' => 'success', 'message' => 'Resource Deleted', 'payload' => []], 204);
    }

    // Task execution helper methods.
    private function task_checklist(Task $task)
    {
        return ['status' => 'success', 'response_details' => gmdate('Y-m-d H:i:s+0000')];
    }

    private function task_find_in_string(Task $task)
    {
        $request_details = json_decode($task->request_details, true);
        if (
            isset($request_details['needle'])
            and is_string($request_details['needle'])
            and isset($request_details['haystack'])
            and is_string($request_details['haystack'])
        )
        {
            // TODO: Safely allow pattern meta-characters to be used.
            return ['status' => 'success', 'response_details' => (preg_match('/' . preg_quote($request_details['needle'], '/') . '/', $request_details['haystack']) ? "Found" : "Not Found")];
        }
        return ['status' => 'failure', 'response_details' => 'Invalid needle or haystack properties'];
    }

    private function task_postfix_evaluation(Task $task)
    {
        $request_details = json_decode($task->request_details, true);
        if (
            isset($request_details['formula'])
            and is_array($request_details['formula'])
        )
        {
            $stack = [];
            foreach ($request_details['formula'] as $item)
            {
                if (in_array($item, ['+','-','*','/']))
                {
                    $operand2 = array_pop($stack);
                    $operand1 = array_pop($stack);
                    if (isset($operand1) and isset($operand2))
                    {
                        if ($item == '+')
                        {
                            $stack[] = ($operand1 + $operand2);
                        }
                        elseif ($item == '-')
                        {
                            $stack[] = ($operand1 - $operand2);
                        }
                        elseif ($item == '*')
                        {
                            $stack[] = ($operand1 * $operand2);
                        }
                        else #if ($item == '/')
                        {
                            if ($operand2 == 0)
                            {
                                # Division by zero.
                                return ['status' => 'failure', 'response_details' => 'Division by zero'];
                            }
                            $stack[] = ($operand1 / $operand2);
                        }
                    }
                    else
                    {
                        // Not enough operands to complete the operation.
                        return ['status' => 'failure', 'response_details' => 'Invalid formula: not enough operands to complete operation'];
                    }
                }
                elseif (preg_match('/^-?[0-9]+(?:\.[0-9]+)?$/', $item))
                {
                    // Found a numeric value, save it for later.
                    $stack[] = $item;
                }
                else
                {
                    // Couldn't parse it as an operator or a numeric value.
                    return ['status' => 'failure', 'response_details' => 'Invalid formula: non-operator non-numeric item found in formula'];
                }
            }

            return ['status' => 'success', 'response_details' => array_pop($stack)];
        }
        return ['status' => 'failure', 'response_details' => 'Invalid formula property'];
    }
}

