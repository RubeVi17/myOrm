<?php

return new class {
    public function up()
    {
        Schema::table('topics', function (Blueprint $table) {
            $table->foreignId('categorie_id')->nullable()->constrained('categories')->after('user_id');
        });
    }

    public function down()
    {
        Schema::table('topics', function (Blueprint $table) {
            $table->dropColumn('categorie_id');
        });
    }
};