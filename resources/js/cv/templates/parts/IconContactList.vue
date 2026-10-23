<script setup lang="ts">
import type { ContactItem } from '../../blocks';
import ContactIcon from './ContactIcon.vue';

/**
 * Contacts as icon + text pairs, inline or stacked. A visually hidden
 * separator keeps the values apart in the extracted PDF text.
 */
withDefaults(defineProps<{ contacts: ContactItem[]; stacked?: boolean }>(), {
    stacked: false,
});
</script>

<template>
    <span class="icon-contacts" :class="{ 'is-stacked': stacked }">
        <span
            v-for="(contact, index) in contacts"
            :key="contact.kind"
            class="icon-contact"
        >
            <span v-if="index > 0" class="icon-contact-sep">{{ ' | ' }}</span>
            <ContactIcon :kind="contact.kind" />
            <a v-if="contact.href" :href="contact.href">{{ contact.label }}</a>
            <span v-else>{{ contact.label }}</span>
        </span>
    </span>
</template>
