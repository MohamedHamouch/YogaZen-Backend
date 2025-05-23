<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\WooCommerceWebhookController;

// public routes
Route::prefix('auth')->group(function () {
    Route::post('student/login', [AuthController::class, 'studentLogin']);
    Route::post('teacher/login', [AuthController::class, 'teacherLogin']);
});

// routes both students and teachers
Route::middleware('auth.studentOrTeacher')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/getUser', [AuthController::class, 'getUser']);
    Route::post('auth/refresh', [AuthController::class, 'refresh']);
});

// routes for students
Route::middleware('auth:student')->group(function () {
    Route::apiResource('subscriptions', SubscriptionController::class);
    Route::apiResource('students', StudentController::class);
});

// routes for teachers
Route::middleware('auth:teacher')->group(function () {
    Route::apiResource('courses', CourseController::class);
    Route::apiResource('teachers', TeacherController::class);
});

// WooCommerce webhook
// Route::post('woo/webhook', [WooCommerceWebhookController::class, 'handleWebhook']);
