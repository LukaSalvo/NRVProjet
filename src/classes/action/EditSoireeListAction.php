<?php

namespace iutnc\nrv\action;

use iutnc\nrv\repository\NRVRepository;
use iutnc\nrv\auth\AuthnProvider;
use iutnc\nrv\auth\Authz;

class EditSoireesListAction extends Action {

    public function execute(): string {
        $currentUser = AuthnProvider::getSignedInUser();
        $authz = new Authz($currentUser);

        if (!$authz->isAdmin()) {
            return "<p>Accès refusé : vous n'avez pas les droits nécessaires pour accéder à cette page.</p>";
        }

        $repo = NRVRepository::getInstance();
        $soirees = $repo->getAllSoirees();

        $output = "<h2>Liste des soirées</h2><ul>";
        foreach ($soirees as $soiree) {
            $output .= "<li>{$soiree['nom_soiree']} - {$soiree['date']}
                        <a href='?action=editSoiree&id_soiree={$soiree['id_soiree']}'>Modifier</a> |
                        <a href='?action=cancelSoiree&id_soiree={$soiree['id_soiree']}'>Annuler</a>
                        </li>";
        }
        $output .= "</ul>";
        
        return $output;
    }
}
