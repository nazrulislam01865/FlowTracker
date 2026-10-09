<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inquiry_items', function (Blueprint $table): void {
            $table->foreignId('supplier_id')->nullable()->after('inquiry_id')
                ->constrained('master_records')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('inquiry_items', fn (Blueprint $table) => $table->dropConstrainedForeignId('supplier_id'));
    }
};
