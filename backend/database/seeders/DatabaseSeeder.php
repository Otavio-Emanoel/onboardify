<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Module;
use App\Models\Task;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create a sample white-label Tenant (Acme Corp)
        $tenant = Tenant::create([
            'name' => 'Acme Corp',
            'primary_color' => '#0ea5e9', // Sky blue
            'secondary_color' => '#0f172a', // Slate dark
            'logo_url' => 'https://raw.githubusercontent.com/Otavio-Emanoel/onboardify/refs/heads/main/identity/logo.png'
        ]);

        // 2. Create Admin user
        User::create([
            'name' => 'Jane Doe',
            'email' => 'admin@onboardify.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'tenant_id' => $tenant->id,
            'level' => 1,
            'points' => 0
        ]);

        // 3. Create Employee user
        User::create([
            'name' => 'Alex Mercer',
            'email' => 'employee@onboardify.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'tenant_id' => $tenant->id,
            'level' => 2,
            'points' => 120
        ]);

        // 4. Create Basic Onboarding Journey Modules & Tasks
        $mod1 = Module::create([
            'tenant_id' => $tenant->id,
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

        $mod2 = Module::create([
            'tenant_id' => $tenant->id,
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
