<script setup lang="ts">
defineOptions({ inheritAttrs: false });
</script>

<template>
    <!-- A sheet with a folded corner and three text lines: the product makes documents. -->
    <svg
        viewBox="0 0 24 24"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
        aria-hidden="true"
        v-bind="$attrs"
    >
        <path
            d="M5 2.75h9.5L19.25 7.5v13.75H5z"
            stroke="currentColor"
            stroke-width="1.5"
            stroke-linejoin="round"
        />
        <path
            d="M14.25 2.75V7.75h5"
            stroke="currentColor"
            stroke-width="1.5"
            stroke-linejoin="round"
        />
        <path
            d="M8.25 11.5h7.5M8.25 14.5h7.5M8.25 17.5h4.5"
            stroke="currentColor"
            stroke-width="1.5"
            stroke-linecap="round"
        />
    </svg>
</template>
