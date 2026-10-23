<script setup lang="ts">
import type { Block } from '../../blocks';
import type { Region } from '../../regions';
import EntryFacts from '../parts/EntryFacts.vue';
import IconContactList from '../parts/IconContactList.vue';

/**
 * Two Column: a full-width header with a round photo and icon contacts, the
 * profile across the page, then experience on the left and education,
 * skills and languages on the right, separated by rules.
 */
withDefaults(defineProps<{ block: Block; region?: Region }>(), {
    region: 'main',
});
</script>

<template>
    <header v-if="block.kind === 'header'" class="b-header">
        <img
            v-if="block.photoUrl"
            :src="block.photoUrl"
            alt=""
            class="photo is-round"
        />
        <div class="header-text">
            <h1 v-if="block.name" class="name">{{ block.name }}</h1>
            <p v-if="block.headline" class="headline">{{ block.headline }}</p>
            <p v-if="block.contacts.length" class="contacts">
                <IconContactList :contacts="block.contacts" stacked />
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
            <span
                v-if="block.entry.location && region === 'main'"
                class="entry-aside nowrap"
                >{{ block.entry.location }}</span
            >
        </div>
        <p
            v-if="
                (block.entry.title && block.entry.subtitle) ||
                block.entry.dates ||
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
                    (block.entry.dates ||
                        block.entry.details.length ||
                        block.entry.link)
                "
                >{{ ', ' }}</template
            >
            <EntryFacts
                :entry="block.entry"
                :fields="
                    region === 'main'
                        ? ['dates', 'details', 'link']
                        : ['location', 'dates', 'details', 'link']
                "
                separator=", "
            />
        </p>
    </div>

    <div v-else-if="block.kind === 'bullet'" class="b-bullet">
        <span class="bullet-mark">•</span>
        <span class="bullet-text">{{ block.text }}</span>
    </div>

    <div
        v-else-if="
            block.kind === 'line' &&
            region === 'aside' &&
            block.section === 'skills'
        "
        class="b-line"
    >
        <p v-if="block.label" class="line-label">{{ block.label }}</p>
        <p>{{ block.value }}</p>
    </div>

    <div v-else-if="block.kind === 'line' && region === 'aside'" class="b-line">
        <p v-if="block.label" class="line-label">{{ block.label }}</p>
        <ul class="stack-list">
            <li v-for="item in block.items" :key="item" class="list-item">
                <span class="bullet-mark">•</span>{{ item }}
            </li>
        </ul>
    </div>

    <p v-else-if="block.kind === 'line'" class="b-line">
        <strong v-if="block.label">{{ block.label }}: </strong>{{ block.value }}
    </p>
</template>
