<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeUserViews extends Command
{
    protected $signature = 'make:user-views';
    protected $description = 'Generate user view structure with layouts and components';

    public function handle()
    {
        $basePath = resource_path('views');
        $structure = [
            'layouts' => [
                'user.blade.php' => "<!-- Main layout for user -->"
            ],
            'user' => [
                'dashboard.blade.php' => "<!-- Dashboard page -->",
                'components' => [
                    'styles.blade.php' => "<!-- Custom CSS -->",
                    'desktop-nav.blade.php' => "<!-- Desktop navigation -->",
                    'header.blade.php' => "<!-- Header section -->",
                    'welcome-card.blade.php' => "<!-- Welcome card with actions -->",
                    'status-cards.blade.php' => "<!-- Attendance status cards -->",
                    'attendance-summary.blade.php' => "<!-- Monthly summary -->",
                    'weekly-summary.blade.php' => "<!-- Weekly summary -->",
                    'bottom-nav.blade.php' => "<!-- Mobile navigation -->",
                    'sidebar-menu.blade.php' => "<!-- Sidebar menu -->",
                ]
            ]
        ];

        $this->createStructure($basePath, $structure);
        $this->info('✅ User view structure created successfully!');
    }

    protected function createStructure($path, $items)
    {
        foreach ($items as $name => $content) {
            $currentPath = $path . DIRECTORY_SEPARATOR . $name;
            if (is_array($content)) {
                File::ensureDirectoryExists($currentPath);
                $this->createStructure($currentPath, $content);
            } else {
                File::ensureDirectoryExists(dirname($currentPath));
                File::put($currentPath, $content . PHP_EOL);
                $this->line("📄 Created: " . str_replace(resource_path(), 'resources', $currentPath));
            }
        }
    }
}
