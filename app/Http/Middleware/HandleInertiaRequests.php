<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user()?->only('id', 'name', 'email', 'is_admin'),
            ],
            'creator' => self::creator(),
        ];
    }

    /**
     * The site creator's name and contact links for the footer; empty
     * settings are left out.
     *
     * @return array{name: string, links: list<array{kind: string, label: string, href: string}>}
     */
    private static function creator(): array
    {
        $name = (string) config('cvforge.creator.name');
        $instagram = ltrim((string) config('cvforge.creator.instagram'), '@');
        $linkedin = (string) config('cvforge.creator.linkedin');
        $email = (string) config('cvforge.creator.email');

        return [
            'name' => $name,
            'links' => array_values(array_filter([
                $instagram !== '' ? ['kind' => 'instagram', 'label' => '@'.$instagram, 'href' => 'https://www.instagram.com/'.rawurlencode($instagram).'/'] : null,
                $linkedin !== '' ? ['kind' => 'linkedin', 'label' => $name !== '' ? $name : 'LinkedIn', 'href' => $linkedin] : null,
                $email !== '' ? ['kind' => 'email', 'label' => $email, 'href' => 'mailto:'.$email] : null,
            ])),
        ];
    }
}
