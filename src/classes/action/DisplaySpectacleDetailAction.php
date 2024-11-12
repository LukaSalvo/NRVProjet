<?php

namespace iutnc\nrv\action;

use iutnc\nrv\repository\NRVRepository;

class DisplaySpectacleDetailAction extends Action {

    public function execute(): string {
        $repo = NRVRepository::getInstance();

        if (isset($_GET['id_spectacle'])) {
            $spectacleId = (int)$_GET['id_spectacle'];
            $spectacle = $repo->getSpectacleById($spectacleId);
            $artists = $repo->getArtistsBySpectacleId($spectacleId);

            // Stocker l'ID du spectacle en session
            $_SESSION['current_spectacle_id'] = $spectacleId;

            if (!$spectacle) {
                return "<p>Spectacle non trouvé.</p>";
            }

            // Affichage des informations principales du spectacle
            $output = "
            <h2>{$spectacle['nomSpec']}</h2>
            <p><strong>Style :</strong> {$spectacle['style']}</p>
            <p><strong>Durée :</strong> {$spectacle['duree']} minutes</p>
            <h3>Artistes</h3>
            <ul>";

            foreach ($artists as $artist) {
                $output .= "<li>{$artist['nom_artiste']}</li>";
            }
            $output .= "</ul>";

            // Suggestions de spectacles similaires
            $similarByDate = $repo->getSpectaclesByDate($spectacle['date'], $spectacleId);
            $similarByLocation = $repo->getSpectaclesByLocation($spectacle['nom_lieu'], $spectacleId);
            $similarByStyle = $repo->getSpectaclesByStyle($spectacle['style'], $spectacleId);

            $output .= $this->renderSimilarSection("Autres spectacles à la même date", $similarByDate);
            $output .= $this->renderSimilarSection("Autres spectacles au même lieu", $similarByLocation);
            $output .= $this->renderSimilarSection("Autres spectacles du même style", $similarByStyle);

            // Ajout du bouton de retour à l'accueil
            $output .= '
            <form action="index.php" method="get">
                <button type="submit" name="action" value="default">Retour à l\'accueil</button>
            </form>';

            //Ajout du bouton pour like un spectacle
            $output .= '
            <form action="index.php" method="get">
                <input type="hidden" name="id_spectacle" value="' . $spectacleId . '">
                <button type="submit" name="action" value="like">Ajouter aux favoris</button>
            </form>';
            return $output;

        } else {
            return "<p>Erreur : ID du spectacle non fourni.</p>";
        }
    }

    private function renderSimilarSection(string $title, array $spectacles): string {
        if (empty($spectacles)) {
            return "";
        }

        $output = "<h3>$title</h3><ul>";
        foreach ($spectacles as $spectacle) {
            $output .= "<li><a href='?action=displaySpectacleDetail&id_spectacle={$spectacle['id_spectacle']}'>{$spectacle['nomSpec']} - {$spectacle['style']} ({$spectacle['duree']} minutes)</a></li>";
        }
        $output .= "</ul>";

        return $output;
    }
}
