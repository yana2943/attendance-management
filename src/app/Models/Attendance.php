<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'work_date',
        'start_time',
        'end_time',
        'break_start',
        'break_end',
        'break2_start',
        'break2_end',
        'status',
        'note',
        'is_pending',
    ];

    protected $casts = [
        'start_time'   => 'datetime',
        'end_time'     => 'datetime',
        'break_start'  => 'datetime',
        'break_end'    => 'datetime',
        'break2_start' => 'datetime',
        'break2_end'   => 'datetime',
        'work_date'    => 'date',
        'is_pending'   => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDisplayStatusAttribute()
    {
        return match($this->status) {
            '勤務外'   => '勤務外',
            '出勤中'   => '出勤中',
            '休憩中'   => '休憩中',
            '退勤済'   => '退勤済',
            'approved' => '承認済',
            default    => $this->user_status,
        };
    }


    public function corrections()
    {
        return $this->hasMany(AttendanceCorrection::class);
    }

    public function isWorking()
    {
        return $this->status === '出勤中';
    }

    public function isOnBreak()
    {
        return $this->status === '休憩中';
    }

    public function isFinished()
    {
        return $this->status === '退勤済';
    }

    public function getStatusLabelAttribute()
    {
        return match($this->approval_status) {
            '承認待ち' => '承認待ち',
            '承認済' => '承認済',
            default => '-',
        };
    }

    public function getClockInHhmmAttribute()
    {
        return $this->start_time
            ? Carbon::parse($this->start_time)->format('H:i')
            : '';
    }

    public function getClockOutHhmmAttribute()
    {
        return $this->end_time
            ? Carbon::parse($this->end_time)->format('H:i')
            : '';
    }

    public function getTotalBreakHhmmAttribute()
    {
        $total = 0;

        if ($this->break_start && $this->break_end) {
            $total += Carbon::parse($this->break_start)->diffInMinutes($this->break_end);
        }

        if ($this->break2_start && $this->break2_end) {
            $total += Carbon::parse($this->break2_start)->diffInMinutes($this->break2_end);
        }

        return sprintf('%02d:%02d', intdiv($total, 60), $total % 60);
    }


    public function getTotalWorkHhmmAttribute()
    {
        if (!$this->start_time || !$this->end_time) {
            return '';
        }

        $workMinutes = Carbon::parse($this->start_time)->diffInMinutes($this->end_time);

        $break = Carbon::parse($this->break_start)->diffInMinutes($this->break_end ?? now(), false) ?? 0;
        $break2 = Carbon::parse($this->break2_start)->diffInMinutes($this->break2_end ?? now(), false) ?? 0;

        $total = $workMinutes - ($break + $break2);

        return sprintf('%02d:%02d', intdiv(max($total, 0), 60), max($total, 0) % 60);
    }
}
