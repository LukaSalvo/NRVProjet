<?php

namespace iutnc\nrv\action;

use iutnc\nrv\repository\NRVRepository;
use iutnc\nrv\auth\AuthnProvider;
use iutnc\nrv\auth\Authz;

class EditSoireeListAction extends Action {

    public function execute(): string {
        // Vérifie que l'utilisateur est connecté et est administrateur
        try {
            $currentUser = AuthnProvider::getSignedInUser();
            $authz = new Authz($currentUser);

            if (!$authz->isAdmin()) {
                return "<p>Accès refusé : vous n'avez pas les droits nécessaires pour accéder à cette page.</p>";
            }
        } catch (\Exception $e) {
            return "<p>Erreur : " . $e->getMessage() . "</p>";
        }

        // Récupération des soirées
        $repo = NRVRepository::getInstance();
        $soirees = $repo->getAllSoirees();

        // Affichage de la liste des soirées avec options de modification et annulation
        $output = "<h2>Liste des soirées</h2><ul>";
        foreach ($soirees as $soiree) {
            $output .= "<li>
                <strong>{$soiree['nom_soiree']}</strong> - {$soiree['date']}
                <a href='?action=editSoiree&id_soiree={$soiree['id_soiree']}' class='btn-edit'>Modifier</a> |
                <a href='?action=cancelSoiree&id_soiree={$soiree['id_soiree']}' class='btn-cancel'>Annuler</a>
            </li>";
        }
        $output .= "</ul>";
        
        return $output;
    }
}
