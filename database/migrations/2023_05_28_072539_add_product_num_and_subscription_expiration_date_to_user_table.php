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
        Schema::table('users', function (Blueprint $table) {
            $table->dateTime('subscription_expiration_date')->nullable();
            $table->boolean('subscription_status')->default(0);
            $table->boolean('number_of_products')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('subscription_expiration_date');
            $table->dropColumn('number_of_products');
            $table->dropColumn('subscription_status');
        });
    }
};
