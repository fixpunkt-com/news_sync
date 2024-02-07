<?php declare(strict_types=1);

namespace GeorgRinger\NewsSync\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class SyncImagesCommand extends Command
{
    protected function configure(): void
    {
        $this->setDescription('Sync news images');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        var_dump("HI");

        $rows = $this->getAllRows();
        $progressBar = new ProgressBar($output, count($rows));

        $progressBar->start();

        foreach ($rows as $row) {
            var_dump($row);
            $this->syncSingleFile($row);

            $progressBar->advance();
        }

        $progressBar->finish();
        $output->write('');

        return Command::SUCCESS;
    }

    protected function syncSingleFile(array $sysFileRow)
    {
        // the storage option could be used to identify the full path which isnt always in fileadmin/.
        $filePath = 'fileadmin' . $sysFileRow['identifier'];
        $localFilePath = Environment::getPublicPath() . '/' . $filePath;

        // Lets set this in the ext_conf_template.txt
        $domain = 'https://bwinf.de/';

        $remoteFilePath = $domain . $filePath;
        if (is_file($localFilePath)) {
            return;
        }

        // create dir first
        $fileInfo = pathinfo($localFilePath);
        if (!is_dir($fileInfo['dirname'])) {
            GeneralUtility::mkdir_deep($fileInfo['dirname']);
        }

        // fetch & write file
        $fileContent = GeneralUtility::getUrl($remoteFilePath);
        if ($fileContent) {
            GeneralUtility::writeFile($localFilePath, $fileContent, true);
        }
    }

    protected function getAllRows()
    {
        $qb = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_file');
        $qb->getRestrictions()->removeAll();

        $rows = $qb->select('*')
            ->from('sync_sys_file') // we list all files from old db to download them
            ->executeQuery()
            ->fetchAllAssociative();

        return $rows;
    }


}
