import { ref } from 'vue';
import { RequestError, requestJson } from '@/lib/http';
import { assist } from '@/routes/resumes';
import { useEditor } from '../useEditorContext';

type Task = 'summary' | 'highlights' | 'keywords';

/** Shared across dialogs so a pasted job description is entered only once. */
const jobDescription = ref('');

export function useAssistant<T>(task: Task) {
    const editor = useEditor();
    const loading = ref(false);
    const error = ref('');
    const result = ref<T | null>(null);

    async function run(extra: Record<string, unknown> = {}): Promise<T | null> {
        // Only offered on a stored CV; the try-out editor has none.
        if (editor.guest) {
            return null;
        }

        loading.value = true;
        error.value = '';
        result.value = null;

        try {
            // The assistant reads the stored CV, so persist pending edits first.
            if (!(await editor.flush())) {
                error.value =
                    'Simpan CV terlebih dahulu: masih ada isian yang perlu diperbaiki.';

                return null;
            }

            result.value = await requestJson<T>({
                method: 'post',
                url: assist.url(editor.resumeId),
                data: {
                    task,
                    job_description: jobDescription.value.trim() || null,
                    ...extra,
                },
            });

            return result.value;
        } catch (exception) {
            const failure = exception as RequestError;
            error.value =
                Object.values(failure.errors ?? {})[0] ?? failure.message;

            return null;
        } finally {
            loading.value = false;
        }
    }

    function reset(): void {
        error.value = '';
        result.value = null;
    }

    return { jobDescription, loading, error, result, run, reset };
}
