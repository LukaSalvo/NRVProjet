<?php

namespace iutnc\nrv\action;

use iutnc\nrv\repository\NRVRepository;

class DisplaySpectacleByStyleAction extends Action {

    public function execute(): string {
        $repo = NRVRepository::getInstance();

        // Récupère tous les styles distincts pour le menu déroulant
        $styles = $repo->getAllStyles();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['style'])) {
            $style = $_POST['style'];
            $spectacles = $repo->getSpectaclesByStyle($style);

            $output = "<h2>Spectacles pour le style : {$style}</h2><ul>";
            foreach ($spectacles as $spectacle) {
                $output .= "<li><a href='?action=displaySpectacleDetail&id_spectacle={$spectacle['id_spectacle']}'>{$spectacle['nomSpec']} - {$spectacle['style']} ({$spectacle['duree']} minutes)</a></li>";
            }
            $output .= "</ul><a href='?action=filterByStyle'>Retour à la sélection de style</a>";
            return $output;

        } else {
            // Génère les options du menu déroulant pour chaque style
            $styleOptions = '';
            foreach ($styles as $style) {
                $styleOptions .= "<option value='{$style['nom_style']}'>{$style['nom_style']}</option>";
            }

            return <<<HTML
                <form method="POST" action="?action=filterByStyle">
                    <label for="style">Sélectionner un style :</label>
                    <select id="style" name="style" required>
                        $styleOptions
                    </select>
                    <button type="submit">Filtrer</button>
                </form>
            HTML;
        }
    }
}
