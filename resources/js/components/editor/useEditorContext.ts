import type { InjectionKey } from 'vue';
import { inject, nextTick, provide } from 'vue';
import type { ResumeEditor } from '@/composables/useResumeEditor';

/** What a visitor in the try-out editor tried to do that needs an account. */
export type AccountReason = 'save' | 'pdf' | 'photo';

export type EditorContext = ResumeEditor & {
    assistantEnabled: boolean;
} & (
        | { guest: false; resumeId: number }
        // Trying the editor before signing up: there is no stored CV yet.
        | {
              guest: true;
              resumeId: null;
              requireAccount: (reason: AccountReason) => void;
          }
    );

const EditorKey: InjectionKey<EditorContext> = Symbol('resume-editor');

export function provideEditor(context: EditorContext): void {
    provide(EditorKey, context);
}

export function useEditor(): EditorContext {
    const context = inject(EditorKey);

    if (!context) {
        throw new Error('useEditor() must be used inside the resume editor.');
    }

    return context;
}

/** Asks the workspace to show the form, which is hidden behind the preview on phones. */
export const SHOW_FORM_EVENT = 'editor:show-form';

/** Brings the first field with a validation error into view and focuses it. */
export async function showFirstError(): Promise<void> {
    window.dispatchEvent(new Event(SHOW_FORM_EVENT));
    await nextTick();

    const field = document.querySelector<HTMLElement>('[aria-invalid="true"]');
    field?.scrollIntoView({ block: 'center', behavior: 'smooth' });
    field?.focus({ preventScroll: true });
}
