<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_opname_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('opname_no')->unique();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('draft'); // draft, running, waiting_approval, done, rejected
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_opname_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opname_session_id')->constrained('stock_opname_sessions')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('location_id')->constrained()->cascadeOnDelete();
            $table->decimal('system_qty', 15, 2)->default(0);
            $table->decimal('physical_qty', 15, 2)->default(0);
            $table->decimal('difference_qty', 15, 2)->default(0);
            $table->string('status')->default('counted'); // counted, recount, approved
            $table->string('photo')->nullable();
            $table->foreignId('counted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('counted_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['opname_session_id', 'product_id', 'location_id'], 'opname_line_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opname_lines');
        Schema::dropIfExists('stock_opname_sessions');
    }
};
