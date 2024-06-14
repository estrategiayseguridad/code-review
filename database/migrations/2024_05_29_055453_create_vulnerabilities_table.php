<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vulnerabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analysis_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('file_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('method');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('lines')->nullable();
            $table->string('severity')->nullable();
            $table->string('impact')->nullable();
            $table->string('cwe')->nullable();
            $table->string('cve')->nullable();
            $table->text('solution')->nullable();
            $table->text('mitigation')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->foreign('verified_by')->references('id')->on('users')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
        });
        DB::unprepared('
            CREATE TRIGGER vulnerabilities_BEFORE_INSERT BEFORE INSERT ON vulnerabilities FOR EACH ROW
                SET NEW.id = (SELECT IFNULL(MAX(id), 0) + 1 FROM vulnerabilities);
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vulnerabilities');
    }
};
