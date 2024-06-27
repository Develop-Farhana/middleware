<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.api');
    }

    public function addTask(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'task' => 'required|string|max:255',
        ]);

        $task = Task::create([
            'user_id' => $validated['user_id'],
            'task' => $validated['task'],
            'status' => 'pending',
        ]);

        return response()->json([
            'task' => $task,
            'status' => 1,
            'message' => 'Successfully created a task'
        ]);
    }

    public function updateStatus(Request $request)
    {
        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'status' => 'required|in:pending,done',
        ]);

        $task = Task::find($validated['task_id']);
        $task->status = $validated['status'];
        $task->save();

        return response()->json([
            'task' => $task,
            'status' => 1,
            'message' => 'Marked task as ' . $validated['status']
        ]);
    }

    public function getAllTasks()
    {
        $tasks = Task::all();

        return response()->json([
            'tasks' => $tasks,
            'status' => 1,
            'message' => 'Successfully retrieved all tasks'
        ]);
    }
}
