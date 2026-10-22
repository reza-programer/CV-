<?php

namespace App\Filament\Resources\CvTemplates\Pages;

use App\Filament\Resources\CvTemplates\CvTemplateResource;
use Filament\Resources\Pages\ListRecords;

class ListCvTemplates extends ListRecords
{
    protected static string $resource = CvTemplateResource::class;

    public function getSubheading(): ?string
    {
        return 'Tata letak setiap template ada di kode aplikasi. Di sini Anda mengatur nama, deskripsi, urutan, dan ketersediaannya.';
    }
}
