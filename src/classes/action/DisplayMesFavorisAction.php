<?php
namespace iutnc\nrv\action;

use iutnc\nrv\repository\NRVRepository;
use iutnc\nrv\auth\AuthnProvider;

class DisplayMesFavorisAction extends Action {
    public function execute(): string {

        $authProvider = new AuthnProvider();
        $user = $authProvider->getSignedInUser();

        if (!$user) {
            return 'Veuillez vous connecter pour voir vos spectacles favoris.';
        }

        $userId = $user->getId();
        $repo = NRVRepository::getInstance();
        $favoris = $repo->getFavorisByUserId($userId);


        $result = "<h1>Vos Spectacles Favoris</h1>";
        if (empty($favoris)) {
            $result .= "<p>Vous n'avez aucun spectacle en favoris.</p>";
        } else {
            foreach ($favoris as $spectacle) {
                $nomSpec = htmlspecialchars($spectacle['nomSpec'] ?? 'Nom inconnu');
                $style = htmlspecialchars($spectacle['nom_style'] ?? 'Non spécifié');
                $duree = htmlspecialchars($spectacle['duree'] ?? 'Non spécifiée');
                $idSpectacle = $spectacle['id_spectacle'] ?? null;

                $result .= "<p>{$nomSpec}</p>";
                $result .= '
                    <br>
                    <strong>Nom :</strong> ' . $nomSpec . '<br>
                    <strong>Style :</strong> ' . $style . '<br>
                    <strong>Durée :</strong> ' . $duree . ' min <br>';

                if ($idSpectacle) {
                    $result .= '<a href="?action=displaySpectacleDetail&id_spectacle=' . htmlspecialchars($idSpectacle) . '">Voir plus de détails</a>';

                }
                $result .= '<br><br>';
            }
        }

        return $result;
    }
}
