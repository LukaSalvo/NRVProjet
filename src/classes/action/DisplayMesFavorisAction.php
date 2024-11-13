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
                $result .= "<p>{$spectacle['nomSpec']}</p>";
            }
        }

        return $result;
    }
}
