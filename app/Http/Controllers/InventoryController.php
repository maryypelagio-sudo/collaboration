<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\StockLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');

        $query = Item::with('category');

        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false)->whereNull('archived_at');
        } elseif ($status === 'archived') {
            $query->where('is_active', false)->whereNotNull('archived_at');
        } elseif ($status === 'rarely_used') {
            $query->where('is_active', true);
        }

        $items = $query->get();

        return Inertia::render('Inventory/Index', [
            'items' => $items,
            'categories' => Category::all(),
            'stats' => [
                'total' => Item::count(),
                'active' => Item::active()->count(),
                'inactive' => Item::where('is_active', false)->whereNull('archived_at')->count(),
                'archived' => Item::where('is_active', false)->whereNotNull('archived_at')->count(),
                'rarely_used' => Item::active()->get()->filter->is_rarely_used->count(),
            ],
            'status' => $status,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:items,sku',
            'quantity' => 'required|integer|min:0',
            'unit' => 'required|string',
            'min_stock_level' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        DB::transaction(function () use ($validated) {
            $item = Item::create($validated);

            if ($item->quantity > 0) {
                StockLog::create([
                    'item_id' => $item->id,
                    'user_id' => auth()->id(),
                    'type' => 'in',
                    'quantity' => $item->quantity,
                    'notes' => 'Initial stock on creation',
                ]);
            }
        });

        return back()->with('success', 'Item created successfully.');
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:items,sku,' . $item->id,
            'unit' => 'required|string',
            'min_stock_level' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
        ]);

        $item->update($validated);

        return back()->with('success', 'Item updated successfully.');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return back()->with('success', 'Item deleted successfully.');
    }

    public function archive(Request $request, Item $item)
    {
        $validated = $request->validate([
            'archive_reason' => 'required|string|max:500',
        ]);

        if (! $item->is_active) {
            return back()->with('error', 'Item is already archived.');
        }

        $item->update([
            'is_active' => false,
            'archive_reason' => $validated['archive_reason'],
            'archived_at' => now(),
        ]);

        return back()->with('success', 'Item archived successfully.');
    }

    public function restore(Item $item)
    {
        if ($item->is_active) {
            return back()->with('error', 'Item is already active.');
        }

        $item->update([
            'is_active' => true,
            'archive_reason' => null,
            'archived_at' => null,
        ]);

        return back()->with('success', 'Item restored successfully.');
    }

    public function adjustStock(Request $request, Item $item)
    {
        $validated = $request->validate([
            'type' => 'required|in:in,out,adjustment',
            'quantity' => 'required|integer',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $item) {
            $oldQuantity = $item->quantity;

            if ($validated['type'] === 'adjustment') {
                $item->quantity = $validated['quantity'];
                $logQuantity = $validated['quantity'] - $oldQuantity;
            } else {
                $multiplier = $validated['type'] === 'in' ? 1 : -1;
                $item->quantity += ($validated['quantity'] * $multiplier);
                $logQuantity = $validated['quantity'] * $multiplier;
            }

            $item->save();

            StockLog::create([
                'item_id' => $item->id,
                'user_id' => auth()->id(),
                'type' => $validated['type'],
                'quantity' => $logQuantity,
                'notes' => $validated['notes'],
            ]);
        });

        return back()->with('success', 'Stock adjusted successfully.');
    }
}
