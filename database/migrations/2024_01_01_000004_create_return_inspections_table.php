<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('return_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_id')->constrained('rentals')->onDelete('cascade');
            $table->foreignId('checked_by_user_id')->constrained('users');
            $table->date('actual_return_date');
            $table->integer('late_days')->default(0);
            $table->decimal('late_fee_amount', 12, 2)->default(0);
            $table->enum('condition_status', ['good', 'minor_stain', 'damaged', 'incomplete_accessories']);
            $table->text('inspection_notes')->nullable();
            $table->decimal('damage_penalty_fee', 12, 2)->default(0);
            $table->decimal('total_deduction', 12, 2);
            $table->decimal('deposit_refunded', 12, 2);
            $table->decimal('extra_charge', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_inspections');
    }
};
