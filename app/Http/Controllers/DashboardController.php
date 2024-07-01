<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class DashboardController extends Controller
{
    public function index()
    {
        $tasks = Task::all();
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
