<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Workout;
use Illuminate\Support\Facades\Auth;

class WorkoutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Workout::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->has('user_id') && $request['user_id'] !== Auth::id()) {
                return response([], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $validated['user_id'] = Auth::id();
        $workout = Workout::create($validated);
        return $workout;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Workout::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $workout = Workout::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $workout->update($validated);
        return $workout;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $workout = Workout::findOrFail($id);
        $workout->delete();
        return response()->noContent();
    }
}
