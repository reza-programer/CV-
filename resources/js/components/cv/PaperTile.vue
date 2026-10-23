<script setup lang="ts">
import type { ResumeData } from '@/cv/types';
import CvThumbnail from './CvThumbnail.vue';

/**
 * The first page of a CV lying on the desk: used wherever a CV or template
 * is shown as a tile (gallery, template picker, dashboard).
 */
defineProps<{ data: ResumeData; selected?: boolean }>();
</script>

<template>
    <span
        class="block rounded-md bg-desk p-[9%] transition-[background-color,box-shadow] group-hover:bg-[#ecebe5]"
        :class="selected ? 'ring-2 ring-ink ring-offset-2' : ''"
    >
        <span class="block paper">
            <CvThumbnail :data="data" />
        </span>
    </span>
</template>
