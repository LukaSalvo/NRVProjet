<?php

namespace iutnc\nrv\action;

use iutnc\nrv\repository\NRVRepository;

class DisplaySpectacleByDateAction extends Action {

    public function execute(): string {
        $repo = NRVRepository::getInstance();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['date'])) {
            $date = $_POST['date'];
            $spectacles = $repo->getSpectaclesByDate($date);

            $output = "<h2>Spectacles pour la date : {$date}</h2><ul>";
            foreach ($spectacles as $spectacle) {
                $output .= "<li><a href='?action=displaySpectacleDetail&id_spectacle={$spectacle['id_spectacle']}'>{$spectacle['nomSpec']} - {$spectacle['style']} ({$spectacle['durée']} minutes)</a></li>";
            }
            $output .= "</ul><a href='?action=filterByDate'>Retour à la sélection de date</a>";
            return $output;

        } else {
            return <<<HTML
                <form method="POST" action="?action=filterByDate">
                    <label for="date">Sélectionner une date :</label>
                    <input type="date" id="date" name="date" required>
                    <button type="submit">Filtrer</button>
                </form>
            HTML;
        }
    }
}
