<?php

return new class {
    public function up()
    {
        Schema::create('topics_comments', function ($table) {
            $table->id();
            $table->foreignId('topic_id')->constrained('topics');
            $table->foreignId('user_id')->constrained('users');
            $table->boolean('pinned')->default(false);
            $table->text('comment');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('topics_comments');
    }
};