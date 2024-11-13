<?php

namespace iutnc\nrv\action;

use iutnc\nrv\repository\NRVRepository;
use iutnc\nrv\auth\AuthnProvider;

class DisplayMesFavorisAction extends Action {
    public function execute(): string {
        $authProvider = new AuthnProvider();
        $user = $authProvider->getSignedInUser();

        if (!$user) {
            return '<p class="text-red-500 text-center">Veuillez vous connecter pour voir vos spectacles favoris.</p>';
        }

        $userId = $user->getId();
        $repo = NRVRepository::getInstance();
        $favoris = $repo->getFavorisByUserId($userId);

        $result = "<div class='container mx-auto my-8'>
                    <h2 class='text-3xl font-bold text-indigo-700 mb-6'>Vos Spectacles Favoris</h2>";
        if (empty($favoris)) {
            $result .= "<p class='text-gray-600 text-center'>Vous n'avez aucun spectacle en favoris.</p>";
        } else {
            $result .= "<div class='grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6'>";
            foreach ($favoris as $spectacle) {
                $nomSpec = htmlspecialchars($spectacle['nomSpec'] ?? 'Nom inconnu');
                $style = htmlspecialchars($spectacle['nom_style'] ?? 'Non spécifié');
                $duree = htmlspecialchars($spectacle['duree'] ?? 'Non spécifiée');
                $idSpectacle = $spectacle['id_spectacle'] ?? null;

                $result .= '
                <div class="card bg-white shadow-md rounded-lg p-4 transition duration-300 hover:shadow-lg">
                    <h5 class="text-xl font-semibold text-purple-700 mb-2">' . $nomSpec . '</h5>
                    <p class="text-gray-700"><strong>Style:</strong> ' . $style . '</p>
                    <p class="text-gray-700"><strong>Durée:</strong> ' . $duree . ' min</p>';

                if ($idSpectacle) {
                    $result .= '<a href="?action=displaySpectacleDetail&id_spectacle=' . htmlspecialchars($idSpectacle) . '" 
                                class="text-indigo-600 hover:underline mt-3 inline-block">Voir plus de détails</a>';
                }
                $result .= '</div>';
            }
            $result .= "</div>";
        }

        $result .= "</div>";
        return $result;
    }
}
