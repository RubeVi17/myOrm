<?php

class MakeMigration
{
    public function handle(array $argv)
    {
        $name = $argv[2] ?? null;

        if (!$name) {
            echo "Migration name required.\n";
            return;
        }

        $timestamp = date('Y_m_d_His');
        $filename = "{$timestamp}_{$name}.php";
        $path = __DIR__."/../../../Migrations/{$filename}";

        $template = <<<PHP
<?php

return new class {
    public function up()
    {
        //
    }

    public function down()
    {
        //
    }
};
PHP;

        file_put_contents($path, $template);

        echo "Migration created: {$filename}\n";
    }
}
