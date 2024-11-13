<?php

namespace iutnc\nrv\action;

use iutnc\nrv\auth\Authz;
use iutnc\nrv\repository\NRVRepository;

class UncancelSoireeAction extends Action {

    public function execute(): string {
        try {
            $currentUser = unserialize($_SESSION['user']);
            $authz = new Authz($currentUser);

            if (!$authz->isAdmin()) {
                return "<p>Accès refusé : vous n'avez pas les droits nécessaires pour accéder à cette page.</p>";
            }

            // Vérifie si l'ID de la soirée est fourni
            if (!isset($_GET['id_soiree'])) {
                return "<p>Erreur : ID de la soirée non fourni.</p>";
            }

            $repo = NRVRepository::getInstance();
            $soireeId = (int)$_GET['id_soiree'];
            
            // Met à jour le statut de la soirée pour retirer l'annulation
            $repo->updateSoireeAnnulation($soireeId, 1);
            
            return "<p>Annulation de la soirée retirée avec succès.</p><a href='?action=displaySoiree'>Retour à la liste des soirées</a>";

        } catch (\Exception $e) {
            return "<p>Erreur : " . $e->getMessage() . "</p>";
        }
    }
}
