<?php

return new class {
    public function up()
    {
        Schema::create('categories', function($table){
            $table->id();
            $table->string('name', 100);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('categories');
    }
};