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
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no', 50)->unique();
            $table->enum('type', ['in', 'out'])->index(); // in = barang masuk, out = barang keluar
            $table->date('transaction_date')->index();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->string('recipient', 150)->nullable(); // Penerima / Tujuan barang keluar
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('total_items')->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
    }
};
