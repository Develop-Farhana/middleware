<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Retrieve tasks associated with the logged-in user
        $user = Auth::user();
        $tasks = Task::where('user_id', $user->id)->get();
    
        return view('dashboard', ['tasks' => $tasks]);
    }

    public function updateStatus(Request $request)
    {
        $validatedData = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'status' => 'required|in:pending,done',
        ]);

        try {
            $task = Task::findOrFail($validatedData['task_id']);
            $task->status = $validatedData['status'];
            $task->save();

            return response()->json(['success' => true, 'task' => $task]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update task status.']);
        }
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'task' => 'required|string|max:255',
            'status' => 'nullable|in:pending,done',
        ]);

        try {
            $user = Auth::user();
            $task = new Task();
            $task->task = $validatedData['task'];
            $task->status = $validatedData['status'] ?? 'pending';
            $task->user_id = $user->id;
            $task->save();

            return response()->json(['success' => true, 'task' => $task]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to add task.']);
        }
    }
}
