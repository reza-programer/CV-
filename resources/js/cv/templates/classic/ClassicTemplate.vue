<script setup lang="ts">
import type { Block } from '../../blocks';
import ContactList from '../parts/ContactList.vue';
import EntryFacts from '../parts/EntryFacts.vue';

/**
 * Classic ATS: centred name and one-line contact row, uppercase headings over
 * a thin rule, and entries written as "Role | Organisation" with the dates on
 * the line below.
 */
defineProps<{ block: Block }>();
</script>

<template>
    <header
        v-if="block.kind === 'header'"
        class="b-header"
        :class="{ 'has-photo': block.photoUrl }"
    >
        <div class="header-text">
            <h1 v-if="block.name" class="name">{{ block.name }}</h1>
            <p v-if="block.headline" class="headline">{{ block.headline }}</p>
            <p v-if="block.contacts.length" class="contacts">
                <ContactList :contacts="block.contacts" />
            </p>
        </div>
        <img v-if="block.photoUrl" :src="block.photoUrl" alt="" class="photo" />
    </header>

    <div v-else-if="block.kind === 'heading'" class="b-heading">
        <h2 class="heading-text">{{ block.title }}</h2>
    </div>

    <p v-else-if="block.kind === 'text'" class="b-text">{{ block.text }}</p>

    <div v-else-if="block.kind === 'entry'" class="b-entry">
        <p class="entry-head">
            <strong v-if="block.entry.title">{{ block.entry.title }}</strong>
            <template v-if="block.entry.title && block.entry.subtitle">{{
                ' | '
            }}</template>
            <span v-if="block.entry.subtitle">{{ block.entry.subtitle }}</span>
        </p>
        <p
            v-if="
                block.entry.location ||
                block.entry.dates ||
                block.entry.details.length ||
                block.entry.link
            "
            class="entry-facts"
        >
            <EntryFacts :entry="block.entry" />
        </p>
    </div>

    <div v-else-if="block.kind === 'bullet'" class="b-bullet">
        <span class="bullet-mark">•</span>
        <span class="bullet-text">{{ block.text }}</span>
    </div>

    <p v-else-if="block.kind === 'line'" class="b-line">
        <strong v-if="block.label">{{ block.label }}: </strong>{{ block.value }}
    </p>
</template>
