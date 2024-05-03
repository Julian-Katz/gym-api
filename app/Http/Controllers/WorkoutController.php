<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Workout;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\WorkoutResource;

class WorkoutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return WorkoutResource::collection(Workout::all());
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
        return new WorkoutResource($workout);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return new WorkoutResource(Workout::findOrFail($id));
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
        return new WorkoutResource($workout);
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
