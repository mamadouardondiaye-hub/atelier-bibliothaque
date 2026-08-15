<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\StatisticsService;
use Core\View;

class DashboardController extends Controller
{
    public function __construct(private StatisticsService $statistics) {}

    public function index(): string
    {
        return View::render('dashboard/index', [
            'title' => 'Tableau de bord',
            'stats' => $this->statistics->dashboard(),
        ]);
    }
}
