<?php

return new class {
    public function up()
    {
        Schema::create('posts', function ($table) {
            $table->id();
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users');

            $table->string('title');
            $table->string('body', 500);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('posts');
    }
};
