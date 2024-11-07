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

            $output = "
            <h2>{$spectacle['nomSpec']}</h2>
            <p><strong>Style :</strong> {$spectacle['style']}</p>
            <p><strong>Durée :</strong> {$spectacle['durée']} minutes</p>
            <p><strong>Description :</strong> {$spectacle['description']}</p>
            <h3>Artistes</h3>
            <ul>";

            foreach ($artists as $artist) {
                $output .= "<li>{$artist['nom_artiste']}</li>";
            }

            $output .= "</ul>";

            $output .= "
            <h3>Spectacles Similaires</h3>
            <ul>
                <li><a href='?action=filterByDate&date={$spectacle['date']}'>Voir d'autres spectacles à la même date</a></li>
                <li><a href='?action=filterByLocation&location={$spectacle['nom_lieu']}'>Voir d'autres spectacles au même lieu</a></li>
                <li><a href='?action=filterByStyle&style={$spectacle['style']}'>Voir d'autres spectacles du même style</a></li>
            </ul>";

            return $output;

        } else {
            return "<p>Erreur : ID du spectacle non fourni.</p>";
        }
    }
}
