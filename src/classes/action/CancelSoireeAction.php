<?php

namespace iutnc\nrv\action;

use iutnc\nrv\auth\AuthnProvider;
use iutnc\nrv\auth\Authz;
use iutnc\nrv\repository\NRVRepository;

class CancelSoireeAction extends Action {

    public function execute(): string {
        // Récupère l'utilisateur connecté
        try {
            $currentUser = AuthnProvider::getSignedInUser();
            $authz = new Authz($currentUser);

            // Vérifie si l'utilisateur est un administrateur
            if (!$authz->isAdmin()) {
                return "<p>Accès refusé : vous n'avez pas les droits nécessaires pour accéder à cette page.</p>";
            }

        } catch (\Exception $e) {
            return "<p>Erreur : " . $e->getMessage() . "</p>";
        }

        // Vérifie si l'ID de la soirée est fourni
        if (!isset($_GET['id_soiree'])) {
            return "<p>Erreur : ID de la soirée non fourni.</p>";
        }

        $repo = NRVRepository::getInstance();
        $soireeId = (int)$_GET['id_soiree'];

        // Annule la soirée en mettant à jour son statut dans la base de données
        $repo->cancelSoiree($soireeId);
        return "<p>La soirée a été annulée avec succès.</p>";
    }
}
