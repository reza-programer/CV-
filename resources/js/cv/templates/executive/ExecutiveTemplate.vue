<script setup lang="ts">
import type { Block } from '../../blocks';
import ContactList from '../parts/ContactList.vue';
import EntryFacts from '../parts/EntryFacts.vue';

/**
 * Executive ATS: Times-metric serif, everything centred in the header with
 * the photo above the name, a double rule under it, and headings flanked by
 * hairlines. Entries lead with the role, organisation in italics below.
 */
defineProps<{ block: Block }>();
</script>

<template>
    <header v-if="block.kind === 'header'" class="b-header">
        <img v-if="block.photoUrl" :src="block.photoUrl" alt="" class="photo" />
        <h1 v-if="block.name" class="name">{{ block.name }}</h1>
        <p v-if="block.headline" class="headline">{{ block.headline }}</p>
        <p v-if="block.contacts.length" class="contacts">
            <ContactList :contacts="block.contacts" separator=" · " />
        </p>
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
            <em v-if="block.entry.title && block.entry.subtitle">{{
                block.entry.subtitle
            }}</em>
            <template
                v-if="
                    block.entry.title &&
                    block.entry.subtitle &&
                    (block.entry.location ||
                        block.entry.details.length ||
                        block.entry.link)
                "
                >{{ ', ' }}</template
            >
            <EntryFacts
                :entry="block.entry"
                :fields="['location', 'details', 'link']"
                separator=", "
            />
        </p>
    </div>

    <div v-else-if="block.kind === 'bullet'" class="b-bullet">
        <span class="bullet-mark">•</span>
        <span class="bullet-text">{{ block.text }}</span>
    </div>

    <p v-else-if="block.kind === 'line'" class="b-line">
        <strong v-if="block.label" class="line-label"
            >{{ block.label }}: </strong
        >{{ block.value }}
    </p>
</template>
