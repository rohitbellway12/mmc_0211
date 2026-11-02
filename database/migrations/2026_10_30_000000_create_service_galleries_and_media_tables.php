<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceGalleriesAndMediaTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('service_galleries')) {
            Schema::create('service_galleries', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('service_id')->constrained('services')->cascadeOnDelete();
                $table->string('title');
                $table->text('description')->nullable();
                $table->integer('priority')->default(1);
                $table->boolean('is_active')->default(1);
                $table->timestamps();

                $table->index(['service_id', 'priority', 'is_active']);
            });
        }

        if (!Schema::hasTable('service_gallery_media')) {
            Schema::create('service_gallery_media', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('gallery_id')->constrained('service_galleries')->cascadeOnDelete();
                $table->string('media_type')->default('image'); // 'image' or 'video'
                $table->string('media_file')->nullable();
                $table->text('video_url')->nullable();
                $table->string('thumbnail')->nullable();
                $table->integer('priority')->default(1);
                $table->boolean('is_active')->default(1);
                $table->timestamps();

                $table->index(['gallery_id', 'media_type', 'priority']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_gallery_media');
        Schema::dropIfExists('service_galleries');
    }
}

