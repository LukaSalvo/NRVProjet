<?php

namespace iutnc\nrv\action;

use iutnc\nrv\repository\NRVRepository;

class DisplaySpectaclesByLocationAction extends Action {

    public function execute(): string {
        $repo = NRVRepository::getInstance();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['location'])) {
            $location = $_POST['location'];
            $spectacles = $repo->getSpectaclesByLocation($location);

            $output = "<h2>Spectacles pour le lieu : {$location}</h2><ul>";
            foreach ($spectacles as $spectacle) {
                $output .= "<li><a href='?action=displaySpectacleDetail&id_spectacle={$spectacle['id_spectacle']}'>{$spectacle['nomSpec']} - {$spectacle['style']} ({$spectacle['durée']} minutes)</a></li>";
            }
            $output .= "</ul><a href='?action=filterByLocation'>Retour à la sélection de lieu</a>";
            return $output;

        } else {
            return <<<HTML
                <form method="POST" action="?action=filterByLocation">
                    <label for="location">Sélectionner un lieu :</label>
                    <input type="text" id="location" name="location" required>
                    <button type="submit">Filtrer</button>
                </form>
            HTML;
        }
    }
}
