<?php

namespace iutnc\nrv\action;

use iutnc\nrv\repository\NRVRepository;
use iutnc\nrv\auth\AuthnProvider;
use iutnc\nrv\auth\Authz;

class EditSoireeListAction extends Action {

    public function execute(): string {
        try {
            $currentUser = AuthnProvider::getSignedInUser();
            $authz = new Authz($currentUser);
            if (!$authz->isAdmin()) {
                return "<p class='text-red-500 text-center'>Accès refusé : droits administrateur requis.</p>";
            }
        } catch (\Exception $e) {
            return "<p class='text-red-500 text-center'>Erreur : " . $e->getMessage() . "</p>";
        }

        $repo = NRVRepository::getInstance();
        $soirees = $repo->getAllSoirees();

        $output = '<div class="container mx-auto my-8 p-6 bg-white shadow-lg rounded-lg"><h2 class="text-2xl font-semibold text-purple-700 mb-4">Liste des soirées</h2><div class="space-y-4">';
        foreach ($soirees as $soiree) {
            $output .= '
                <div class="bg-gray-100 p-4 rounded-lg shadow-md">
                    <h5 class="text-lg font-bold">' . htmlspecialchars($soiree['nom_soiree']) . '</h5>
                    <p class="text-gray-700"><strong>Date:</strong> ' . htmlspecialchars($soiree['date']) . '</p>
                    <a href="?action=editSoiree&id_soiree=' . $soiree['id_soiree'] . '" class="bg-blue-500 text-white py-1 px-3 rounded hover:bg-blue-600">Modifier</a>
                    <a href="?action=cancelSoiree&id_soiree=' . $soiree['id_soiree'] . '" class="bg-red-500 text-white py-1 px-3 rounded hover:bg-red-600 ml-2">Annuler</a>
                </div>';
        }
        $output .= '</div></div>';

        return $output;
    }
}
