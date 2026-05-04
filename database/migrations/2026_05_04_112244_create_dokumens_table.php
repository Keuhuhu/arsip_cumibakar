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
        Schema::create('dokumens', function (Blueprint $table) {
            $table->id();
            $table->integer('no_urut');
            $table->string('no_surat', 100)->nullable();
            $table->string('perihal', 500);
            $table->date('tanggal_surat');
            $table->date('tanggal_masuk')->nullable();
            $table->string('asal_surat')->nullable();
            $table->string('tujuan_surat')->nullable();
            $table->foreignId('kategori_id')->constrained('kategoris');
            $table->string('jenis', 30);
            $table->string('status', 30)->default('aktif');
            $table->text('keterangan')->nullable();
            $table->string('file_path');
            $table->string('file_name');
            $table->unsignedBigInteger('file_size');
            $table->string('file_type');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->json('tags')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumens');
    }
};
