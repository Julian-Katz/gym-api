<?php

namespace App\Http\Controllers;

use App\Http\Resources\SetResource;
use Illuminate\Http\Request;
use App\Models\Workout;
use App\Models\Set;

class SetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Workout $workout)
    {
        return SetResource::collection($workout->sets);
    }

    /**
     * Display the specified resource.
     */
    public function show(Workout $workout, Set $set)
    {
        return new SetResource($set);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Workout $workout)
    {
        $set = null;
        $validated = $request->validate([
            'exercise_id' => 'required|exists:exercises,id',
            'repetitions' => 'required_without:duration|integer',
            'duration' => 'required_without:repetitions|integer',
            'break_afterwards' => 'integer',
            'before_set' => 'integer',
            'position' => 'exclude'
        ]);
        $validated['workout_id'] = $workout->id;
        $validated['user_id'] = auth()->id();

        if (isset($validated['before_set'])) {
            $set = new SetResource(Set::createBefore($validated, $validated['before_set']));
        } else {
            $set = new SetResource(Set::createBefore($validated));
        }
        return $set;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Workout $workout, Set $set)
    {
        $validated = $request->validate([
            'exercise_id' => 'required|exists:exercises,id',
            'repetitions' => 'required_without:duration|integer',
            'duration' => 'required_without:repetitions|integer',
            'break_afterwards' => 'integer',
            'before_set' => 'integer',
            'position' => 'exclude'
        ]);
        $validated['workout_id'] = $workout->id;
        $validated['set_id'] = $set->id;
        $validated['user_id'] = auth()->id();

        if (isset($validated['before_set'])) {
            return new SetResource($set->updateBefore($validated, $validated['before_set']));
        } else {
            return new SetResource($set->updateBefore($validated));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Workout $workout, Set $set)
    {
        $set->delete();
        return response()->noContent();
    }
}
