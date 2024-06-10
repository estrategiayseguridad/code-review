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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('path');
            $table->unsignedBigInteger('extension_id')->nullable();
            $table->foreign('extension_id')->references('id')->on('extensions')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamp('analyzed_at')->nullable();
        });
        DB::unprepared('
            CREATE TRIGGER files_BEFORE_INSERT BEFORE INSERT ON files FOR EACH ROW
                SET NEW.id = (SELECT IFNULL(MAX(id), 0) + 1 FROM files);
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
