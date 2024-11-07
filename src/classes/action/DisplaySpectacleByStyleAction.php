<?php

namespace iutnc\nrv\action;

use iutnc\nrv\repository\NRVRepository;

class DisplaySpectaclesByStyleAction extends Action {

    public function execute(): string {
        $repo = NRVRepository::getInstance();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['style'])) {
            $style = $_POST['style'];
            $spectacles = $repo->getSpectaclesByStyle($style);

            $output = "<h2>Spectacles pour le style : {$style}</h2><ul>";
            foreach ($spectacles as $spectacle) {
                $output .= "<li><a href='?action=displaySpectacleDetail&id_spectacle={$spectacle['id_spectacle']}'>{$spectacle['nomSpec']} - {$spectacle['style']} ({$spectacle['durée']} minutes)</a></li>";
            }
            $output .= "</ul><a href='?action=filterByStyle'>Retour à la sélection de style</a>";
            return $output;

        } else {
            return <<<HTML
                <form method="POST" action="?action=filterByStyle">
                    <label for="style">Sélectionner un style :</label>
                    <input type="text" id="style" name="style" required>
                    <button type="submit">Filtrer</button>
                </form>
            HTML;
        }
    }
}
