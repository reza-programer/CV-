<script setup lang="ts">
import type { Block } from '../../blocks';
import type { Region } from '../../regions';
import EntryFacts from '../parts/EntryFacts.vue';
import IconContactList from '../parts/IconContactList.vue';

/**
 * Sidebar Light: a pale tinted panel on the right with the round photo,
 * contacts, skill tags and languages; the main column leads with the name
 * and keeps the full work history in reading order before the panel.
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
            <span
                v-if="block.entry.title && block.entry.subtitle"
                class="entry-org"
                >{{ block.entry.subtitle }}</span
            >
            <template
                v-if="
                    block.entry.title &&
                    block.entry.subtitle &&
                    (block.entry.location ||
                        block.entry.details.length ||
                        block.entry.link)
                "
                >{{ ' · ' }}</template
            >
            <EntryFacts
                :entry="block.entry"
                :fields="['location', 'details', 'link']"
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
        <ul class="chips">
            <li v-for="item in block.items" :key="item" class="chip">
                {{ item }}
            </li>
        </ul>
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
