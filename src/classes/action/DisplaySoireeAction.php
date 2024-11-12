<?php

namespace iutnc\nrv\action;

use iutnc\nrv\auth\Authz;
use iutnc\nrv\repository\NRVRepository;

class DisplaySoireeAction extends Action {

    public function execute(): string {
        $repo = NRVRepository::getInstance();

        if (isset($_GET['id_soiree'])) {
            $idSoiree = (int)$_GET['id_soiree'];
            $soiree = $repo->getSoireeById($idSoiree);
            $spectacles = $repo->findSpectaclesBySoireeId($idSoiree);

            if (!$soiree) {
                return "<p>Soirée non trouvée.</p>";
            }

            // Vérifie si la soirée est annulée
            $statusMessage = $soiree['status'] == 0 ? "<p style='color: red; font-weight: bold;'>Cette soirée est annulée</p>" : "";

            $output = "
            <!DOCTYPE html>
            <html lang='fr'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Soirée: {$soiree['nom_lieu']} - {$soiree['date']}</title>
            </head>
            <body>
                <h1>Soirée à {$soiree['nom_lieu']} le {$soiree['date']}</h1>
                $statusMessage
                <h2>Liste des Spectacles</h2>
                <ul>";

            foreach ($spectacles as $spectacle) {
                $output .= "<li><strong>{$spectacle['nomSpec']}</strong> - {$spectacle['style']} ({$spectacle['duree']} minutes)</li>";
            }

            $output .= "</ul>";

            if (isset($_SESSION['user']) && (new Authz(unserialize($_SESSION['user'])))->isAdmin()) {
                $output .= "
                <a href='?action=addSpectacle&soiree_id={$idSoiree}' class='btn-primary'>Ajouter un spectacle</a>";
            }

            $output .= "
                <a href='?action=displaySoiree'>Retour à la liste des soirées</a>
            </body>
            </html>";

            return $output;

        } else {
            $soirees = $repo->getAllSoirees();
            $html = "<h2>Liste des Soirées</h2><ul>";

            foreach ($soirees as $soiree) {
                // Ajoute "Annulée" à côté du nom de la soirée si elle est annulée
                $annuleeText = $soiree['status'] == 0 ? "<span style='color: red;'> (Annulée)</span>" : "";
                $html .= "<li><a href='?action=displaySoiree&id_soiree={$soiree['id_soiree']}'>{$soiree['nom_lieu']} - {$soiree['date']}</a>$annuleeText</li>";
            }
            $html .= "</ul>"
                . "<div><button class='btt' onclick='window.location.href=\"?action=filterByDate\"'>Filtrer les soirées par date</button></div>
                <br>
                <button class='btt' onclick='window.location=\"?action=filterByLocation\"'>Filtrer les soirées par lieu</button></div>
                <br>
                <button class='btt' onclick='window.location=\"?action=filterByStyle\"'>Filtrer les soirées par style</button></div>
                <br>";
            return $html;
        }
    }
}
