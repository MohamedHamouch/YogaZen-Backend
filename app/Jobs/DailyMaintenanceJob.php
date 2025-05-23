<?php

namespace App\Jobs;

use App\Models\Student;
use App\Models\Subscription;
use App\Models\JobLog;
use App\Mail\SubscriptionReminderMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class DailyMaintenanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $this->sendSubscriptionReminders();
        $this->archiveInactiveStudents();
    }

    private function sendSubscriptionReminders()
    {
        $expiringSoon = Subscription::with('student')
            ->where('expires_at', '<=', now()->addDays(3))
            ->where('expires_at', '>', now())
            ->get();

        foreach ($expiringSoon as $subscription) {
            try {
                Mail::to($subscription->student->email)
                    ->send(new SubscriptionReminderMail($subscription));

                JobLog::logAction(
                    'subscription_reminder_sent',
                    'subscription',
                    $subscription->id,
                    "Reminder sent to {$subscription->student->email}",
                    'success'
                );
            } catch (\Exception $e) {
                JobLog::logAction(
                    'subscription_reminder_failed',
                    'subscription',
                    $subscription->id,
                    "Failed to send reminder: {$e->getMessage()}",
                    'failed'
                );
            }
        }
    }

    private function archiveInactiveStudents()
    {
        $inactiveStudents = Student::where('last_login_at', '<', now()->subMonths(12))
            ->where('status', '!=', 'archived')
            ->get();

        foreach ($inactiveStudents as $student) {
            try {
                $student->update(['status' => 'archived']);

                JobLog::logAction(
                    'student_archived',
                    'student',
                    $student->id,
                    "Student archived due to 12 months inactivity",
                    'success'
                );
            } catch (\Exception $e) {
                JobLog::logAction(
                    'student_archive_failed',
                    'student',
                    $student->id,
                    "Failed to archive student: {$e->getMessage()}",
                    'failed'
                );
            }
        }
    }
}