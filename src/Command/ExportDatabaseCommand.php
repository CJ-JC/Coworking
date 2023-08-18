<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ExportDatabaseCommand extends Command
{
    private $dbalConnection;

    public function __construct(Connection $dbalConnection)
    {
        parent::__construct();

        $this->dbalConnection = $dbalConnection;
    }

    protected function configure()
    {
        $this->setName('app:export-database')
            ->setDescription('Export the database to a SQL file');
    }
    // mysqldump --user=root --password=root --host=127.0.0.1 --port=8889 dbs11628883 > backup.sql

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $databaseUrl = $_ENV['DATABASE_URL'];
        $urlParts = parse_url($databaseUrl);

        $dbName = ltrim($urlParts['path'], '/');
        $dbUser = $urlParts['user'];
        $dbPassword = $urlParts['pass'];
        $dbHost = $urlParts['host'];

        // $backupPath = __DIR__ . '/../../backup.sql';
        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s %s > %s',
            $dbUser,
            $dbPassword,
            $dbHost,
            $dbName,
            // $backupPath
        );

        exec($command);

        return Command::SUCCESS;
    }
}