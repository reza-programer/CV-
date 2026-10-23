<script setup lang="ts">
import { useId } from 'vue';

defineOptions({ inheritAttrs: false });

// Gradient ids must be unique when the logo appears more than once on a page.
const id = useId();
</script>

<template>
    <!--
        The kezot icon: a CV sheet with a photo and lines, a second sheet
        behind it, and a pencil. Same drawing as docs/brand/kezot-icon.svg.
    -->
    <svg
        viewBox="520 128 480 480"
        xmlns="http://www.w3.org/2000/svg"
        aria-hidden="true"
        v-bind="$attrs"
    >
        <defs>
            <linearGradient :id="`${id}-blue`" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0" stop-color="#2f8dff" />
                <stop offset="1" stop-color="#0656e2" />
            </linearGradient>
            <linearGradient :id="`${id}-paper`" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0" stop-color="#f8faff" />
                <stop offset="1" stop-color="#e7effc" />
            </linearGradient>
        </defs>
        <rect
            x="560"
            y="178"
            width="262"
            height="368"
            rx="24"
            :fill="`url(#${id}-blue)`"
            transform="rotate(-10 691 362)"
        />
        <path
            d="M629 209H848L920 272V563Q920 587 896 587H629Q605 587 605 563V233Q605 209 629 209Z"
            :fill="`url(#${id}-paper)`"
            stroke="#dfe8f6"
            stroke-width="1.5"
        />
        <path d="M850 211 916 270H862Q850 270 850 258Z" fill="#1c72f2" />
        <circle cx="697.5" cy="305" r="57.5" :fill="`url(#${id}-blue)`" />
        <circle cx="697.5" cy="287.5" r="16.5" fill="#ffffff" />
        <path
            d="M666 340C666 323 680 312 697.5 312S729 323 729 340Q697.5 355 666 340Z"
            fill="#ffffff"
        />
        <g stroke-linecap="round" stroke-width="15">
            <path d="M782.5 302.5H870" stroke="#4d92f2" />
            <path d="M782.5 335H843" stroke="#5f9df3" />
            <path d="M653.5 395H832" stroke="#2479f0" />
            <path
                d="M692.5 440H812M692.5 480H798M692.5 520H785"
                stroke="#94b8ef"
            />
        </g>
        <g fill="#1f74f0">
            <circle cx="656" cy="440" r="9.5" />
            <circle cx="656" cy="480" r="9.5" />
            <circle cx="656" cy="520" r="9.5" />
        </g>
        <g transform="translate(826 552.5) rotate(-55.4)">
            <path d="M0 0 40 -21V21Z" fill="#0f5fe6" />
            <rect
                x="47"
                y="-21"
                width="163"
                height="42"
                :fill="`url(#${id}-blue)`"
            />
            <path
                d="M217 -21H247A21 21 0 0 1 247 21H217Z"
                :fill="`url(#${id}-blue)`"
            />
        </g>
    </svg>
</template>
