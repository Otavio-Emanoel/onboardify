<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Task;
use App\Models\User;
use App\Models\UserTaskProgress;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function getEmployees(Request $request)
    {
        $admin = $request->user();
        
        // Fetch all employees belonging to the admin's tenant
        $employees = User::where('role', 'employee')
            ->where('tenant_id', $admin->tenant_id)
            ->get();

        // Calculate metrics dynamically
        $totalHires = $employees->count();
        
        $employeeData = $employees->map(function ($emp) {
            // Get all tasks for this tenant
            $modules = Module::where('tenant_id', $emp->tenant_id)->pluck('id');
            $totalTasks = Task::whereIn('module_id', $modules)->count();
            
            // Calculate completed tasks
            $completedTasks = UserTaskProgress::where('user_id', $emp->id)
                ->where('completed', true)
                ->count();
                
            $progress = $totalTasks > 0 ? Math_round(($completedTasks / $totalTasks) * 100) : 0;
            
            // Determine status based on progress
            $status = 'on_track';
            if ($progress === 100) {
                $status = 'completed';
            } elseif ($progress < 10 && now()->diffInDays($emp->created_at) > 3) {
                // If registered for over 3 days and less than 10% progress, they are behind
                $status = 'behind';
            }

            return [
                'id' => $emp->id,
                'name' => $emp->name,
                'email' => $emp->email,
                'department' => 'Onboarding', // default department
                'hireDate' => $emp->created_at->format('Y-m-d'),
                'progress' => $progress,
                'level' => $emp->level,
                'points' => $emp->points,
                'status' => $status
            ];
        });

        return response()->json($employeeData);
    }

    public function saveJourney(Request $request)
    {
        $admin = $request->user();

        $request->validate([
            'week1' => 'required|array',
            'week2' => 'required|array'
        ]);

        // Wrap database writes in a transaction to prevent partial saves
        \DB::transaction(function() use ($request, $admin) {
            // Retrieve or create Module for Week 1
            $moduleWeek1 = Module::updateOrCreate(
                ['tenant_id' => $admin->tenant_id, 'week' => 'Week 1'],
                ['title' => 'Welcome & Basic Setup', 'description' => 'Week 1 base configuration', 'order' => 1]
            );

            // Retrieve or create Module for Week 2
            $moduleWeek2 = Module::updateOrCreate(
                ['tenant_id' => $admin->tenant_id, 'week' => 'Week 2'],
                ['title' => 'Role Deep Dive & Security', 'description' => 'Week 2 base configuration', 'order' => 2]
            );

            // Re-sync Week 1 Tasks
            // First, remove old tasks not in the incoming payload
            $w1TaskTitles = collect($request->week1)->pluck('title');
            Task::where('module_id', $moduleWeek1->id)
                ->whereNotIn('title', $w1TaskTitles)
                ->delete();

            foreach ($request->week1 as $index => $taskData) {
                Task::updateOrCreate(
                    ['module_id' => $moduleWeek1->id, 'title' => $taskData['title']],
                    [
                        'description' => $taskData['description'] ?? 'Complete this item',
                        'type' => $taskData['type'] ?? 'pdf',
                        'pts' => $taskData['pts'] ?? 10,
                        'duration' => $taskData['duration'] ?? '5 min',
                        'order' => $index
                    ]
                );
            }

            // Re-sync Week 2 Tasks
            $w2TaskTitles = collect($request->week2)->pluck('title');
            Task::where('module_id', $moduleWeek2->id)
                ->whereNotIn('title', $w2TaskTitles)
                ->delete();

            foreach ($request->week2 as $index => $taskData) {
                Task::updateOrCreate(
                    ['module_id' => $moduleWeek2->id, 'title' => $taskData['title']],
                    [
                        'description' => $taskData['description'] ?? 'Complete this item',
                        'type' => $taskData['type'] ?? 'pdf',
                        'pts' => $taskData['pts'] ?? 10,
                        'duration' => $taskData['duration'] ?? '5 min',
                        'order' => $index
                    ]
                );
            }
        });

        return response()->json([
            'message' => 'Journey layout config stored successfully.'
        ]);
    }
}

// Global scope helper for round in php inside standard class
function Math_round($val) {
    return (int) round($val);
}
