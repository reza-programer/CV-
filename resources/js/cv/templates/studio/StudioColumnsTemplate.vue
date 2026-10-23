<script setup lang="ts">
import type { Block } from '../../blocks';
import type { Region } from '../../regions';
import ContactIcon from '../parts/ContactIcon.vue';
import EntryFacts from '../parts/EntryFacts.vue';
import SectionIcon from '../parts/SectionIcon.vue';
import type { StudioOptions } from './options';
import StudioContacts from './StudioContacts.vue';

/**
 * Shared two-column markup for the studio templates. With a split header
 * the photo and contacts open the side column and the name opens the main
 * one; with a top header the whole header runs across the page. Side-column
 * entries and lists are compact.
 */
withDefaults(
    defineProps<{ block: Block; region?: Region; options?: StudioOptions }>(),
    { region: 'main', options: () => ({}) },
);
</script>

<template>
    <header
        v-if="block.kind === 'header' && block.part === 'contact'"
        class="b-header b-contact-card"
    >
        <img v-if="block.photoUrl" :src="block.photoUrl" alt="" class="photo" />
        <template v-if="block.contacts.length">
            <h2 class="aside-title">
                <span v-if="options.sectionIcons" class="icon-badge"
                    ><ContactIcon kind="phone"
                /></span>
                <span class="heading-label">{{ block.contactTitle }}</span>
            </h2>
            <div class="contacts">
                <StudioContacts
                    :contacts="block.contacts"
                    :mode="options.contacts ?? 'icons-stacked'"
                />
            </div>
        </template>
    </header>

    <header
        v-else-if="block.kind === 'header' && block.part === 'identity'"
        class="b-header b-identity"
    >
        <h1 v-if="block.name" class="name">{{ block.name }}</h1>
        <p v-if="block.headline" class="headline">{{ block.headline }}</p>
    </header>

    <header
        v-else-if="block.kind === 'header'"
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
        <h2 class="heading-text">
            <span v-if="options.sectionIcons" class="icon-badge"
                ><SectionIcon :section="block.section"
            /></span>
            <span class="heading-label">{{ block.title }}</span>
        </h2>
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
        <ul class="stack-list">
            <li v-for="item in block.items" :key="item">{{ item }}</li>
        </ul>
    </div>

    <p v-else-if="block.kind === 'line'" class="b-line">
        <strong v-if="block.label">{{ block.label }}: </strong>{{ block.value }}
    </p>
</template>
