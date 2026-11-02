<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerDocumentTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('customer_document_types')) {
            Schema::create('customer_document_types', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description')->nullable();
                $table->boolean('is_required')->default(0);
                $table->boolean('is_active')->default(1);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('customer_documents')) {
            Schema::create('customer_documents', function (Blueprint $table) {
                $table->id();
                $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
                $table->unsignedBigInteger('document_type_id');
                $table->string('file_path');
                $table->string('file_name')->nullable();
                $table->timestamps();

                $table->foreign('document_type_id')->references('id')->on('customer_document_types')->onDelete('cascade');
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
        Schema::dropIfExists('customer_documents');
        Schema::dropIfExists('customer_document_types');
    }
}

