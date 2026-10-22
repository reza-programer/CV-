<script setup lang="ts">
import type { Block } from '../../blocks';
import ContactList from '../parts/ContactList.vue';
import EntryFacts from '../parts/EntryFacts.vue';

/**
 * Modern ATS: Calibri-metric type, left-aligned header closed by a single
 * solid rule, sentence-case headings without rules, and entries led by the
 * role with organisation, place and dates on one quiet line beneath.
 */
defineProps<{ block: Block }>();
</script>

<template>
    <header v-if="block.kind === 'header'" class="b-header">
        <div class="header-row">
            <div class="header-text">
                <h1 v-if="block.name" class="name">{{ block.name }}</h1>
                <p v-if="block.headline" class="headline">
                    {{ block.headline }}
                </p>
            </div>
            <img
                v-if="block.photoUrl"
                :src="block.photoUrl"
                alt=""
                class="photo"
            />
        </div>
        <p v-if="block.contacts.length" class="contacts">
            <ContactList :contacts="block.contacts" separator="  |  " />
        </p>
    </header>

    <div v-else-if="block.kind === 'heading'" class="b-heading">
        <h2 class="heading-text">{{ block.title }}</h2>
    </div>

    <p v-else-if="block.kind === 'text'" class="b-text">{{ block.text }}</p>

    <div v-else-if="block.kind === 'entry'" class="b-entry">
        <p v-if="block.entry.title" class="entry-title">
            {{ block.entry.title }}
        </p>
        <p class="entry-facts">
            <EntryFacts
                :entry="block.entry"
                :fields="['subtitle', 'location', 'dates', 'details', 'link']"
            />
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
