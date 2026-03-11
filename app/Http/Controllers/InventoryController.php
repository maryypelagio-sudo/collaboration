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
    public function index()
    {
        return Inertia::render('Inventory/Index', [
            'items' => Item::with('category')->get(),
            'categories' => Category::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:items,sku',
            'quantity' => 'required|integer|min:0',
            'active_quantity' => 'required|integer|min:0',
            'damaged_quantity' => 'required|integer|min:0',
            'unit' => 'required|string',
            'min_stock_level' => 'required|integer|min:0',
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
            'active_quantity' => 'required|integer|min:0',
            'damaged_quantity' => 'required|integer|min:0',
            'unit' => 'required|string',
            'min_stock_level' => 'required|integer|min:0',
        ]);

        $item->update($validated);

        return back()->with('success', 'Item updated successfully.');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return back()->with('success', 'Item deleted successfully.');
    }

    public function adjustStock(Request $request, Item $item)
    {
        $validated = $request->validate([
            'type' => 'required|in:in,out,adjustment,active_in,active_out,damaged_in,damaged_out',
            'quantity' => 'required|integer',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $item) {
            $oldQuantity = $item->quantity;

            if ($validated['type'] === 'adjustment') {
                $item->quantity = $validated['quantity'];
                $logQuantity = $validated['quantity'] - $oldQuantity;
            } elseif ($validated['type'] === 'active_in') {
                $item->active_quantity += $validated['quantity'];
                $item->quantity -= $validated['quantity']; // Move from Available to Active
                $logQuantity = $validated['quantity'];
            } elseif ($validated['type'] === 'active_out') {
                $item->active_quantity -= $validated['quantity'];
                $item->quantity += $validated['quantity']; // Move from Active to Available
                $logQuantity = -$validated['quantity'];
            } elseif ($validated['type'] === 'damaged_in') {
                $item->damaged_quantity += $validated['quantity'];
                $item->quantity -= $validated['quantity']; // Move from Available to Damaged
                $logQuantity = $validated['quantity'];
            } elseif ($validated['type'] === 'damaged_out') {
                $item->damaged_quantity -= $validated['quantity'];
                $logQuantity = -$validated['quantity'];
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
