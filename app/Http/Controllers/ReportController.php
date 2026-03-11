<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\StockLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Show the report page
     */
    public function index()
    {
        return inertia('Report');
    }

    /**
     * Generate and download the inventory PDF report
     */
    public function download()
    {
        // Fetch inventory data
        $items = Item::with('category')->get();
        $categories = Category::withCount('items')->get();
        
        $stats = [
            'total_items' => Item::count(),
            'total_categories' => Category::count(),
            'low_stock' => Item::whereColumn('quantity', '<=', 'min_stock_level')->count(),
        ];

        // Generate PDF with the data
        $pdf = Pdf::loadView('reports.inventory', [
            'items' => $items,
            'categories' => $categories,
            'stats' => $stats,
            'generated_at' => now()->format('Y-m-d H:i:s'),
        ]);

        // Download the PDF
        return $pdf->download('inventory-report-' . date('Y-m-d') . '.pdf');
    }
}

