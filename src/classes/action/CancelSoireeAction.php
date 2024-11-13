<?php

namespace iutnc\nrv\action;

use iutnc\nrv\auth\AuthnProvider;
use iutnc\nrv\auth\Authz;
use iutnc\nrv\repository\NRVRepository;

class CancelSoireeAction extends Action {

    public function execute(): string {
        // Récupération de l'utilisateur et vérification des droits d'admin
        $currentUser = AuthnProvider::getSignedInUser();
        $authz = new Authz($currentUser);

        if (!$authz->isAdmin()) {
            return "<p>Accès refusé : vous n'avez pas les droits nécessaires pour accéder à cette page.</p>";
        }

        // Vérification de l'ID de la soirée
        if (!isset($_GET['id_soiree'])) {
            return "<p>Erreur : ID de la soirée non fourni.</p>";
        }

        // Annulation de la soirée dans la base de données
        $repo = NRVRepository::getInstance();
        $idSoiree = (int)$_GET['id_soiree'];
        $repo->cancelSoiree($idSoiree);

        return "<p>Soirée annulée avec succès.</p>";
    }
}
