<script setup lang="ts">
import { computed } from 'vue';
import type { Block } from '../../blocks';
import ContactList from '../parts/ContactList.vue';
import EntryFacts from '../parts/EntryFacts.vue';

/**
 * Monogram ATS: the initials in an accent square beside the name (or the
 * photo, when there is one), short accent underlines under the headings, and
 * entries with the dates flush right.
 */
const props = defineProps<{ block: Block }>();

/** First and last word of the name, ignoring degree suffixes such as "S.T.". */
const initials = computed(() => {
    if (props.block.kind !== 'header') {
        return '';
    }

    const words = props.block.name
        .split(',')[0]
        .split(/\s+/)
        .filter((word) => /^\p{L}/u.test(word) && !word.includes('.'));

    if (words.length === 0) {
        return '';
    }

    const last = words.length > 1 ? words[words.length - 1] : '';

    return (words[0][0] + (last[0] ?? '')).toUpperCase();
});
</script>

<template>
    <header v-if="block.kind === 'header'" class="b-header">
        <img v-if="block.photoUrl" :src="block.photoUrl" alt="" class="photo" />
        <span v-else-if="initials" class="monogram" aria-hidden="true">{{
            initials
        }}</span>
        <div class="header-text">
            <h1 v-if="block.name" class="name">{{ block.name }}</h1>
            <p v-if="block.headline" class="headline">{{ block.headline }}</p>
            <p v-if="block.contacts.length" class="contacts">
                <ContactList :contacts="block.contacts" separator="  ·  " />
            </p>
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
