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
        Schema::create('agendas', function (Blueprint $table) {
            $table->id();
            $table->string('no_agenda', 50)->unique();
            $table->string('jenis', 30);
            $table->string('no_surat', 100)->nullable();
            $table->date('tanggal_surat');
            $table->date('tanggal_agenda');
            $table->string('perihal', 500);
            $table->string('asal')->nullable();
            $table->string('tujuan')->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('dokumen_id')->nullable()->constrained('dokumens')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agendas');
    }
};
