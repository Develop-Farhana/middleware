<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class DashboardController extends Controller
{
    public function index()
    {
        // Assuming you are using Laravel's built-in authentication system
        $user = auth()->user();
    
        // Retrieve tasks associated with the logged-in user
        $tasks = Task::where('user_id', $user->id)->get();
    
        return view('dashboard', ['tasks' => $tasks]);
    }

    public function updateStatus(Request $request)
    {
        $task = Task::find($request->task_id);
        if ($task) {
            $task->status = $request->status;
            $task->save();
            return response()->json(['success' => true, 'status' => $task->status, 'task' => $task]);
        }
        return response()->json(['success' => false, 'message' => 'Task not found.']);
    }

    public function store(Request $request)
    {
        $task = Task::create($request->all());
        return response()->json(['success' => true, 'task' => $task]);
    }
}
