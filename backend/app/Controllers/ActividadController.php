<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\LogActividadModel;
use App\Views\LogActividadView;

class ActividadController extends Controller
{
    private LogActividadModel $logs;

    public function __construct()
    {
        parent::__construct();
        $this->logs = new LogActividadModel();
    }

    /** Lista los eventos de auditoría más recientes. */
    public function index(): void
    {
        $limit = (int) ($this->input('limit') ?? 50);
        $limit = max(1, min($limit, 200));

        $items = $this->logs->recent($limit);

        $this->json([
            'success' => true,
            'data' => LogActividadView::collection($items),
        ]);
    }
}

