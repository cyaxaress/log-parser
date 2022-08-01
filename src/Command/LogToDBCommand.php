<?php

namespace App\Command;

use App\Services\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'log:todb')]
class LogToDBCommand extends Command
{
    protected static $defaultName = 'log:todb';
    protected static $defaultDescription = 'Parse log file and insert the data to a database.';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $logFile = $input->getArgument("logFile") ?? __DIR__ . "/../../logs.txt";
        if ($file = fopen($logFile, "r")) {
            while (!feof($file)) {
                $line = trim(fgets($file));
                $data["service"] = Str::before($line, " - -");
                $data["status"] = Str::lastCharacters($line, 3);
                $data["date"] = Str::between($line, "[", "]");
                $route =  Str::between($line, "\"", "\"");
                $routeParts = explode(" ", $route);
                $data["uri"] = $routeParts[1];
                $data["method"] = $routeParts[0];


            }
            fclose($file);
        }
        return Command::SUCCESS;
    }




    protected function configure()
    {
        $this->addArgument('logFile', InputArgument::OPTIONAL, 'log file required');
    }
}
