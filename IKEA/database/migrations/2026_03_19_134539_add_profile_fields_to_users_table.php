<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Profile photo
            $table->string('avatar')->nullable()->after('email');

            // Delivery address
            $table->string('phone')->nullable()->after('avatar');
            $table->string('address')->nullable()->after('phone');
            $table->string('city')->nullable()->after('address');
            $table->string('province')->nullable()->after('city');
            $table->string('zip_code')->nullable()->after('province');

            // Preferred payment method
            $table->enum('payment_method', ['cod', 'gcash', 'bank_transfer'])
                  ->nullable()
                  ->after('zip_code');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'avatar', 'phone', 'address',
                'city', 'province', 'zip_code', 'payment_method',
            ]);
        });
    }
};