<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ExerciseResource;


class ExerciseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Exercise::query();


        if ($request->has('name')) {
            $query->where('name', 'like', $request->name);
        }
        if($request->has('updated_at')) {
            $query->where('updated_at', $request->updated_at);
        }

        if ($request->has('created_at')) {
            $query->where('created_at', 'like', $request->created_at);
        }

        if ($request->has('sort_by')) {
            $sortOrder = $request->has('sort_order') ? $request->sort_order : 'asc';
            $query->orderBy($request->sort_by, $sortOrder);
        }
        return ExerciseResource::collection($query->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $validated['user_id'] = Auth::id();
        $exercise = Exercise::create($validated);
        return new ExerciseResource($exercise);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return new ExerciseResource(Exercise::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $exercise = Exercise::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $exercise->update($validated);
        return new ExerciseResource($exercise);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $exercise = Exercise::findOrFail($id);
        $exercise->delete();
        return response()->noContent();
    }
}
