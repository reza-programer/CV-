<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Akun')
                    ->columns(2)
                    ->columnSpanFull()
                    ->components([
                        TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('password')
                            ->label('Kata sandi')
                            ->password()
                            ->revealable()
                            ->minLength(8)
                            ->required(fn ($livewire) => $livewire instanceof CreateRecord)
                            ->dehydrated(fn (?string $state) => filled($state))
                            ->helperText(fn ($livewire) => $livewire instanceof CreateRecord ? null : 'Kosongkan bila tidak ingin mengganti kata sandi.'),
                        Toggle::make('is_admin')
                            ->label('Akses panel admin')
                            ->helperText('Admin dapat mengelola pengguna dan template, tetapi tidak dapat membuka isi CV pengguna.')
                            ->inline(false),
                    ]),
            ]);
    }
}
