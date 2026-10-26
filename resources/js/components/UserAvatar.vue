<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { getInitials } from '@/composables/useInitials';
import type { User } from '@/types';

/** The account photo, or the name's initials when there is none. */
defineProps<{ user: User }>();
</script>

<template>
    <Avatar class="shrink-0 overflow-hidden rounded-full">
        <AvatarImage
            v-if="user.avatar_url"
            :src="user.avatar_url"
            :alt="user.name"
        />
        <AvatarFallback
            class="rounded-full bg-neutral-200 text-xs font-medium text-neutral-800"
        >
            {{ getInitials(user.name) }}
        </AvatarFallback>
    </Avatar>
</template>
