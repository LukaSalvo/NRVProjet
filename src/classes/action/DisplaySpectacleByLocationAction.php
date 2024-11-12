<?php

namespace iutnc\nrv\action;

use iutnc\nrv\repository\NRVRepository;

class DisplaySpectacleByLocationAction extends Action {

    public function execute(): string {
        $repo = NRVRepository::getInstance();

        // Récupère tous les lieux distincts pour le menu déroulant
        $locations = $repo->getAllLocations();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['location'])) {
            $location = $_POST['location'];
            $spectacles = $repo->getSpectaclesByLocation($location);

            $output = "<h2>Spectacles pour le lieu : {$location}</h2><ul>";
            foreach ($spectacles as $spectacle) {
                $output .= "<li><a href='?action=displaySpectacleDetail&id_spectacle={$spectacle['id_spectacle']}'>{$spectacle['nomSpec']} - {$spectacle['style']} ({$spectacle['duree']} minutes)</a></li>";
            }
            $output .= "</ul><a href='?action=filterByLocation'>Retour à la sélection de lieu</a>";
            return $output;

        } else {
            // Génère les options du menu déroulant pour chaque lieu
            $locationOptions = '';
            foreach ($locations as $location) {
                $locationOptions .= "<option value='{$location['nom_lieu']}'>{$location['nom_lieu']}</option>";
            }

            return <<<HTML
                <form method="POST" action="?action=filterByLocation">
                    <label for="location">Sélectionner un lieu :</label>
                    <select id="location" name="location" required>
                        $locationOptions
                    </select>
                    <button type="submit">Filtrer</button>
                </form>
            HTML;
        }
    }
}
