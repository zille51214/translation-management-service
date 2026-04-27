<?php 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->string('locale');
            $table->text('value');
            $table->timestamps();

            // Indexes for performance
            $table->index(['key']);
            $table->index(['locale']);
            $table->unique(['key', 'locale']); // prevent duplicates
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};