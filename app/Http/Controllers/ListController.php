<?php

namespace App\Http\Controllers;

use App\Models\Lists;
use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ListController extends Controller
{
    public function store(Request $request, Task $task)
    {
        $request->validate(['name' => 'required']);
        
        $task->lists()->create(['name' => $request->name]);

        return redirect()->back();
    }

    public function update(Request $request, Task $task, Lists $list)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $list->update(['name' => $request->name]);

        return redirect()->back();
    }

    public function destroy(Task $task, Lists $list)
    {
        $list->delete();
        
        return redirect()->back();
    }
}
