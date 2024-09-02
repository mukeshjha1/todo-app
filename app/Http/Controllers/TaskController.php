<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all();
        return view('tasks.index', compact('tasks'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]); 
        
        $existingTask = Task::where('name', $request->name)->first();
        if ($existingTask) {
            return response()->json(['success' => false, 'message' => 'Task already exists.'], 400);
        }

        $task = Task::create([
            'name' => $validatedData['name'],
            'status' => 0,  
        ]);

        return response()->json(['success' => true,'task' => $task]);

         
    }

    public function update(Request $request, $id)
    {
        $task = Task::find($id);  
        if (!$task) {
            return response()->json(['success' => false, 'message' => 'Task not found.'], 404);
        } 
        $task->status = $request->status;
        $task->save();

        return response()->json(['success' => true]);
    }

    public function destroy(Task $task)
    {
        $task->delete(); 
        return response()->json(['success' => true]);
    }
}
