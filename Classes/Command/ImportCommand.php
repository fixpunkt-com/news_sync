<?php declare(strict_types=1);

namespace GeorgRinger\NewsSync\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ImportCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setHelp('hey there.')
            ->addArgument(
                'database',
                InputArgument::REQUIRED,
                'database sql file'
            )
            ->setDescription('import news db');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $process = new Process(['node', './vendor/fixpunkt/news-sync/Resources/Private/node-convert-db/index.js']);
        $process->setEnv(["database=dbcopy.sql"]); // input file
        $process->setEnv(["output=sqlfile"]); // output file
        // $process->setWorkingDirectory(getcwd() . "/vendor/fixpunkt/news-sync/Resources/Private/node-convert-db/");
        return $process->run();
        /*$sql = file_get_contents($input->getArgument("database"));

        GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('sys_category')->executeStatement($sql);*/

        // return Command::SUCCESS;
    }


}
