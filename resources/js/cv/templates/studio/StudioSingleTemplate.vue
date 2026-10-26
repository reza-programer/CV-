<script setup lang="ts">
import type { Block } from '../../blocks';
import EntryFacts from '../parts/EntryFacts.vue';
import SectionIcon from '../parts/SectionIcon.vue';
import type { StudioOptions } from './options';
import StudioContacts from './StudioContacts.vue';

/**
 * Shared single-column markup for the studio templates: photo-led header,
 * section titles wrapped in a label (so a template can draw pills, tabs or
 * underlines), and entries with the dates flush right. Each template's
 * stylesheet decides the look and any page decoration.
 */
withDefaults(
    defineProps<{ block: Block; region?: string; options?: StudioOptions }>(),
    { region: 'main', options: () => ({}) },
);
</script>

<template>
    <header
        v-if="block.kind === 'header'"
        class="b-header"
        :class="{ 'has-photo': block.photoUrl }"
    >
        <img v-if="block.photoUrl" :src="block.photoUrl" alt="" class="photo" />
        <div class="header-text">
            <h1 v-if="block.name" class="name">{{ block.name }}</h1>
            <p v-if="block.headline" class="headline">{{ block.headline }}</p>
            <div v-if="block.contacts.length" class="contacts">
                <StudioContacts
                    :contacts="block.contacts"
                    :mode="options.contacts ?? 'icons'"
                />
            </div>
        </div>
    </header>

    <div v-else-if="block.kind === 'heading'" class="b-heading">
        <h2
            class="heading-text"
            :data-number="String(block.number).padStart(2, '0')"
        >
            <span v-if="options.sectionIcons" class="icon-badge"
                ><SectionIcon :section="block.section"
            /></span>
            <span class="heading-label">{{ block.title }}</span>
        </h2>
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
