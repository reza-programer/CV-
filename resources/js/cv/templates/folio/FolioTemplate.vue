<script setup lang="ts">
import type { Block } from '../../blocks';
import type { Region } from '../../regions';
import ContactList from '../parts/ContactList.vue';
import EntryFacts from '../parts/EntryFacts.vue';

/**
 * Folio: a full-width header over a heavy rule, then a light grey panel on
 * the left for education, skills, certificates and languages, and the
 * profile and work history on the right. Black and white.
 */
withDefaults(defineProps<{ block: Block; region?: Region }>(), {
    region: 'main',
});
</script>

<template>
    <header v-if="block.kind === 'header'" class="b-header">
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

    <div
        v-else-if="block.kind === 'entry' && region === 'aside'"
        class="b-entry"
    >
        <p>
            <strong>{{ block.entry.title || block.entry.subtitle }}</strong>
        </p>
        <p class="entry-sub">
            <EntryFacts
                :entry="block.entry"
                :fields="
                    block.entry.title
                        ? ['subtitle', 'dates', 'details', 'link']
                        : ['dates', 'details', 'link']
                "
            />
        </p>
    </div>

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

    <div v-else-if="block.kind === 'line' && region === 'aside'" class="b-line">
        <p v-if="block.label" class="line-label">{{ block.label }}</p>
        <p>{{ block.value }}</p>
    </div>

    <p v-else-if="block.kind === 'line'" class="b-line">
        <strong v-if="block.label">{{ block.label }}: </strong>{{ block.value }}
    </p>
</template>
