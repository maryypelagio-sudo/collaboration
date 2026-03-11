<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Item;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class BorrowingController extends Controller
{
    public function index()
    {
        return Inertia::render('Borrowings/Index', [
            'borrowings' => Borrowing::with(['item', 'user'])
            ->orderBy('borrowed_at', 'desc')
            ->get(),
            'items' => Item::where('quantity', '>', 0)->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $item = Item::findOrFail($validated['item_id']);

        if ($item->quantity < $validated['quantity']) {
            return back()->withErrors(['quantity' => 'Not enough stock available.']);
        }

        Borrowing::create([
            'item_id' => $validated['item_id'],
            'user_id' => 1, // Defaulting to user 1 since auth is not fully set up
            'quantity' => $validated['quantity'],
            'notes' => $validated['notes'],
            'borrowed_at' => now(),
            'status' => 'borrowed',
        ]);

        $item->decrement('quantity', $validated['quantity']);

        return redirect()->back()->with('success', 'Equipment borrowed successfully.');
    }

    public function return (Borrowing $borrowing)
    {
        if ($borrowing->status === 'returned') {
            return back()->withErrors(['status' => 'Item already returned.']);
        }

        $borrowing->update([
            'returned_at' => now(),
            'status' => 'returned',
        ]);

        $borrowing->item->increment('quantity', $borrowing->quantity);

        return redirect()->back()->with('success', 'Equipment returned successfully.');
    }
}
