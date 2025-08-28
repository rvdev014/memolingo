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
        // words (id, language_code='en', lemma, base_form)
        Schema::create('memolingo_words', function (Blueprint $table) {
            $table->id();
            $table->string('language_code', 5)->default('en');
            $table->string('lemma', 100)->index();
            $table->string('base_form', 100)->index();
            $table->timestamps();
            
            $table->unique(['language_code', 'lemma']);
        });

        // user_known_lexicon (id, user_id, word_id, added_at)
        Schema::create('user_known_lexicon', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('word_id')->constrained('memolingo_words')->onDelete('cascade');
            $table->timestamp('added_at')->useCurrent();
            $table->timestamps();
            
            $table->unique(['user_id', 'word_id']);
        });

        // user_dictionary (id, user_id, word_id, progress_percent, ease, interval_days, last_review_at, due_at, status)
        Schema::create('user_dictionary', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('word_id')->constrained('memolingo_words')->onDelete('cascade');
            $table->integer('progress_percent')->default(0);
            $table->decimal('ease', 3, 2)->default(2.50);
            $table->integer('interval_days')->default(1);
            $table->timestamp('last_review_at')->nullable();
            $table->timestamp('due_at')->useCurrent();
            $table->enum('status', ['active', 'mastered'])->default('active');
            $table->timestamps();
            
            $table->unique(['user_id', 'word_id']);
            $table->index(['due_at', 'status']);
        });

        // ingested_texts (id, user_id, text_excerpt, full_text, keep_full, created_at)
        Schema::create('ingested_texts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('text_excerpt');
            $table->longText('full_text')->nullable();
            $table->boolean('keep_full')->default(false);
            $table->timestamps();
        });

        // text_tokens (id, ingested_text_id, token_raw, word_id, is_unknown)
        Schema::create('text_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingested_text_id')->constrained('ingested_texts')->onDelete('cascade');
            $table->string('token_raw', 100);
            $table->foreignId('word_id')->nullable()->constrained('memolingo_words')->onDelete('set null');
            $table->boolean('is_unknown')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('text_tokens');
        Schema::dropIfExists('ingested_texts');
        Schema::dropIfExists('user_dictionary');
        Schema::dropIfExists('user_known_lexicon');
        Schema::dropIfExists('memolingo_words');
    }
};
