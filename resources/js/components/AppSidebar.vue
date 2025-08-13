<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Folder, BookOpenText} from 'lucide-vue-next';
import { LuLayoutPanelLeft, CaLicenseDraft, AnOutlinedFileSearch, AnOutlinedFileProtect,
     CaUserSettings, CdRocket, CdPreview, CdReferences } from '@kalimahapps/vue-icons';
import AppLogo from './AppLogo.vue';

const page = usePage();
const userPermissions = page.props.user.permissions;

const mainNavItems: (NavItem & { permission?: string })[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LuLayoutPanelLeft
    },
    {
        title: 'Books',
        href: '/books',
        icon: BookOpen,
        permission: 'read_books'
    },
    {
        title: 'Book Chapter',
        href: '/chapter-books',
        icon: BookOpenText,
        permission: 'read_book_chapters'
    },
    {
        title: 'Articles',
        href: '/articles',
        icon: CaLicenseDraft,
        permission: 'read_articles'
    },
    {
        title: 'Directed Projects',
        href: '/directed-projects',
        icon: AnOutlinedFileSearch,
        permission: 'read_directed_projects'
    },
    {
        title: 'Research Projects',
        href: '/research-projects',
        icon: AnOutlinedFileProtect,
        permission: 'read_research_projects'
    },
    {
        title: 'Events',
        href: '/events',
        icon: CdRocket,
        permission: 'read_events'
    },
    {
        title: 'Presentations',
        href: '/presentations',
        icon: CdPreview,
        permission: 'read_presentations'
    },
    {
        title: 'Other Jobs',
        href: '/other-jobs',
        icon: CdReferences,
        permission: 'read_other_jobs'
    },
    {
        title: 'Users Management',
        href: '/users-management',
        icon: CaUserSettings,
        permission: 'read_roles'
    },
];

// Filtrar por permisos del usuario
const visibleMainNavItems = mainNavItems.filter(item => {
    return !item.permission || userPermissions.includes(item.permission);
});

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="visibleMainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
