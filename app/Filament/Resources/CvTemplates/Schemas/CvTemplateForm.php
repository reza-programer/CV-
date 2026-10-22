<?php

namespace App\Filament\Resources\CvTemplates\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CvTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->columnSpanFull()
                    ->components([
                        TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(80),
                        TextInput::make('slug')
                            ->label('Kode tata letak')
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Menghubungkan baris ini ke komponen tata letak di kode; tidak dapat diubah.'),
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->helperText('Tampil di galeri template pada halaman utama dan saat membuat CV.')
                            ->maxLength(255)
                            ->rows(3)
                            ->columnSpanFull(),
                        TextInput::make('sort_order')
                            ->label('Urutan')
                            ->numeric()
                            ->minValue(0)
                            ->required(),
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->helperText('Template nonaktif tidak dapat dipilih untuk CV baru. CV yang sudah memakainya tetap dapat dibuka dan diunduh.')
                            ->inline(false),
                    ]),
            ]);
    }
}
