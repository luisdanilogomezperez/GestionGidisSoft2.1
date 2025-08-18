<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MenuItem;

class MenuItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            ['title' => 'Dashboard', 'href' => '/dashboard', 'icon' => 'LuLayoutPanelLeft', 'permission' => null, 'order' => 1],
            ['title' => 'Books', 'href' => '/books', 'icon' => 'BookOpen', 'permission' => 'read_books', 'order' => 2],
            ['title' => 'Book Chapter', 'href' => '/chapter-books', 'icon' => 'BookOpenText', 'permission' => 'read_book_chapters', 'order' => 3],
            ['title' => 'Articles', 'href' => '/articles', 'icon' => 'CaLicenseDraft', 'permission' => 'read_articles', 'order' => 4],
            ['title' => 'Directed Projects', 'href' => '/directed-projects', 'icon' => 'AnOutlinedFileSearch', 'permission' => 'read_directed_projects', 'order' => 5],
            ['title' => 'Research Projects', 'href' => '/research-projects', 'icon' => 'AnOutlinedFileProtect', 'permission' => 'read_research_projects', 'order' => 6],
            ['title' => 'Events', 'href' => '/events', 'icon' => 'CdRocket', 'permission' => 'read_events', 'order' => 7],
            ['title' => 'Presentations', 'href' => '/presentations', 'icon' => 'CdPreview', 'permission' => 'read_presentations', 'order' => 8],
            ['title' => 'Other Jobs', 'href' => '/other-jobs', 'icon' => 'CdReferences', 'permission' => 'read_other_jobs', 'order' => 9],
            ['title' => 'Users Management', 'href' => '/users-management', 'icon' => 'CaUserSettings', 'permission' => 'read_roles', 'order' => 10],
        ];

        foreach ($items as $item) {
            MenuItem::create($item);
        }
    }
}
