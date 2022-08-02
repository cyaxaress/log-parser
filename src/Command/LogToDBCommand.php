<?php

namespace App\Command;

use App\Entity\LogFile;
use App\Entity\LogRecord;
use App\Repository\LogFileRepository;
use App\Repository\LogRecordRepository;
use App\Services\Str;
use Doctrine\Persistence\ManagerRegistry;
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

    /** @required */
    public LogRecordRepository $logRecordRepository;

    /** @required */
    public LogFileRepository $logFileRepository;

    /** @required */
    public ManagerRegistry $doctrine;


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $logFilePath = $input->getArgument("logFile") ?? __DIR__ . "/../../log.txt";
        if (!file_exists($logFilePath)){
            throw new \Exception("File does not exits!");
        }

        // get log file record from the database or create new record,
        // in this record we keep last checked timestamp and line number
        // we can use this information to start from where it left off last time.

        $logFile = $this->logFileRepository->findOneBy(['path' => $logFilePath]);
        if (!$logFile) {
            $logFile = new LogFile();
            $logFile->setCursorLine(0)->setPath($logFilePath);
            $this->logFileRepository->add($logFile, true);
        }

        if ($file = fopen($logFile->getPath(), "r")) {

            // start read the log file line by line and save it to database.
            $currentLine = 1;
            while (!feof($file)) {
                $line = trim(fgets($file));

                // here we check if this line is already saved to database or not.
                // if it's already saved we just continue to next line otherwise we save a new log record
                if ($currentLine <= $logFile->getCursorLine()) {
                    $currentLine++;
                    continue;
                }

                // the Str class developed by ourselves has some method to help us
                // about parsing log file. here we get parts of data that we need from log line
                // and keep them in an array.
                $data["service"] = Str::before($line, " - -");
                $data["status"] = Str::lastCharacters($line, 3);
                $data["date"] = Str::before(Str::between($line, "[", "]"), " +");
                $route = Str::between($line, "\"", "\"");
                $routeParts = explode(" ", $route);
                $data["uri"] = $routeParts[1];
                $data["method"] = $routeParts[0];

                // after getting all parts of log file, we save it into the database
                $logRecord = new LogRecord();
                $logRecord->setDate(\DateTime::createFromFormat("d/M/Y:H:i:s", $data["date"]))
                    ->setMethod($data["method"])
                    ->setService($data["service"])
                    ->setStatus($data["status"])
                    ->setUri($data["uri"]);
                $this->logRecordRepository->add($logRecord, true);

                // after saving each log record, here we update log file record,
                // this action helps us to continue from where we left off if any interruptions happens
                $logFile->setCursorLine($currentLine);
                $logFile->setCheckedAt(new \DateTime());
                $this->doctrine->getManager()->flush();
                $currentLine++;
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
