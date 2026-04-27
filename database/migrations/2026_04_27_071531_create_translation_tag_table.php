<?php 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tag_translation', function (Blueprint $table) {
            $table->id();

            $table->foreignId('translation_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('tag_id')
                ->constrained()
                ->cascadeOnDelete();

            // Prevent duplicate relations
            $table->unique(['translation_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translation_tag');
    }
};