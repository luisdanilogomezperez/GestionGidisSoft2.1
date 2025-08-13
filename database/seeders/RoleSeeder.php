<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role_admin = Role::create(['name' => 'admin']);
        $role_docente = Role::create(['name' => 'docente']);

        $permission_create_role = Permission::create(['name' => 'create_roles']);
        $permission_read_role = Permission::create(['name' => 'read_roles']);
        $permission_update_role = Permission::create(['name' => 'update_roles']);
        $permission_delete_role = Permission::create(['name' => 'delete_roles']);

        $permission_create_book = Permission::create(['name' => 'create_books']);
        $permission_read_book = Permission::create(['name' => 'read_books']);
        $permission_update_book = Permission::create(['name' => 'update_books']);
        $permission_delete_book = Permission::create(['name' => 'delete_books']);

        $permission_create_chapter_book = Permission::create(['name' => 'create_book_chapters']);
        $permission_read_chapter_book = Permission::create(['name' => 'read_book_chapters']);
        $permission_update_chapter_book = Permission::create(['name' => 'update_book_chapters']);
        $permission_delete_chapter_book = Permission::create(['name' => 'delete_book_chapters']);

        $permission_create_article = Permission::create(['name' => 'create_articles']);
        $permission_read_article = Permission::create(['name' => 'read_articles']);
        $permission_update_article = Permission::create(['name' => 'update_articles']);
        $permission_delete_article = Permission::create(['name' => 'delete_articles']);

        $permission_create_directed_project = Permission::create(['name' => 'create_directed_projects']);
        $permission_read_directed_project = Permission::create(['name' => 'read_directed_projects']);
        $permission_update_directed_project = Permission::create(['name' => 'update_directed_projects']);
        $permission_delete_directed_project = Permission::create(['name' => 'delete_directed_projects']);

        $permission_create_event = Permission::create(['name' => 'create_events']);
        $permission_read_event = Permission::create(['name' => 'read_events']);
        $permission_update_event = Permission::create(['name' => 'update_events']);
        $permission_delete_event = Permission::create(['name' => 'delete_events']);

        $permission_create_other_jobs = Permission::create(['name' => 'create_other_jobs']);
        $permission_read_other_jobs = Permission::create(['name' => 'read_other_jobs']);
        $permission_update_other_jobs = Permission::create(['name' => 'update_other_jobs']);
        $permission_delete_other_jobs = Permission::create(['name' => 'delete_other_jobs']);

        $permission_create_presentation = Permission::create(['name' => 'create_presentations']);
        $permission_read_presentation = Permission::create(['name' => 'read_presentations']);
        $permission_update_presentation = Permission::create(['name' => 'update_presentations']);
        $permission_delete_presentation = Permission::create(['name' => 'delete_presentations']);

        $permission_create_research_project = Permission::create(['name' => 'create_research_projects']);
        $permission_read_research_project = Permission::create(['name' => 'read_research_projects']);
        $permission_update_research_project = Permission::create(['name' => 'update_research_projects']);
        $permission_delete_research_project = Permission::create(['name' => 'delete_research_projects']);

        $permissions_admin = [
            $permission_create_role,
            $permission_read_role,
            $permission_update_role,
            $permission_delete_role,
            $permission_create_book,
            $permission_read_book,
            $permission_update_book,
            $permission_delete_book,
            $permission_create_chapter_book,
            $permission_read_chapter_book,
            $permission_update_chapter_book,
            $permission_delete_chapter_book,
            $permission_create_article,
            $permission_read_article,
            $permission_update_article,
            $permission_delete_article,
            $permission_create_directed_project,
            $permission_read_directed_project,
            $permission_update_directed_project,
            $permission_delete_directed_project,
            $permission_create_event,
            $permission_read_event,
            $permission_update_event,
            $permission_delete_event,
            $permission_create_other_jobs,
            $permission_read_other_jobs,
            $permission_update_other_jobs,
            $permission_delete_other_jobs,
            $permission_create_presentation,        
            $permission_read_presentation,
            $permission_update_presentation,
            $permission_delete_presentation,
            $permission_create_research_project,
            $permission_read_research_project,
            $permission_update_research_project,
            $permission_delete_research_project
        ];

        $permissions_docente = [
            $permission_create_book,
            $permission_read_book,
            $permission_update_book,
            $permission_delete_book,
            $permission_create_chapter_book,
            $permission_read_chapter_book,
            $permission_update_chapter_book,
            $permission_delete_chapter_book,
            $permission_create_article,
            $permission_read_article,
            $permission_update_article,
            $permission_delete_article,
            $permission_create_directed_project,
            $permission_read_directed_project,
            $permission_update_directed_project,
            $permission_delete_directed_project,
            $permission_create_event,
            $permission_read_event,
            $permission_update_event,
            $permission_delete_event,
            $permission_create_other_jobs,
            $permission_read_other_jobs,
            $permission_update_other_jobs,
            $permission_delete_other_jobs,
            $permission_create_presentation,        
            $permission_read_presentation,
            $permission_update_presentation,
            $permission_delete_presentation,
            $permission_create_research_project,
            $permission_read_research_project,
            $permission_update_research_project,
            $permission_delete_research_project

        ];

        $role_admin->syncPermissions($permissions_admin);
        $role_docente->syncPermissions($permissions_docente);
    }
}
