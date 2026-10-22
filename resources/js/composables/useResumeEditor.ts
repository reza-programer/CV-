import { router } from '@inertiajs/vue3';
import {
    computed,
    onBeforeUnmount,
    onMounted,
    reactive,
    ref,
    toRaw,
    watch,
} from 'vue';
import type { ResumeData } from '@/cv/types';
import type { FieldErrors } from '@/lib/http';
import { RequestError, requestJson } from '@/lib/http';
import { update } from '@/routes/resumes';

export type SaveState = 'saved' | 'pending' | 'saving' | 'invalid' | 'failed';

type ListKey =
    | 'experiences'
    | 'activities'
    | 'educations'
    | 'projects'
    | 'skill_groups'
    | 'certifications'
    | 'languages';

const AUTOSAVE_DELAY = 1200;

/**
 * Editor state for one CV: the live document, autosave, and server-side
 * validation errors keyed by field path (e.g. "experiences.0.end_date").
 *
 * Every change is saved automatically after a short pause. Saves never
 * overlap: edits made while a request is in flight are sent right after it.
 */
export function useResumeEditor(
    initial: ResumeData,
    initialSavedAt: string | null,
) {
    // Page props are reactive proxies, which structuredClone rejects.
    const resume = reactive<ResumeData>(JSON.parse(JSON.stringify(initial)));
    const errors = ref<FieldErrors>({});
    const state = ref<SaveState>('saved');
    const failure = ref('');
    const savedAt = ref<string | null>(initialSavedAt);

    let revision = 0;
    let savedRevision = 0;
    let timer: number | undefined;
    let inFlight: Promise<boolean> | null = null;
    let lastSnapshot = snapshot();

    /** Uploads that change the stored CV outside the autosave payload (the photo). */
    const tasks = new Set<Promise<unknown>>();

    // Compare the serialised payload rather than reacting to every mutation:
    // the photo URL is saved by its own endpoint, and an edit that is undone
    // before the next save should not cost a request.
    watch(
        resume,
        () => {
            const next = snapshot();

            if (next === lastSnapshot) {
                return;
            }

            lastSnapshot = next;
            revision += 1;

            if (!inFlight) {
                state.value = 'pending';
            }

            schedule();
        },
        { deep: true },
    );

    function schedule(delay = AUTOSAVE_DELAY): void {
        window.clearTimeout(timer);
        timer = window.setTimeout(() => void save(), delay);
    }

    function payload(): Omit<ResumeData, 'id' | 'photo_url'> {
        const {
            id: _id,
            photo_url: _photo,
            ...data
        } = JSON.parse(JSON.stringify(toRaw(resume))) as ResumeData;

        return data;
    }

    function snapshot(): string {
        return JSON.stringify(payload());
    }

    /** Keep `flush()` waiting until the given upload has finished. */
    function track<T>(task: Promise<T>): Promise<T> {
        tasks.add(task);
        void task.finally(() => tasks.delete(task)).catch(() => undefined);

        return task;
    }

    async function save(): Promise<boolean> {
        window.clearTimeout(timer);

        if (inFlight) {
            await inFlight;

            return revision === savedRevision
                ? state.value === 'saved'
                : save();
        }

        if (revision === savedRevision && state.value === 'saved') {
            return true;
        }

        const target = revision;
        state.value = 'saving';

        inFlight = requestJson<{ saved_at: string }>({
            method: 'put',
            url: update.url(resume.id!),
            data: payload(),
        })
            .then((response) => {
                savedRevision = target;
                savedAt.value = response.saved_at;
                errors.value = {};
                failure.value = '';
                state.value = revision === target ? 'saved' : 'pending';

                return true;
            })
            .catch((error: unknown) => {
                const requestError =
                    error instanceof RequestError
                        ? error
                        : new RequestError('Gagal menyimpan.', 0);

                if (requestError.isValidation) {
                    errors.value = requestError.errors;
                    state.value = 'invalid';
                } else {
                    failure.value = requestError.message;
                    state.value = 'failed';
                }

                return false;
            })
            .finally(() => {
                inFlight = null;

                // Edits made during the request still need saving.
                if (revision !== target) {
                    schedule(400);
                }
            });

        return inFlight;
    }

    /** Save immediately and report whether the stored CV is up to date. */
    async function flush(): Promise<boolean> {
        window.clearTimeout(timer);
        await Promise.allSettled(tasks);

        if (revision === savedRevision && !inFlight) {
            return state.value === 'saved';
        }

        return save();
    }

    function error(path: string): string | undefined {
        return errors.value[path];
    }

    /** Errors are indexed by position; drop a list's errors when its order changes. */
    function forgetListErrors(list: ListKey): void {
        const prefix = `${list}.`;
        errors.value = Object.fromEntries(
            Object.entries(errors.value).filter(
                ([key]) => !key.startsWith(prefix),
            ),
        );
    }

    function addItem<K extends ListKey>(
        list: K,
        item: ResumeData[K][number],
    ): void {
        (resume[list] as Array<ResumeData[K][number]>).push(item);
    }

    function removeItem(list: ListKey, index: number): void {
        resume[list].splice(index, 1);
        forgetListErrors(list);
    }

    function moveItem(list: ListKey, from: number, to: number): void {
        const items = resume[list] as unknown[];

        if (to < 0 || to >= items.length) {
            return;
        }

        const [item] = items.splice(from, 1);
        items.splice(to, 0, item);
        forgetListErrors(list);
    }

    const hasUnsavedChanges = computed(() => state.value !== 'saved');

    const onBeforeUnload = (event: BeforeUnloadEvent) => {
        if (hasUnsavedChanges.value) {
            void save();
            event.preventDefault();
        }
    };

    let removeNavigationGuard: (() => void) | undefined;

    onMounted(() => {
        window.addEventListener('beforeunload', onBeforeUnload);

        removeNavigationGuard = router.on('before', (event) => {
            if (state.value === 'pending' || state.value === 'saving') {
                // Inertia visits keep the page alive, so the save completes.
                void save();

                return;
            }

            if (
                (state.value === 'invalid' || state.value === 'failed') &&
                event.detail.visit.method === 'get' &&
                !window.confirm(
                    'Perubahan terakhir belum tersimpan. Tetap tinggalkan halaman?',
                )
            ) {
                event.preventDefault();
            }
        });
    });

    onBeforeUnmount(() => {
        window.clearTimeout(timer);
        window.removeEventListener('beforeunload', onBeforeUnload);
        removeNavigationGuard?.();
    });

    return {
        resume,
        errors,
        state,
        failure,
        savedAt,
        save,
        flush,
        track,
        error,
        addItem,
        removeItem,
        moveItem,
    };
}

export type ResumeEditor = ReturnType<typeof useResumeEditor>;

/** Stable v-for keys for list items that have no id of their own. */
const itemKeys = new WeakMap<object, string>();
let keySequence = 0;

export function itemKey(item: object): string {
    const raw = toRaw(item);
    let key = itemKeys.get(raw);

    if (!key) {
        key = `item-${++keySequence}`;
        itemKeys.set(raw, key);
    }

    return key;
}
