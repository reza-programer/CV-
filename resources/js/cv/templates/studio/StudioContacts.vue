<script setup lang="ts">
import type { ContactItem } from '../../blocks';
import ContactList from '../parts/ContactList.vue';
import IconContactList from '../parts/IconContactList.vue';
import type { ContactMode } from './options';

/**
 * Contact details in one of the studio styles: a plain row, icon pairs
 * inline or stacked, or a two-column table of field names and values in the
 * manner of an Indonesian "data pribadi" block.
 */
defineProps<{ contacts: ContactItem[]; mode: ContactMode }>();
</script>

<template>
    <dl v-if="mode === 'table'" class="contact-table">
        <div
            v-for="contact in contacts"
            :key="contact.kind"
            class="contact-row"
        >
            <dt>{{ contact.field }}</dt>
            <dd>
                <a v-if="contact.href" :href="contact.href">{{
                    contact.label
                }}</a>
                <template v-else>{{ contact.label }}</template>
            </dd>
        </div>
    </dl>
    <IconContactList
        v-else-if="mode === 'icons' || mode === 'icons-stacked'"
        :contacts="contacts"
        :stacked="mode === 'icons-stacked'"
    />
    <ContactList v-else :contacts="contacts" />
</template>
