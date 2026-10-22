<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { Copy, Download, EllipsisVertical, PenLine, Trash2 } from '@lucide/vue';
import { computed, ref } from 'vue';
import CvThumbnail from '@/components/cv/CvThumbnail.vue';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { destroy, duplicate, edit, pdf } from '@/routes/resumes';
import type { ResumeListItem } from '@/types/resume';

const props = defineProps<{ resume: ResumeListItem }>();

const confirmingDelete = ref(false);
const busy = ref(false);

const updated = computed(() => relativeTime(props.resume.updated_at));

function relativeTime(iso: string): string {
    const seconds = (new Date(iso).getTime() - Date.now()) / 1000;
    const format = new Intl.RelativeTimeFormat('id', { numeric: 'auto' });
    const steps: Array<[Intl.RelativeTimeFormatUnit, number]> = [
        ['minute', 60],
        ['hour', 3600],
        ['day', 86400],
        ['month', 2592000],
        ['year', 31536000],
    ];

    if (Math.abs(seconds) < 60) {
        return 'baru saja';
    }

    for (let index = steps.length - 1; index >= 0; index--) {
        const [unit, size] = steps[index];

        if (Math.abs(seconds) >= size) {
            return format.format(Math.round(seconds / size), unit);
        }
    }

    return format.format(Math.round(seconds / 60), 'minute');
}

function duplicateResume(): void {
    router.post(duplicate.url(props.resume.id), {}, { preserveScroll: true });
}

function deleteResume(): void {
    busy.value = true;
    router.delete(destroy.url(props.resume.id), {
        preserveScroll: true,
        onFinish: () => {
            busy.value = false;
            confirmingDelete.value = false;
        },
    });
}
</script>

<template>
    <article class="group grid min-w-0 grid-cols-1 gap-3">
        <Link
            :href="edit(resume.id)"
            class="block overflow-hidden rounded-sm border border-neutral-200 transition-colors group-hover:border-neutral-400 focus-visible:ring-2 focus-visible:ring-neutral-400 focus-visible:outline-none"
            :aria-label="`Buka ${resume.title}`"
        >
            <CvThumbnail :data="resume.document" />
        </Link>

        <div class="flex items-start gap-2">
            <div class="min-w-0 flex-1">
                <h3 class="truncate text-sm font-medium text-neutral-900">
                    <Link :href="edit(resume.id)" class="hover:underline">{{
                        resume.title
                    }}</Link>
                </h3>
                <p class="truncate text-xs text-neutral-500">
                    {{ resume.template_name }} · diubah {{ updated }}
                </p>
            </div>

            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <Button
                        variant="ghost"
                        size="icon-sm"
                        class="-mr-1.5"
                        :aria-label="`Tindakan untuk ${resume.title}`"
                    >
                        <EllipsisVertical />
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end" class="w-44">
                    <DropdownMenuItem as-child>
                        <Link :href="edit(resume.id)"><PenLine /> Edit</Link>
                    </DropdownMenuItem>
                    <DropdownMenuItem as-child>
                        <a :href="pdf.url(resume.id)" download
                            ><Download /> Unduh PDF</a
                        >
                    </DropdownMenuItem>
                    <DropdownMenuItem @select="duplicateResume"
                        ><Copy /> Duplikat</DropdownMenuItem
                    >
                    <DropdownMenuSeparator />
                    <DropdownMenuItem
                        class="text-red-600 focus:text-red-700"
                        @select="confirmingDelete = true"
                    >
                        <Trash2 class="text-current" /> Hapus
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </div>

        <AlertDialog v-model:open="confirmingDelete">
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle
                        >Hapus "{{ resume.title }}"?</AlertDialogTitle
                    >
                    <AlertDialogDescription>
                        CV beserta fotonya akan dihapus permanen dan tidak dapat
                        dikembalikan.
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel :disabled="busy"
                        >Batal</AlertDialogCancel
                    >
                    <AlertDialogAction
                        class="bg-red-600 hover:bg-red-700"
                        :disabled="busy"
                        @click.prevent="deleteResume"
                    >
                        Hapus CV
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </article>
</template>
