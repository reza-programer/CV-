<script setup lang="ts">
import { computed } from 'vue';
import type { Block } from '../../blocks';
import ContactList from '../parts/ContactList.vue';
import EntryFacts from '../parts/EntryFacts.vue';

/**
 * Professional ATS: firm hierarchy for experienced candidates. Each entry
 * leads with the organisation in bold with dates flush right, then the role
 * in italics with the location flush right. Projects and certificates keep
 * their own name as the lead line.
 */
const props = defineProps<{ block: Block }>();

/** Experience and education lead with where; projects and certificates with what. */
const lead = computed(() => {
    if (props.block.kind !== 'entry') {
        return { primary: '', secondary: '' };
    }

    const { entry } = props.block;
    const organisationFirst = [
        'experience',
        'internship',
        'organization',
        'education',
    ].includes(entry.section);

    return organisationFirst && entry.subtitle
        ? { primary: entry.subtitle, secondary: entry.title }
        : { primary: entry.title, secondary: entry.subtitle };
});
</script>

<template>
    <header v-if="block.kind === 'header'" class="b-header">
        <div class="header-text">
            <h1 v-if="block.name" class="name">{{ block.name }}</h1>
            <p v-if="block.headline" class="headline">{{ block.headline }}</p>
            <p v-if="block.contacts.length" class="contacts">
                <ContactList :contacts="block.contacts" />
            </p>
        </div>
        <img v-if="block.photoUrl" :src="block.photoUrl" alt="" class="photo" />
    </header>

    <div v-else-if="block.kind === 'heading'" class="b-heading">
        <h2 class="heading-text">{{ block.title }}</h2>
    </div>

    <p v-else-if="block.kind === 'text'" class="b-text">{{ block.text }}</p>

    <div v-else-if="block.kind === 'entry'" class="b-entry">
        <div class="entry-row">
            <strong class="entry-lead">{{ lead.primary }}</strong>
            <span v-if="block.entry.dates" class="entry-aside nowrap">{{
                block.entry.dates
            }}</span>
        </div>
        <div
            v-if="
                lead.secondary ||
                block.entry.location ||
                block.entry.details.length ||
                block.entry.link
            "
            class="entry-row"
        >
            <span class="entry-sub">
                <em v-if="lead.secondary">{{ lead.secondary }}</em>
                <span
                    v-if="block.entry.details.length || block.entry.link"
                    class="entry-details"
                >
                    <template v-if="lead.secondary">{{ ' · ' }}</template>
                    <EntryFacts
                        :entry="block.entry"
                        :fields="['details', 'link']"
                    />
                </span>
            </span>
            <span v-if="block.entry.location" class="entry-aside nowrap">{{
                block.entry.location
            }}</span>
        </div>
    </div>

    <div v-else-if="block.kind === 'bullet'" class="b-bullet">
        <span class="bullet-mark">•</span>
        <span class="bullet-text">{{ block.text }}</span>
    </div>

    <p v-else-if="block.kind === 'line'" class="b-line">
        <strong v-if="block.label">{{ block.label }}: </strong>{{ block.value }}
    </p>
</template>
