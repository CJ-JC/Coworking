<?php

// src/Controller/BackupController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BackupController extends AbstractController
{
    #[Route("/download-backup", name:"backup_download")]
    public function downloadBackup(): Response
    {
        // Obtenir la date actuelle au format Y-m-d_H-i-s
        $currentDate = date('d-m-Y_H-i');

        // Nom du fichier de sauvegarde avec la date
        $backupFileName = 'backup_' . $currentDate . '.sql';

        // Commande mysqldump
        // $command = 'mysqldump --user=dbu5671816 --password=qafbih-gacmaw-Gikxy1 --host=db5013906270.hosting-data.io --port=3306 dbs11628883 > '. $backupFileName;
        $command = 'mysqldump --user=dbu5671816 --password=qafbih-gacmaw-Gikxy1 --host=db5013906270.hosting-data.io --port=3306 dbs11628883 > '. $backupFileName;

        // Exécution de la commande
        exec($command);

        // Charger le contenu du fichier de sauvegarde
        $backupContent = file_get_contents($backupFileName);

        // Répondre avec le contenu de la sauvegarde en tant que téléchargement
        $response = new Response($backupContent);
        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', 'attachment; filename="'. $backupFileName . '"');

        // Supprimer le fichier de sauvegarde local
        unlink($backupFileName);

        return $response;
    }
}
