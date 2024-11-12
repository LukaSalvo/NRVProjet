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
