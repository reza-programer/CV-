<script setup lang="ts">
import type { ContactItem } from '../../blocks';

withDefaults(defineProps<{ contacts: ContactItem[]; separator?: string }>(), {
    separator: ' | ',
});
</script>

<template>
    <template v-for="(contact, index) in contacts" :key="contact.kind">
        <span v-if="index > 0" class="contact-sep">{{ separator }}</span>
        <a v-if="contact.href" :href="contact.href" class="contact nowrap">{{
            contact.label
        }}</a>
        <span v-else class="contact nowrap">{{ contact.label }}</span>
    </template>
</template>
