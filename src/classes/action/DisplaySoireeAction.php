<?php

namespace iutnc\nrv\action;

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

            // Indiquer si la soirée est annulée
            $annulationMessage = $soiree['annuler'] == 0 ? "<p><strong>Cette soirée est annulée.</strong></p>" : "";

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
                $annulationMessage
                <h2>Liste des Spectacles</h2>
                <ul>";

            foreach ($spectacles as $spectacle) {
                $output .= "<li><strong>{$spectacle['nomSpec']}</strong> - {$spectacle['style']} ({$spectacle['duree']} minutes)</li>";
            }

            $output .= "</ul>";

            // Affichage du lien d'ajout de spectacle pour les administrateurs
            if (isset($_SESSION['user']) && (new Authz(unserialize($_SESSION['user'])))->isAdmin()) {
                $output .= "<a href='?action=addSpectacle&soiree_id={$idSoiree}' class='btn-primary'>Ajouter un spectacle</a>";
            }

            $output .= "<a href='?action=displaySoiree'>Retour à la liste des soirées</a>
            </body>
            </html>";

            return $output;

        } else {
            $soirees = $repo->getAllSoirees();
            $html = "<h2>Liste des Soirées</h2><ul>";

            // Marquer chaque soirée annulée dans la liste
            foreach ($soirees as $soiree) {
                $status = $soiree['annuler'] == 0 ? " (Annulée)" : "";
                $html .= "<li><a href='?action=displaySoiree&id_soiree={$soiree['id_soiree']}'>{$soiree['nom_lieu']} - {$soiree['date']}</a>{$status}</li>";
            }

            $html .= "</ul>";
            return $html;
        }
    }
}
