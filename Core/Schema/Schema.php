<?php

class Schema
{

    public static function create($table, callable $callback)
    {
        $blueprint = new Blueprint($table);
        $callback($blueprint);

        $sql = $blueprint->toSql();
        Database::connection()->exec($sql);
    }


    public static function drop($table)
    {
        Database::connection()->exec("DROP TABLE IF EXISTS {$table}");
    }
    

}