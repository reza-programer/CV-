import { http } from '@inertiajs/vue3';

/**
 * JSON requests outside Inertia page visits (autosave, uploads, assistant).
 * Uses Inertia's HTTP client so the XSRF cookie is sent the same way.
 */

export type FieldErrors = Record<string, string>;

export class RequestError extends Error {
    constructor(
        message: string,
        readonly status: number,
        readonly errors: FieldErrors = {},
    ) {
        super(message);
    }

    get isValidation(): boolean {
        return this.status === 422;
    }
}

type RequestOptions = {
    method: 'get' | 'post' | 'put' | 'patch' | 'delete';
    url: string;
    data?: unknown;
    signal?: AbortSignal;
};

export async function requestJson<T>({
    method,
    url,
    data,
    signal,
}: RequestOptions): Promise<T> {
    try {
        const response = await http.getClient().request({
            method,
            url,
            data: data as never,
            signal,
            headers: { Accept: 'application/json' },
        });

        return (response.data ? JSON.parse(response.data as string) : {}) as T;
    } catch (error) {
        throw toRequestError(error);
    }
}

function toRequestError(error: unknown): RequestError {
    const response = (error as { response?: { status: number; data: string } })
        .response;

    if (!response) {
        return new RequestError(
            'Tidak dapat terhubung ke server. Periksa koneksi Anda.',
            0,
        );
    }

    let body: { message?: string; errors?: Record<string, string[]> } = {};

    try {
        body = JSON.parse(response.data);
    } catch {
        // Non-JSON error page; fall back to a generic message below.
    }

    const errors: FieldErrors = {};

    for (const [field, messages] of Object.entries(body.errors ?? {})) {
        errors[field] = messages[0];
    }

    const fallback =
        response.status === 419
            ? 'Sesi Anda berakhir. Muat ulang halaman lalu coba lagi.'
            : response.status === 429
              ? 'Terlalu banyak permintaan. Tunggu sebentar lalu coba lagi.'
              : 'Terjadi kesalahan. Silakan coba lagi.';

    return new RequestError(
        response.status === 422
            ? 'Periksa kembali isian yang ditandai.'
            : body.message || fallback,
        response.status,
        errors,
    );
}
