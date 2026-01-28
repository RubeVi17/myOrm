<?php

return new class {

    public function up(): void
    {
        Schema::create('users', function($table){
            $table->id();
            $table->string('name');
            $table->string('email', 150);
            $table->integer('age');
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::drop('users');
    }

};