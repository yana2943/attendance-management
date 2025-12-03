<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceCorrectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_corrections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attendance_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->date('work_date');
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->dateTime('break_start')->nullable();
            $table->dateTime('break_end')->nullable();
            $table->dateTime('break2_start')->nullable();
            $table->dateTime('break2_end')->nullable();
            $table->text('note')->nullable();
            $table->unsignedTinyInteger('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_corrections');
    }
}
