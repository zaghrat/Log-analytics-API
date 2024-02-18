<?php

namespace App\Command;

use App\Entity\Log;
use App\Service\LogParser;
use App\Service\LogSaver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Stopwatch\Stopwatch;

#[AsCommand(
    name: 'app:import-logs',
    description: 'Import logs from a file into the database',
)]
class ImportLogsCommand extends Command
{
    private LogParser $logParser;
    private LogSaver $logSaver;
    private EntityManagerInterface $entityManager;

    private const int LINES_TO_READ = 10000;

    public function __construct(LogParser $logParser, LogSaver $logSaver, EntityManagerInterface $entityManager)
    {
        $this->logParser = $logParser;
        $this->logSaver = $logSaver;
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // check how many lines already imported
        $countOfSavedLines = $this->entityManager->getRepository(Log::class)->count();
        $importedLinesCount = 0;
         // read self::LINES_TO_READ lines from the file starting from the line $countOfSavedLines
        foreach ($this->logParser->parse($countOfSavedLines, self::LINES_TO_READ) as $data) {
            $this->logSaver->save($data);
            $importedLinesCount++;

            // insert query every 1000 line
            if ($importedLinesCount % 1000 === 0) {
                $this->entityManager->flush();
            }
        }

        $this->entityManager->flush();

        $io->success("{$importedLinesCount} Log lines have been imported successfully!");

        return Command::SUCCESS;
    }
}
