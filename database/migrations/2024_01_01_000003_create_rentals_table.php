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
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->string('rental_code')->unique();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('costume_id')->constrained('costumes');
            $table->integer('qty')->default(1);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('total_days');
            $table->decimal('rental_subtotal', 12, 2);
            $table->decimal('deposit_total', 12, 2);
            $table->decimal('grand_total', 12, 2);
            $table->string('guarantee_card_held')->nullable();
            $table->text('pickup_condition_notes')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'picked_up', 'returned', 'overdue', 'cancelled'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};
