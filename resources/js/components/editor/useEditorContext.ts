import type { InjectionKey } from 'vue';
import { inject, provide } from 'vue';
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
