<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds payment_method, transaction_type, and recorded_by columns
     * to support SPP payment tracking and savings deposit/withdrawal features.
     */
    public function up(): void
    {
        Schema::table('finances', function (Blueprint $table) {
            // Payment method for SPP: cash or transfer
            $table->enum('payment_method', ['cash', 'transfer'])->nullable()->after('is_paid');

            // Transaction type for savings: deposit or withdrawal
            $table->enum('transaction_type', ['deposit', 'withdrawal'])->nullable()->after('payment_method');

            // Who recorded this transaction
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete()->after('transaction_type');

            // Additional index for efficient queries
            $table->index('payment_method');
            $table->index('transaction_type');
            $table->index('recorded_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('finances', function (Blueprint $table) {
            $table->dropIndex(['payment_method']);
            $table->dropIndex(['transaction_type']);
            $table->dropIndex(['recorded_by']);
            $table->dropForeign(['recorded_by']);
            $table->dropColumn(['payment_method', 'transaction_type', 'recorded_by']);
        });
    }
};
