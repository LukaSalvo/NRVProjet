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

            $_SESSION['current_spectacle_id'] = $spectacleId;

            if (!$spectacle) {
                return "<p class='bg-red-100 text-red-700 p-4 rounded'>Spectacle non trouvé.</p>";
            }

            $output = '
            <div class="container mx-auto p-6 bg-gray-100 rounded-lg shadow-lg">
                <h2 class="text-3xl font-bold text-purple-700 mb-4">' . htmlspecialchars($spectacle['nomSpec']) . '</h2>
                <p class="text-lg"><strong>Style :</strong> ' . htmlspecialchars($spectacle['style']) . '</p>
                <p class="text-lg"><strong>Durée :</strong> ' . htmlspecialchars($spectacle['duree']) . ' minutes</p>
                <h3 class="text-2xl font-semibold text-purple-600 mt-6">Artistes</h3>
                <ul class="list-group mt-4 space-y-2">';

            foreach ($artists as $artist) {
                $output .= '<li class="bg-white p-3 rounded shadow-md">' . htmlspecialchars($artist['nom_artiste']) . '</li>';
            }
            $output .= '</ul>';

            $similarByDate = $repo->getSpectaclesByDate($spectacle['date'], $spectacleId);
            $similarByLocation = $repo->getSpectaclesByLocation($spectacle['nom_lieu'], $spectacleId);
            $similarByStyle = $repo->getSpectaclesByStyle($spectacle['style'], $spectacleId);

            $output .= $this->renderSimilarSection("Autres spectacles à la même date", $similarByDate);
            $output .= $this->renderSimilarSection("Autres spectacles au même lieu", $similarByLocation);
            $output .= $this->renderSimilarSection("Autres spectacles du même style", $similarByStyle);

            $isLiked = $this->isSpectacleLiked($spectacleId);
            $buttonText = $isLiked ? 'Retirer des favoris' : 'Ajouter aux favoris';
            $buttonClass = $isLiked ? 'bg-red-600 hover:bg-red-700' : 'bg-purple-600 hover:bg-purple-700';

            $output .= '
            <div class="mt-6">
                <a href="?action=default" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded mr-2">Retour à l\'accueil</a>
                <a href="?action=like&id_spectacle=' . $spectacleId . '" 
                   class="' . $buttonClass . ' text-white py-2 px-4 rounded">
                   ' . $buttonText . '
                </a>
            </div>';

            return $output;

        } else {
            return "<p class='bg-red-100 text-red-700 p-4 rounded'>Erreur : ID du spectacle non fourni.</p>";
        }
    }

    private function renderSimilarSection(string $title, array $spectacles): string {
        if (empty($spectacles)) return "";

        $output = '<h3 class="text-2xl font-semibold text-purple-600 mt-6">' . htmlspecialchars($title) . '</h3><ul class="list-group mt-4 space-y-2">';
        foreach ($spectacles as $spectacle) {
            $output .= '
                <li class="bg-white p-4 rounded shadow-md">
                    <a href="?action=displaySpectacleDetail&id_spectacle=' . $spectacle['id_spectacle'] . '" class="text-blue-600 hover:underline font-semibold">
                        ' . htmlspecialchars($spectacle['nomSpec']) . ' - ' . htmlspecialchars($spectacle['style']) . ' (' . htmlspecialchars($spectacle['duree']) . ' minutes)
                    </a>
                </li>';
        }
        $output .= '</ul>';

        return $output;
    }

    private function isSpectacleLiked(int $spectacleId): bool {
        if (!isset($_SESSION['user'])) return false;
        
        $repo = NRVRepository::getInstance();
        $user = unserialize($_SESSION['user']);
        return $repo->isSpectacleLiked($user->getId(), $spectacleId);
    }
}
