<script setup lang="ts">
import type { Block } from '../../blocks';
import ContactList from '../parts/ContactList.vue';
import EntryFacts from '../parts/EntryFacts.vue';

/**
 * Gutter ATS: section titles sit in a narrow left gutter beside their first
 * entry while the content runs in one column on the right. In reading order
 * each title still comes directly before its content.
 */
defineProps<{ block: Block }>();
</script>

<template>
    <header v-if="block.kind === 'header'" class="b-header">
        <div class="header-text">
            <h1 v-if="block.name" class="name">{{ block.name }}</h1>
            <p v-if="block.headline" class="headline">{{ block.headline }}</p>
        </div>
        <p v-if="block.contacts.length" class="contacts">
            <ContactList :contacts="block.contacts" separator=" | " />
        </p>
        <img v-if="block.photoUrl" :src="block.photoUrl" alt="" class="photo" />
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
                separator=", "
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
