<?php

namespace App\Filament\Resources\CvTemplates;

use App\Filament\Resources\CvTemplates\Pages\EditCvTemplate;
use App\Filament\Resources\CvTemplates\Pages\ListCvTemplates;
use App\Filament\Resources\CvTemplates\Schemas\CvTemplateForm;
use App\Filament\Resources\CvTemplates\Tables\CvTemplatesTable;
use App\Models\CvTemplate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

/**
 * Templates are implemented as layout components in the frontend, so the
 * panel edits their metadata only: no creating or deleting rows.
 */
class CvTemplateResource extends Resource
{
    protected static ?string $model = CvTemplate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'template';

    protected static ?string $pluralModelLabel = 'template';

    protected static ?string $navigationLabel = 'Template CV';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return CvTemplateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CvTemplatesTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCvTemplates::route('/'),
            'edit' => EditCvTemplate::route('/{record}/edit'),
        ];
    }
}
