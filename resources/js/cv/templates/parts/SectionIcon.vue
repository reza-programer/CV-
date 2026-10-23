<script setup lang="ts">
import {
    Award,
    BriefcaseBusiness,
    FolderGit2,
    GraduationCap,
    Languages,
    Sparkles,
    User,
    Users,
    Wrench,
} from '@lucide/vue';
import type { SectionKey } from '../../types';

/** Decorative icon beside a section title; the title itself stays text. */
defineProps<{ section: SectionKey }>();

const ICONS = {
    summary: User,
    experience: BriefcaseBusiness,
    internship: Sparkles,
    organization: Users,
    education: GraduationCap,
    projects: FolderGit2,
    skills: Wrench,
    certifications: Award,
    languages: Languages,
};
</script>

<template>
    <component :is="ICONS[section]" class="section-icon" aria-hidden="true" />
</template>
