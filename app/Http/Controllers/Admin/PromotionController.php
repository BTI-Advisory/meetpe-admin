<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function index()
    {
        $promotions = Promotion::latest()->get();
        $active     = Promotion::active()->first();

        return view('dashboard.promotions', compact('promotions', 'active'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label'               => 'required|string|max:255',
            'discount_percentage' => 'required|numeric|min:1|max:100',
            'starts_at'           => 'nullable|date',
            'ends_at'             => 'nullable|date|after_or_equal:starts_at',
        ]);

        Promotion::create($validated);

        return redirect()->route('promotions.index')->with('success', 'Promotion créée avec succès.');
    }

    public function toggle(Promotion $promotion)
    {
        if (!$promotion->is_active) {
            Promotion::where('id', '!=', $promotion->id)->update(['is_active' => false]);
        }

        $promotion->update(['is_active' => !$promotion->is_active]);

        $label = $promotion->is_active ? 'activée' : 'désactivée';

        return redirect()->route('promotions.index')->with('success', "Promotion « {$promotion->label} » $label.");
    }

    public function destroy(Promotion $promotion)
    {
        $promotion->delete();

        return redirect()->route('promotions.index')->with('success', 'Promotion supprimée.');
    }
}
