<?php

namespace iutnc\nrv\action;

use iutnc\nrv\repository\NRVRepository;

class DisplaySpectacleByDateAction extends Action {

    public function execute(): string {
        $repo = NRVRepository::getInstance();

        // Récupère toutes les dates distinctes pour le menu déroulant
        $dates = $repo->getAllDates();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['date'])) {
            $date = $_POST['date'];
            $spectacles = $repo->getSpectaclesByDate($date);

            $output = "<h2>Spectacles pour la date : {$date}</h2><ul>";
            foreach ($spectacles as $spectacle) {
                $output .= "<li><a href='?action=displaySpectacleDetail&id_spectacle={$spectacle['id_spectacle']}'>{$spectacle['nomSpec']} - {$spectacle['style']} ({$spectacle['duree']} minutes)</a></li>";
            }
            $output .= "</ul><a href='?action=filterByDate'>Retour à la sélection de date</a>";
            return $output;

        } else {
            // Génère les options du menu déroulant pour chaque date
            $dateOptions = '';
            foreach ($dates as $date) {
                $dateOptions .= "<option value='{$date['date']}'>{$date['date']}</option>";
            }

            return <<<HTML
                <form method="POST" action="?action=filterByDate">
                    <label for="date">Sélectionner une date :</label>
                    <select id="date" name="date" required>
                        $dateOptions
                    </select>
                    <button type="submit">Filtrer</button>
                </form>
            HTML;
        }
    }
}
