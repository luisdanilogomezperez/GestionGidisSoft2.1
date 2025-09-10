import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';
import axios from 'axios';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
}

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    name: string;
    last_name: string;
    document_type: string;
    document_number: string;
    phone_number: string;
    is_enable: boolean;
    academic_formation?: [];
    work_experience?: [];
    roles: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
};
export interface AcademicFormation {
  institution: string;
  degree: string;
  startYear: integer;
  endYear?: integer | null;
}

export interface WorkExperience {
  company: string;
  position: string;
  startYear: integer;
  endYear?: integer | null;
}

export interface User {
    id: number;
    name: string;
    last_name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    document_type: string;
    document_number: string;
    phone_number: string;
    is_enable?: boolean;
    roles?: string;
    academic_formation?: AcademicFormation[];
    work_experience?: WorkExperience[];
}

export type BreadcrumbItemType = BreadcrumbItem;
