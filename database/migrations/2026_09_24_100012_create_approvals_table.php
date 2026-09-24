<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->morphs('approvable');
            $table->smallInteger('step'); // 1=Kabid, 2=Kadis
            $table->foreignId('approver_id')->constrained('users')->cascadeOnDelete();
            $table->string('decision')->default('pending'); // pending|approved|returned
            $table->text('notes')->nullable();
            $table->timestampTz('decided_at')->nullable();
            $table->timestampsTz();

            $table->index(['approvable_type', 'approvable_id', 'step']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approvals');
    }
};
