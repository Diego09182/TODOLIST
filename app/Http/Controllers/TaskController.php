<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with('lists')->paginate(5);
        
        return Inertia::render('TodoList', [
            'tasks' => $tasks,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:jpg,png,pdf,docx,txt|max:2048',
        ]);

        $filePath = null;

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('tasks', 'public');
        }

        Task::create([
            'title' => $request->title,
            'file_path' => $filePath,
        ]);

        return redirect()->back();
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:jpg,png,pdf,docx|max:2048',
        ]);

        $filePath = $task->file_path;
        if ($request->hasFile('file')) {
            if ($filePath) {
                Storage::disk('public')->delete($filePath);
            }
            $filePath = $request->file('file')->store('tasks', 'public');
        }

        $task->update([
            'title' => $request->title,
            'file_path' => $filePath,
        ]);

        return redirect()->back();
    }

    public function destroy(Task $task)
    {
        if ($task->file_path) {
            Storage::disk('public')->delete($task->file_path);
        }

        $task->delete();
        
        return redirect()->back();
    }
}