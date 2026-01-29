<?php

return new class {
    public function up()
    {
        Schema::table('topics', function (Blueprint $table) {
            $table->dateTime('date')->nullable()->after('description');
        });
    }

    public function down()
    {
        Schema::table('topics', function (Blueprint $table) {
            $table->dropColumn('date');
        });
    }
};