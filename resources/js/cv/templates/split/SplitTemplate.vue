<script setup lang="ts">
import type { Block } from '../../blocks';
import type { Region } from '../../regions';
import EntryFacts from '../parts/EntryFacts.vue';
import IconContactList from '../parts/IconContactList.vue';

/**
 * Split: black and white. Name on the left of a ruled header with icon
 * contacts on the right; below, the work history in a wide column and the
 * qualifications in a narrow one behind a hairline.
 */
withDefaults(defineProps<{ block: Block; region?: Region }>(), {
    region: 'main',
});
</script>

<template>
    <header v-if="block.kind === 'header'" class="b-header">
        <img v-if="block.photoUrl" :src="block.photoUrl" alt="" class="photo" />
        <div class="header-text">
            <h1 v-if="block.name" class="name">{{ block.name }}</h1>
            <p v-if="block.headline" class="headline">{{ block.headline }}</p>
        </div>
        <p v-if="block.contacts.length" class="contacts">
            <IconContactList :contacts="block.contacts" stacked />
        </p>
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
                separator=", "
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
        <p class="line-items">{{ block.value }}</p>
    </div>

    <p v-else-if="block.kind === 'line'" class="b-line">
        <strong v-if="block.label">{{ block.label }}: </strong>{{ block.value }}
    </p>
</template>
