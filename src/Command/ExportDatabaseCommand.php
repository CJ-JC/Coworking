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
    // private $dbalConnection;

    // public function __construct(Connection $dbalConnection)
    // {
    //     parent::__construct();

    //     $this->dbalConnection = $dbalConnection;
    // }

    // protected function configure()
    // {
    //     $this->setName('app:export-database')
    //         ->setDescription('Export the database to a SQL file');
    // }
    // mysqldump --user=root --password=root --host=127.0.0.1 --port=8889 dbs11628883 > backup.sql

    // protected function execute(InputInterface $input, OutputInterface $output): int
    // {
    //     // On récupère l'URL de la base de données à partir des variables d'environnement
    //     $databaseUrl = $_ENV['DATABASE_URL'];
        
    //     // On divise l'URL en différentes parties (user, mot de passe, hôte, etc.) en utilisant la fonction parse_url
    //     $urlParts = parse_url($databaseUrl);
        
    //     // On extrait le nom de la base de données à partir de l'URL et on enlève éventuellement le slash initial
    //     $dbName = ltrim($urlParts['path'], '/');
        
    //     // On récupère le nom d'utilisateur et le mot de passe de la base de données depuis l'URL
    //     $dbUser = $urlParts['user'];
    //     $dbPassword = $urlParts['pass'];
        
    //     // On récupère l'hôte (adresse) de la base de données depuis l'URL
    //     $dbHost = $urlParts['host'];

    //     // On construit la commande de sauvegarde en utilisant les valeurs récupérées précédemment
    //     $command = sprintf(
    //         'mysqldump --user=%s --password=%s --host=%s %s > %s',
    //         $dbUser,
    //         $dbPassword,
    //         $dbHost,
    //         $dbName,
    //         // $backupPath
    //     );

    //     // On exécute la commande de sauvegarde en utilisant la fonction exec, qui permet d'exécuter une commande système
    //     exec($command);

    //     // On indique que l'exécution de la commande s'est bien déroulée en renvoyant une valeur indiquant le succès
    //     return Command::SUCCESS;
    // }
}