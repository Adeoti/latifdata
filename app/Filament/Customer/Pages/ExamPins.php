<?php

namespace App\Filament\Customer\Pages;

use Filament\Pages\Page;

class ExamPins extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static string $view = 'filament.customer.pages.exam-pins';

    protected static ?string $navigationLabel = "Exam Pins";
    protected ?string $subheading = "Currently Not Available ...";

    protected static ?int $navigationSort = 5;
}
