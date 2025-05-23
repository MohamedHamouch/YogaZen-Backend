<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with('subscriptions')->get();
        return response()->json($students);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email',
            'password' => 'required|string|min:8',
            'subscription_expires_at' => 'nullable|date',
            'status' => ['sometimes', Rule::in(['active', 'inactive', 'archived'])]
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $student = Student::create($validated);
        return response()->json($student->load('subscriptions'), 201);
    }

    public function show(Student $student)
    {
        return response()->json($student->load('subscriptions'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:students,email,' . $student->id,
            'password' => 'sometimes|string|min:8',
            'subscription_expires_at' => 'sometimes|nullable|date',
            'status' => ['sometimes', Rule::in(['active', 'inactive', 'archived'])]
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $student->update($validated);
        return response()->json($student->load('subscriptions'));
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return response()->json(['message' => 'Student deleted successfully']);
    }
}