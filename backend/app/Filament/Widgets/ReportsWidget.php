<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Carbon\Carbon;

class ReportsWidget extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.widgets.reports-widget';
    protected static ?int $sort = 10;
    protected int | string | array $columnSpan = 'full';

    public $startDate;
    public $endDate;
    public $reportType = 'sales';

    public function mount(): void
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('reportType')
                    ->label('Type de rapport')
                    ->options([
                        'sales' => '📊 Rapport de Ventes',
                        'products' => '📦 Performance Produits',
                        'customers' => '👥 Comportement Clients',
                    ])
                    ->default('sales'),

                DatePicker::make('startDate')
                    ->label('Date de début')
                    ->default(Carbon::now()->startOfMonth())
                    ->maxDate(now()),

                DatePicker::make('endDate')
                    ->label('Date de fin')
                    ->default(Carbon::now()->endOfMonth())
                    ->maxDate(now())
                    ->after('startDate'),
            ]);
    }

    public function generatePDF()
    {
        $url = url('/api/reports/pdf?' . http_build_query([
            'type' => $this->reportType,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
        ]));

        $this->dispatch('open-url', url: $url);
    }

    public function generateExcel()
    {
        $url = url('/api/reports/excel?' . http_build_query([
            'type' => $this->reportType,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
        ]));

        $this->dispatch('open-url', url: $url);
    }

    public function viewReport()
    {
        $endpoint = match($this->reportType) {
            'products' => 'products',
            'customers' => 'customers',
            default => 'sales'
        };
        
        $url = url('/api/reports/' . $endpoint . '?' . http_build_query([
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
        ]));

        $this->dispatch('open-url', url: $url);
    }
}