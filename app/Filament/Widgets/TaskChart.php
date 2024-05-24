<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class TaskChart extends ChartWidget
{
    protected static ?string $heading = 'Estado de las tareas de hoy';

    protected function getData(): array
    {
        $completed = Task::where('state', 1)->whereDate('created_at', Carbon::today())->count();
        $delay = Task::where('state', 2)->whereDate('created_at', Carbon::today())->count();
        $pending = Task::where('state', 3)->whereDate('created_at', Carbon::today())->count();
        return [
            'datasets' => [
                [
                    'label' => 'Estado de las tareas de hoy',
                    'data' => [$completed, $delay, $pending],
                    'backgroundColor' => ['#27ae60', '#e67e22', '#e74c3c'],
                ],
            ],
            'labels' => ['Completado', 'Atrasado', 'Pendientes'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
