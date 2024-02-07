<?php declare(strict_types=1);

namespace GeorgRinger\NewsSync\Command;

use GeorgRinger\NewsSync\Service\PreparationService;
use GeorgRinger\NewsSync\Service\SyncService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class SyncCommand extends Command
{
    protected function configure(): void
    {
        $this->setDescription('Sync news');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $prefix = 'sync_';
        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());

        $status = true;

        $preperationService = GeneralUtility::makeInstance(PreparationService::class, $prefix);
        $response = $preperationService->run($status);
        foreach ($response as $line) {
            $io->text($line);
        }

        $io->title('Starting import....');
        if ($status) {
            $syncService = GeneralUtility::makeInstance(SyncService::class, $prefix);
            echo "STARTING";
            try {
                $syncService->run();
            }catch(\Exception $e) {
                echo $e->getTraceAsString();
            }
            echo "DONE";
            return Command::SUCCESS;
        } else {
            $io->error('Cant be started, see above,');
            return Command::FAILURE;
        }
    }


}
