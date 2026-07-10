<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Task;
use App\Models\UserTaskProgress;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function getModules(Request $request)
    {
        $employee = $request->user();

        // Ensure we seed default modules if none exist yet for this tenant
        $this->ensureDefaultModulesExist($employee->tenant_id);

        $modules = Module::where('tenant_id', $employee->tenant_id)
            ->with(['tasks'])
            ->orderBy('order')
            ->get();

        // Map completions dynamically
        $responseData = $modules->map(function ($mod) use ($employee) {
            $tasksMapped = $mod->tasks->map(function ($task) use ($employee) {
                $progressRecord = UserTaskProgress::where('user_id', $employee->id)
                    ->where('task_id', $task->id)
                    ->first();

                $completed = $progressRecord ? $progressRecord->completed : false;

                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'description' => $task->description,
                    'type' => $task->type,
                    'pts' => $task->pts,
                    'duration' => $task->duration,
                    'completed' => $completed,
                    // Dynamic fields returned for execution
                    'videoUrl' => $task->video_url,
                    'quizQuestion' => $task->quiz_question,
                    'quizOptions' => $task->quiz_options,
                    'quizCorrectIndex' => $task->quiz_correct_index,
                    'pdfTitle' => $task->pdf_title,
                    'pdfContentPages' => $task->pdf_content_pages
                ];
            });

            // Calculate progress percentage for this module
            $totalTasks = $tasksMapped->count();
            $completedTasksCount = $tasksMapped->filter(fn($t) => $t['completed'])->count();
            $progressPct = $totalTasks > 0 ? (int) round(($completedTasksCount / $totalTasks) * 100) : 0;

            return [
                'id' => $mod->id,
                'title' => $mod->title,
                'description' => $mod->description,
                'week' => $mod->week,
                'progress' => $progressPct,
                'tasks' => $tasksMapped
            ];
        });

        return response()->json($responseData);
    }

    public function completeTask(Request $request, $id)
    {
        $employee = $request->user();
        $task = Task::findOrFail($id);

        // Validation for Quiz
        if ($task->type === 'quiz') {
            $request->validate([
                'selected_index' => 'required|integer'
            ]);

            if ($request->selected_index !== $task->quiz_correct_index) {
                return response()->json([
                    'error' => 'Incorrect answer'
                ], 422);
            }
        }

        // Set Task completion in DB
        $progress = UserTaskProgress::updateOrCreate(
            ['user_id' => $employee->id, 'task_id' => $task->id],
            ['completed' => true, 'completed_at' => now()]
        );

        // Grant Points and update level
        $pointsEarned = $task->pts;
        $employee->points = ($employee->points || 0) + $pointsEarned;
        // Level increments by 1 for every 100 points
        $employee->level = (int) floor($employee->points / 100) + 1;
        $employee->save();

        return response()->json([
            'message' => 'Task marked complete. Points claimed!',
            'points' => $employee->points,
            'level' => $employee->level,
            'progress' => $progress
        ]);
    }

    /**
     * Seeds some default modules & tasks for a tenant if none exist.
     */
    protected function ensureDefaultModulesExist($tenantId)
    {
        if (Module::where('tenant_id', $tenantId)->count() === 0) {
            // Module 1
            $mod1 = Module::create([
                'tenant_id' => $tenantId,
                'title' => 'Welcome & System Setup',
                'description' => 'Configure your hardware, complete basic security protocols, and set up your initial communications workspace.',
                'week' => 'Week 1',
                'order' => 1
            ]);

            Task::create([
                'module_id' => $mod1->id,
                'title' => 'Watch Welcome Video',
                'description' => 'A message from our CEO welcoming you to the team and explaining our central vision and objectives.',
                'type' => 'video',
                'pts' => 10,
                'duration' => '5 min',
                'video_url' => 'https://assets.mixkit.co/videos/preview/mixkit-working-late-at-the-office-39794-large.mp4',
                'order' => 1
            ]);

            Task::create([
                'module_id' => $mod1->id,
                'title' => 'Environment Setup Checklist',
                'description' => 'Follow this technical guide to configure your system preferences, access keys, and repositories.',
                'type' => 'pdf',
                'pts' => 20,
                'duration' => '15 min',
                'pdf_title' => 'developer_setup_guide_v2.pdf',
                'pdf_content_pages' => [
                    'Welcome to the Development Team! \n\n1. Request GitHub Access in the Slack #it-helpdesk channel.',
                    '4. Clone the repository: git clone git@github.com:onboardify/frontend.git',
                    '8. Configure 2FA keys for all administrative logins.'
                ],
                'order' => 2
            ]);

            // Module 2
            $mod2 = Module::create([
                'tenant_id' => $tenantId,
                'title' => 'Company Culture & Values',
                'description' => 'Learn about our core mission, operational frameworks, and hear from team leaders about how we collaborate.',
                'week' => 'Week 1',
                'order' => 2
            ]);

            Task::create([
                'module_id' => $mod2->id,
                'title' => 'Onboarding Quiz: Security Policies',
                'description' => 'Test your understanding of security standards, lock policies, and social engineering warnings.',
                'type' => 'quiz',
                'pts' => 30,
                'duration' => '10 min',
                'quiz_question' => 'Which of the following is our standard policy regarding laptops in public spaces?',
                'quiz_options' => [
                    'Laptops can be left logged-in if you are only gone for under 2 minutes.',
                    'Laptops must be screen-locked and securely tethered or stored when unattended, even briefly.',
                    'You are allowed to share passwords with colleagues over Slack without secure encryption.',
                    'Leaving your computer unlocked in the workspace is perfectly fine.'
                ],
                'quiz_correct_index' => 1,
                'order' => 1
            ]);
        }
    }
}
