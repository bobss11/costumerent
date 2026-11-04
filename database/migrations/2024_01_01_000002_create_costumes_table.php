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
        Schema::create('costumes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories');
            $table->string('code')->unique();
            $table->string('name');
            $table->enum('size', ['S', 'M', 'L', 'XL', 'All Size']);
            $table->enum('gender', ['Pria', 'Wanita', 'Unisex']);
            $table->decimal('rental_price_per_day', 12, 2);
            $table->decimal('deposit_amount', 12, 2);
            $table->decimal('late_fee_per_day', 12, 2);
            $table->integer('stock_total');
            $table->integer('stock_available');
            $table->text('accessories_included');
            $table->string('image_url');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('costumes');
    }
};
