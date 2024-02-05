<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->string('code');
			$table->foreignId('customer_id')->constrained('customers');
			$table->date('tanggal');
			// $table->decimal('nominal');
            $table->decimal('nominal', 10, 2);
			$table->foreignId('kategori_id')->constrained('kategoris');
			$table->text('keterangan');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
