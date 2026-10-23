<script setup lang="ts">
import type { Block } from '../../blocks';
import EntryFacts from '../parts/EntryFacts.vue';

/**
 * Corporate ATS: a light grey band across the top with the name on the left
 * and the contact details stacked on the right, then plain ruled sections.
 * Black and white.
 */
defineProps<{ block: Block }>();
</script>

<template>
    <header v-if="block.kind === 'header'" class="b-header">
        <div class="band">
            <img
                v-if="block.photoUrl"
                :src="block.photoUrl"
                alt=""
                class="photo"
            />
            <div class="header-text">
                <h1 v-if="block.name" class="name">{{ block.name }}</h1>
                <p v-if="block.headline" class="headline">
                    {{ block.headline }}
                </p>
            </div>
            <ul v-if="block.contacts.length" class="contacts">
                <li v-for="contact in block.contacts" :key="contact.kind">
                    <a v-if="contact.href" :href="contact.href">{{
                        contact.label
                    }}</a>
                    <template v-else>{{ contact.label }}</template>
                </li>
            </ul>
        </div>
    </header>

    <div v-else-if="block.kind === 'heading'" class="b-heading">
        <h2 class="heading-text">{{ block.title }}</h2>
    </div>

    <p v-else-if="block.kind === 'text'" class="b-text">{{ block.text }}</p>

    <div v-else-if="block.kind === 'entry'" class="b-entry">
        <div class="entry-row">
            <strong class="entry-lead">{{
                block.entry.title || block.entry.subtitle
            }}</strong>
            <span v-if="block.entry.dates" class="entry-aside nowrap">{{
                block.entry.dates
            }}</span>
        </div>
        <p
            v-if="
                (block.entry.title && block.entry.subtitle) ||
                block.entry.location ||
                block.entry.details.length ||
                block.entry.link
            "
            class="entry-sub"
        >
            <EntryFacts
                :entry="block.entry"
                :fields="
                    block.entry.title
                        ? ['subtitle', 'location', 'details', 'link']
                        : ['location', 'details', 'link']
                "
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
