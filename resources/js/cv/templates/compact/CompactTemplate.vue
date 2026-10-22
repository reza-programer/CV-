<script setup lang="ts">
import type { Block } from '../../blocks';
import ContactList from '../parts/ContactList.vue';
import EntryFacts from '../parts/EntryFacts.vue';

/**
 * Compact ATS: dense single column for long histories. Name and headline
 * share one line, and each entry fits its role, organisation and dates on a
 * single row so the bullets carry the space.
 */
defineProps<{ block: Block }>();
</script>

<template>
    <header v-if="block.kind === 'header'" class="b-header">
        <div class="header-text">
            <p class="title-line">
                <strong v-if="block.name" class="name">{{ block.name }}</strong>
                <template v-if="block.name && block.headline">{{
                    ' — '
                }}</template>
                <span v-if="block.headline" class="headline">{{
                    block.headline
                }}</span>
            </p>
            <p v-if="block.contacts.length" class="contacts">
                <ContactList :contacts="block.contacts" separator=" · " />
            </p>
        </div>
        <img v-if="block.photoUrl" :src="block.photoUrl" alt="" class="photo" />
    </header>

    <div v-else-if="block.kind === 'heading'" class="b-heading">
        <h2 class="heading-text">{{ block.title }}</h2>
    </div>

    <p v-else-if="block.kind === 'text'" class="b-text">{{ block.text }}</p>

    <div v-else-if="block.kind === 'entry'" class="b-entry">
        <p class="entry-main">
            <strong v-if="block.entry.title">{{ block.entry.title }}</strong>
            <template v-if="block.entry.title && block.entry.subtitle">{{
                ', '
            }}</template>
            <span v-if="block.entry.subtitle">{{ block.entry.subtitle }}</span>
            <span
                v-if="
                    block.entry.location ||
                    block.entry.details.length ||
                    block.entry.link
                "
                class="entry-extra"
            >
                {{ ' — '
                }}<EntryFacts
                    :entry="block.entry"
                    :fields="['location', 'details', 'link']"
                    separator=", "
                />
            </span>
        </p>
        <p v-if="block.entry.dates" class="entry-dates nowrap">
            {{ block.entry.dates }}
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
