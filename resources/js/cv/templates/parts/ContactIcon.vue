<script setup lang="ts">
import { Globe, Link, Mail, MapPin, Phone } from '@lucide/vue';
import type { ContactItem } from '../../blocks';

/**
 * Small icon shown next to a contact value. Decorative only: the value is
 * always printed as text beside it, so parsers never depend on the icon.
 */
defineProps<{ kind: ContactItem['kind'] }>();

const ICONS = {
    location: MapPin,
    phone: Phone,
    email: Mail,
    linkedin: Link,
    website: Globe,
};
</script>

<template>
    <component :is="ICONS[kind]" class="contact-icon" aria-hidden="true" />
</template>
