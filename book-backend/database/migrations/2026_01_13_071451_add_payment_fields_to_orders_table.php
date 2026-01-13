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
        Schema::table('orders', function (Blueprint $table) {
            // Add payment related columns
            $table->decimal('amount_paid', 10, 2)->default(0)->after('total');
            $table->decimal('due_amount', 10, 2)->default(0)->after('amount_paid');
            
            // Update payment_status to include partial
            if (Schema::hasColumn('orders', 'payment_status')) {
                // For existing column, we'll handle the enum update differently
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['amount_paid', 'due_amount']);
        });
    }
};
