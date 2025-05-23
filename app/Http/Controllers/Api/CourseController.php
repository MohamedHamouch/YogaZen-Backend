<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('teacher')->get();
        return response()->json($courses);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'level' => ['required', Rule::in(['beginner', 'intermediate', 'advanced'])],
            'duration' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'teacher_id' => 'required|exists:teachers,id'
        ]);

        $course = Course::create($validated);
        return response()->json($course->load('teacher'), 201);
    }

    public function show(Course $course)
    {
        return response()->json($course->load('teacher'));
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'level' => ['sometimes', Rule::in(['beginner', 'intermediate', 'advanced'])],
            'duration' => 'sometimes|integer|min:1',
            'price' => 'sometimes|numeric|min:0',
            'teacher_id' => 'sometimes|exists:teachers,id'
        ]);

        $course->update($validated);
        return response()->json($course->load('teacher'));
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return response()->json(['message' => 'Course deleted successfully']);
    }
}