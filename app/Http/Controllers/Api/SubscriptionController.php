<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Subscription::with('student')->get();
        return response()->json($subscriptions);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'type' => ['required', Rule::in(['monthly', 'yearly', 'lifetime'])],
            'started_at' => 'required|date',
        ]);

        $startedAt = Carbon::parse($validated['started_at']);
        $expiresAt = null;
        if ($validated['type'] === 'monthly') {
            $expiresAt = $startedAt->copy()->addMonth();
        } elseif ($validated['type'] === 'yearly') {
            $expiresAt = $startedAt->copy()->addYear();
        } elseif ($validated['type'] === 'lifetime') {
            $expiresAt = $startedAt->copy()->addYears(100);
        }
        $validated['expires_at'] = $expiresAt;

        $subscription = Subscription::create($validated);
        
        $subscription->student->update([
            'subscription_expires_at' => $expiresAt
        ]);

        return response()->json($subscription->load('student'), 201);
    }

    public function show(Subscription $subscription)
    {
        return response()->json($subscription->load('student'));
    }

    public function update(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'student_id' => 'sometimes|exists:students,id',
            'type' => ['sometimes', Rule::in(['monthly', 'yearly', 'lifetime'])],
            'started_at' => 'sometimes|date',
        ]);

        if (isset($validated['type']) || isset($validated['started_at'])) {
            $startedAt = Carbon::parse($validated['started_at'] ?? $subscription->started_at);
            $type = $validated['type'] ?? $subscription->type;
            $expiresAt = null;
            if ($type === 'monthly') {
                $expiresAt = $startedAt->copy()->addMonth();
            } elseif ($type === 'yearly') {
                $expiresAt = $startedAt->copy()->addYear();
            } elseif ($type === 'lifetime') {
                $expiresAt = $startedAt->copy()->addYears(100);
            }
            $validated['expires_at'] = $expiresAt;
        }

        $subscription->update($validated);

        if (isset($validated['expires_at'])) {
            $subscription->student->update([
                'subscription_expires_at' => $validated['expires_at']
            ]);
        }

        return response()->json($subscription->load('student'));
    }

    public function destroy(Subscription $subscription)
    {
        $subscription->delete();
        return response()->json(['message' => 'Subscription deleted successfully']);
    }
}