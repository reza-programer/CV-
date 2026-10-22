import type { InjectionKey } from 'vue';
import { inject, provide } from 'vue';
import type { ResumeEditor } from '@/composables/useResumeEditor';

export type EditorContext = ResumeEditor & {
    assistantEnabled: boolean;
    resumeId: number;
};

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
