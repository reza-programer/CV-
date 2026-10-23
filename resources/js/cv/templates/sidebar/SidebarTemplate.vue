<script setup lang="ts">
import type { Block } from '../../blocks';
import type { Region } from '../../regions';
import EntryFacts from '../parts/EntryFacts.vue';
import IconContactList from '../parts/IconContactList.vue';

/**
 * Sidebar: a full-height accent panel on the left holds the round photo,
 * contacts, education, skills and languages in white; the main column
 * carries the name and the experience timeline.
 */
withDefaults(defineProps<{ block: Block; region?: Region }>(), {
    region: 'main',
});
</script>

<template>
    <header
        v-if="block.kind === 'header' && block.part === 'contact'"
        class="b-header b-contact-card"
    >
        <img
            v-if="block.photoUrl"
            :src="block.photoUrl"
            alt=""
            class="photo is-round"
        />
        <template v-if="block.contacts.length">
            <h2 class="aside-title">{{ block.contactTitle }}</h2>
            <p class="contacts">
                <IconContactList :contacts="block.contacts" stacked />
            </p>
        </template>
    </header>

    <header v-else-if="block.kind === 'header'" class="b-header b-identity">
        <h1 v-if="block.name" class="name">{{ block.name }}</h1>
        <p v-if="block.headline" class="headline">{{ block.headline }}</p>
    </header>

    <div v-else-if="block.kind === 'heading'" class="b-heading">
        <h2 class="heading-text">{{ block.title }}</h2>
    </div>

    <p v-else-if="block.kind === 'text'" class="b-text">{{ block.text }}</p>

    <div
        v-else-if="block.kind === 'entry' && region === 'aside'"
        class="b-entry"
    >
        <p v-if="block.entry.dates" class="entry-dates">
            {{ block.entry.dates }}
        </p>
        <p>
            <strong>{{ block.entry.title || block.entry.subtitle }}</strong>
        </p>
        <p v-if="block.entry.title && block.entry.subtitle" class="entry-sub">
            {{ block.entry.subtitle }}
        </p>
        <p
            v-if="block.entry.details.length || block.entry.link"
            class="entry-sub"
        >
            <EntryFacts :entry="block.entry" :fields="['details', 'link']" />
        </p>
    </div>

    <div v-else-if="block.kind === 'entry'" class="b-entry is-timeline">
        <p class="entry-dates">
            <EntryFacts
                :entry="block.entry"
                :fields="
                    block.entry.title
                        ? ['dates', 'subtitle', 'location']
                        : ['dates', 'location']
                "
                separator="  |  "
            />
        </p>
        <p class="entry-title">
            <strong>{{ block.entry.title || block.entry.subtitle }}</strong>
        </p>
        <p
            v-if="block.entry.details.length || block.entry.link"
            class="entry-sub"
        >
            <EntryFacts :entry="block.entry" :fields="['details', 'link']" />
        </p>
    </div>

    <div
        v-else-if="block.kind === 'bullet'"
        class="b-bullet"
        :class="{ 'is-timeline': region === 'main' }"
    >
        <span class="bullet-mark">•</span>
        <span class="bullet-text">{{ block.text }}</span>
    </div>

    <div v-else-if="block.kind === 'line' && region === 'aside'" class="b-line">
        <p v-if="block.label" class="line-label">{{ block.label }}</p>
        <ul class="stack-list">
            <li v-for="item in block.items" :key="item">• {{ item }}</li>
        </ul>
    </div>

    <p v-else-if="block.kind === 'line'" class="b-line">
        <strong v-if="block.label">{{ block.label }}: </strong>{{ block.value }}
    </p>
</template>
