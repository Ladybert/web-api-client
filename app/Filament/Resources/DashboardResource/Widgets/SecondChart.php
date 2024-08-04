<?php
 
namespace App\Filament\Widgets;
 
use Filament\Widgets\ChartWidget;
 
class SecondChart extends ChartWidget
{
    protected static ?string $heading = 'Average income per year';
 
    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'User Active created',
                    'barPercentage'=> 0.5,
                    'barThickness'=> 10,
                    'maxBarThickness'=> 10,
                    'minBarLength'=> 2,
                    'data' => [5000000, 2000000, 21000000, 32000000, 45000000, 74000000, 65000000, 45000000, 77000000, 89000000],
                ],
            ],
            'labels' => ['2015', '2016', '2017', '2018', '2019', '2020', '2021', '2022', '2023', '2024'],
        ];
    }
 
    protected function getType(): string
    {
        return 'bar';
    }

}