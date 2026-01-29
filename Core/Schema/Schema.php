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

    public static function table($table, callable $callback)
    {
        $blueprint = new Blueprint($table, true);
        $callback($blueprint);

        $sqls = $blueprint->toSql();
        foreach((array) $sqls as $sql) {
            Database::connection()->exec($sql);
        }
    }
    

}