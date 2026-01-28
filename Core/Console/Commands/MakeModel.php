<?php

class MakeModel
{
    public function handle(array $argv)
    {
        $name = $argv[2] ?? null;

        if (!$name) {
            echo "Model name required.\n";
            return;
        }

        $table = strtolower($name).'s';

        $path = __DIR__."/../../../Models/{$name}.php";

        $template = <<<PHP
<?php

require_once __DIR__.'/../Core/Model.php';

class {$name} extends Model
{
    protected static string \$table = '{$table}';
}
PHP;

        file_put_contents($path, $template);

        echo "Model created: {$name}\n";
    }
}
