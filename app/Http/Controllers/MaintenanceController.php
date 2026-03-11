<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\MaintenanceLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MaintenanceController extends Controller
{
    public function index()
    {
        return Inertia::render('Maintenance/Index', [
            'logs' => MaintenanceLog::with(['item', 'reporter'])->latest()->get(),
            'items' => Item::where('status', '!=', 'in_maintenance')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'description' => 'required|string',
        ]);

        $item = Item::findOrFail($request->item_id);
        
        // Create maintenance log
        MaintenanceLog::create([
            'item_id' => $item->id,
            'reported_by' => auth()->id() ?? 1, // Fallback for dev if no auth
            'description' => $request->description,
            'status' => 'pending',
        ]);

        // Update item status
        $item->update(['status' => 'damaged']);

        return redirect()->back()->with('success', 'Damage reported successfully.');
    }

    public function update(Request $request, MaintenanceLog $log)
    {
        $request->validate([
            'status' => 'required|string|in:pending,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
            'cost' => 'nullable|numeric',
        ]);

        $log->update($request->only(['status', 'notes', 'cost']));

        $item = $log->item;

        if ($request->status === 'in_progress') {
            $log->update(['start_date' => now()]);
            $item->update(['status' => 'in_maintenance']);
        } elseif ($request->status === 'completed') {
            $log->update(['completion_date' => now()]);
            $item->update(['status' => 'available']);
        } elseif ($request->status === 'cancelled') {
            $item->update(['status' => 'available']);
        }

        return redirect()->back()->with('success', 'Maintenance status updated.');
    }
}
