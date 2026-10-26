<script setup lang="ts">
import { Form, Head, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AvatarController from '@/actions/App/Http/Controllers/Settings/AvatarController';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import DeleteUser from '@/components/DeleteUser.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import UserAvatar from '@/components/UserAvatar.vue';

const MAX_BYTES = 5 * 1024 * 1024;
const ACCEPTED = ['image/jpeg', 'image/png', 'image/webp'];

const page = usePage();
const user = computed(() => page.props.auth.user);

const input = ref<HTMLInputElement | null>(null);
const busy = ref<'upload' | 'remove' | null>(null);
const photoError = ref('');

function upload(event: Event): void {
    const file = (event.target as HTMLInputElement).files?.[0];
    (event.target as HTMLInputElement).value = '';

    if (!file) {
        return;
    }

    // The same checks the server makes, so a wrong file fails without waiting for an upload.
    if (!ACCEPTED.includes(file.type)) {
        photoError.value = 'Foto harus berformat JPG, PNG, atau WebP.';

        return;
    }

    if (file.size > MAX_BYTES) {
        photoError.value = 'Ukuran foto maksimal 5 MB.';

        return;
    }

    photoError.value = '';
    router.post(
        AvatarController.store.url(),
        { avatar: file },
        {
            forceFormData: true,
            preserveScroll: true,
            onStart: () => (busy.value = 'upload'),
            onError: (errors) => (photoError.value = errors.avatar ?? ''),
            onFinish: () => (busy.value = null),
        },
    );
}

function remove(): void {
    photoError.value = '';
    router.delete(AvatarController.destroy.url(), {
        preserveScroll: true,
        onStart: () => (busy.value = 'remove'),
        onFinish: () => (busy.value = null),
    });
}
</script>

<template>
    <Head title="Pengaturan profil" />

    <h1 class="sr-only">Pengaturan profil</h1>

    <div class="flex flex-col space-y-6">
        <Heading
            variant="small"
            title="Profil"
            description="Perbarui foto, nama, dan alamat email Anda"
        />

        <div class="grid gap-2">
            <Label>Foto profil</Label>
            <div class="mt-1 flex items-center gap-5">
                <UserAvatar :user="user" class="size-20 text-xl" />
                <div class="grid gap-2">
                    <div class="flex flex-wrap gap-2">
                        <Button
                            type="button"
                            variant="outline"
                            size="sm"
                            :disabled="busy !== null"
                            @click="input?.click()"
                        >
                            {{
                                busy === 'upload'
                                    ? 'Mengunggah…'
                                    : user.avatar_url
                                      ? 'Ganti foto'
                                      : 'Unggah foto'
                            }}
                        </Button>
                        <Button
                            v-if="user.avatar_url"
                            type="button"
                            variant="ghost"
                            size="sm"
                            :disabled="busy !== null"
                            @click="remove"
                        >
                            {{ busy === 'remove' ? 'Menghapus…' : 'Hapus' }}
                        </Button>
                    </div>
                    <p class="text-xs text-neutral-500">
                        JPG, PNG, atau WebP hingga 5 MB. Dipotong otomatis
                        menjadi persegi.
                    </p>
                </div>
                <input
                    ref="input"
                    type="file"
                    class="sr-only"
                    :accept="ACCEPTED.join(',')"
                    tabindex="-1"
                    aria-label="Pilih foto profil"
                    @change="upload"
                />
            </div>
            <InputError :message="photoError" />
        </div>

        <Form
            v-bind="ProfileController.update.form()"
            class="space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="name">Nama</Label>
                <Input
                    id="name"
                    class="mt-1 block w-full"
                    name="name"
                    :default-value="user.name"
                    required
                    autocomplete="name"
                    placeholder="Nama lengkap"
                />
                <InputError class="mt-2" :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="email">Alamat email</Label>
                <Input
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    name="email"
                    :default-value="user.email"
                    required
                    autocomplete="username"
                    placeholder="Alamat email"
                />
                <InputError class="mt-2" :message="errors.email" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing" data-test="update-profile-button"
                    >Simpan</Button
                >
            </div>
        </Form>
    </div>

    <DeleteUser />
</template>
