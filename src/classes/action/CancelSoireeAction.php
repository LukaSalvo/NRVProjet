<?php

namespace iutnc\nrv\action;

use iutnc\nrv\auth\Authz;
use iutnc\nrv\auth\AuthnProvider;
use iutnc\nrv\repository\NRVRepository;

class CancelSoireeAction extends Action {

    public function execute(): string {
        $currentUser = AuthnProvider::getSignedInUser();
        $authz = new Authz($currentUser);

        if (!$authz->isAdmin()) {
            return "<p>Accès refusé : vous n'avez pas les droits nécessaires pour accéder à cette page.</p>";
        }

        $repo = NRVRepository::getInstance();
        $soireeId = (int)$_GET['id_soiree'];
        
        $repo->cancelSoiree($soireeId);
        return "<p>La soirée a été annulée avec succès.</p>";
    }
}
