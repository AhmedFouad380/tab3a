<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('static_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // about_us, terms_and_conditions, privacy_policy
            $table->json('title'); // {"ar": "من نحن", "en": "About Us"}
            $table->json('content'); // HTML / Markdown formatted content in AR & EN
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('category')->default('general'); // general, self_print, pre_order, payment
            $table->json('question'); // {"ar": "كيف تعمل الطباعة الذاتية؟", "en": "How does self-printing work?"}
            $table->json('answer'); // {"ar": "...", "en": "..."}
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('static_pages');
    }
};
