<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->text('shipping_address')->nullable()->change();
            $table->string('shipping_state')->nullable()->change();
            $table->string('shipping_zip')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->text('shipping_address')->nullable(false)->change();
            $table->string('shipping_state')->nullable(false)->change();
            $table->string('shipping_zip')->nullable(false)->change();
        });
    }
};
