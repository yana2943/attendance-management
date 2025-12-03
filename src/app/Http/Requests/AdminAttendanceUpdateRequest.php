<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class AdminAttendanceUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'clock_in'  => ['nullable', 'regex:/^\d{1,2}:\d{2}$/'],
            'clock_out' => ['nullable', 'regex:/^\d{1,2}:\d{2}$/'],
            'break_in'  => ['nullable', 'regex:/^\d{1,2}:\d{2}$/'],
            'break_out' => ['nullable', 'regex:/^\d{1,2}:\d{2}$/'],
            'note'      => ['required', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'note.required' => '備考を記入してください',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $startTime = $this->start_time ? Carbon::parse($this->start_time) : null;
            $endTime   = $this->end_time   ? Carbon::parse($this->end_time)   : null;

            $breakStart1 = $this->break_start ? Carbon::parse($this->break_start) : null;
            $breakEnd1   = $this->break_end   ? Carbon::parse($this->break_end)   : null;

            $breakStart2 = $this->break2_start ? Carbon::parse($this->break2_start) : null;
            $breakEnd2   = $this->break2_end   ? Carbon::parse($this->break2_end)   : null;

            if ($startTime && $endTime && $startTime->gt($endTime)) {
                $validator->errors()->add('start_time', '出勤時間もしくは退勤時間が不適切な値です');
            }

            $checkBreakStart = function($start, $key) use ($startTime, $endTime, $validator) {
                if ($start) {
                    if ($startTime && $start->lt($startTime)) {
                        $validator->errors()->add($key, '休憩時間が不適切な値です');
                    }
                    if ($endTime && $start->gt($endTime)) {
                        $validator->errors()->add($key, '休憩時間が不適切な値です');
                    }
                }
            };

            $checkBreakStart($breakStart1, 'break_start');
            $checkBreakStart($breakStart2, 'break2_start');

            $checkBreakEnd = function($start, $end, $key) use ($endTime, $validator) {
                if ($end) {
                    if ($start && $end->lt($start)) {
                        $validator->errors()->add($key, '休憩時間もしくは退勤時間が不適切な値です');
                    }
                    if ($endTime && $end->gt($endTime)) {
                        $validator->errors()->add($key, '休憩時間もしくは退勤時間が不適切な値です');
                    }
                }
            };

            $checkBreakEnd($breakStart1, $breakEnd1, 'break_end');
            $checkBreakEnd($breakStart2, $breakEnd2, 'break2_end');

            if (!$this->note || trim($this->note) === '') {
                $validator->errors()->add('note', '備考を記入してください');
            }

        });

    }
}
