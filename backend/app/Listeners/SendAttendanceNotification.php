<?php

namespace App\Listeners;

use App\Events\AttendanceRecorded;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAttendanceNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AttendanceRecorded $event): void
    {
        $attendance = $event->attendance;
        
        // Log attendance notification (can be extended to send email/SMS)
        \Log::info('Attendance recorded', [
            'student_id' => $attendance->student_id,
            'date' => $attendance->date,
            'status' => $attendance->status,
            'recorded_by' => $attendance->recorded_by,
        ]);
        
        // Here you can add email/SMS notification logic
        // For example: Mail::to($attendance->student->email)->send(new AttendanceNotification($attendance));
    }
}
