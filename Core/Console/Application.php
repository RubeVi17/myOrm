<?php

require_once __DIR__.'/Commands/MakeMigration.php';
require_once __DIR__.'/Commands/MakeModel.php';

class Application
{
    protected array $commands = [];

    public function __construct()
    {
        $this->commands = [
            'make:migration' => MakeMigration::class,
            'make:model'     => MakeModel::class,
        ];
    }

    public function run(array $argv)
    {
        $command = $argv[1] ?? null;

        if (!$command || !isset($this->commands[$command])) {
            echo "Command not found.\n";
            return;
        }

        $handler = new $this->commands[$command];
        $handler->handle($argv);
    }
}
