<?php

namespace iutnc\nrv\action;

use iutnc\nrv\auth\Authz;
use iutnc\nrv\repository\NRVRepository;

class DisplaySoireeAction extends Action {

    public function execute(): string {
        $repo = NRVRepository::getInstance();
        $soirees = $repo->getAllSoirees();

        $output = "<h2>Liste des Soirées</h2><ul>";

        foreach ($soirees as $soiree) {
            $status = $soiree['annuler'] == 0 ? "<strong>(Annulée)</strong>" : "";
            $output .= "<li>
                <h3>Soirée: " . htmlspecialchars($soiree['nom_soiree']) . " $status</h3>
                <p><strong>Lieu:</strong> " . htmlspecialchars($soiree['nom_lieu']) . "</p>
                <p><strong>Date:</strong> " . htmlspecialchars($soiree['date']) . "</p>";

            // Si l'utilisateur est administrateur et la soirée est annulée, afficher le bouton "Retirer l'annulation"
            if ($soiree['annuler'] == 0 && isset($_SESSION['user']) && (new Authz(unserialize($_SESSION['user'])))->isAdmin()) {
                $output .= "<p><a href='?action=uncancelSoiree&id_soiree={$soiree['id_soiree']}' class='btn-primary'>Retirer l'annulation</a></p>";
            }

            $output .= "</li>";
        }
        $output .= "</ul>";

        return $output;
    }
}
