<?php

return new class {
    public function up()
    {
        Schema::create('topics', function ($table) {
            $table->id();
            $table->string('title')->unique();
            $table->string('slug')->unique();
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('topics');
    }
};