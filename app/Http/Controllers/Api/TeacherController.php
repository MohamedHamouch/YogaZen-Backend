<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = Teacher::with('courses')->get();
        return response()->json($teachers);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:teachers,email',
            'password' => 'required|string|min:8',
            'bio' => 'required|string',
            'specialties' => 'required|array'
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $teacher = Teacher::create($validated);
        return response()->json($teacher->load('courses'), 201);
    }

    public function show(Teacher $teacher)
    {
        return response()->json($teacher->load('courses'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:teachers,email,' . $teacher->id,
            'password' => 'sometimes|string|min:8',
            'bio' => 'sometimes|string',
            'specialties' => 'sometimes|array'
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $teacher->update($validated);
        return response()->json($teacher->load('courses'));
    }

    public function destroy(Teacher $teacher)
    {
        $teacher->delete();
        return response()->json(['message' => 'Teacher deleted successfully']);
    }
}