<script setup lang="ts">
import type { Block } from '../../blocks';
import EntryFacts from '../parts/EntryFacts.vue';
import IconContactList from '../parts/IconContactList.vue';

/**
 * Timeline ATS: entries hang off a vertical line with an accent dot, dates
 * first, so a career reads as a sequence. One column, round photo optional.
 */
defineProps<{ block: Block }>();
</script>

<template>
    <header v-if="block.kind === 'header'" class="b-header">
        <div class="header-text">
            <h1 v-if="block.name" class="name">{{ block.name }}</h1>
            <p v-if="block.headline" class="headline">{{ block.headline }}</p>
            <p v-if="block.contacts.length" class="contacts">
                <IconContactList :contacts="block.contacts" />
            </p>
        </div>
        <img
            v-if="block.photoUrl"
            :src="block.photoUrl"
            alt=""
            class="photo is-round"
        />
    </header>

    <div v-else-if="block.kind === 'heading'" class="b-heading">
        <h2 class="heading-text">{{ block.title }}</h2>
    </div>

    <p v-else-if="block.kind === 'text'" class="b-text">{{ block.text }}</p>

    <div v-else-if="block.kind === 'entry'" class="b-entry">
        <p v-if="block.entry.dates" class="entry-dates">
            {{ block.entry.dates }}
        </p>
        <p class="entry-title">
            <strong>{{ block.entry.title || block.entry.subtitle }}</strong>
        </p>
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
