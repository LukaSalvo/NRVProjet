<?php

namespace iutnc\nrv\action;

use iutnc\nrv\auth\Authz;
use iutnc\nrv\repository\NRVRepository;

class DisplaySoireeAction extends Action {

    public function execute(): string {
        $repo = NRVRepository::getInstance();
        $soirees = $repo->getAllSoirees();

        $output = '<div class="container mx-auto my-8 p-6 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg shadow-lg">
                    <h2 class="text-3xl font-bold text-indigo-700 mb-6 text-center">Liste des Soirées</h2>
                    <div class="space-y-6">';

        foreach ($soirees as $soiree) {
            $status = $soiree['annuler'] == 0 
                      ? "<span class='inline-block bg-red-500 text-white px-2 py-1 rounded-full ml-2 text-xs font-semibold'>Annulée</span>" 
                      : "<span class='inline-block bg-green-500 text-white px-2 py-1 rounded-full ml-2 text-xs font-semibold'>Active</span>";
            $output .= '
                <div class="bg-white p-6 shadow-md rounded-lg hover:shadow-lg transition duration-200">
                    <h5 class="text-2xl font-semibold text-gray-800">' . htmlspecialchars($soiree['nom_soiree']) . ' ' . $status . '</h5>
                    <p class="text-gray-700"><strong>Lieu:</strong> ' . htmlspecialchars($soiree['nom_lieu']) . '</p>
                    <p class="text-gray-700"><strong>Date:</strong> ' . htmlspecialchars($soiree['date']) . '</p>';

            if ($soiree['annuler'] == 0 && isset($_SESSION['user']) && (new Authz(unserialize($_SESSION['user'])))->isAdmin()) {
                $output .= '<a href="?action=uncancelSoiree&id_soiree=' . $soiree['id_soiree'] . '" class="inline-block mt-3 bg-yellow-500 text-gray-900 font-semibold py-2 px-4 rounded hover:bg-yellow-600">Retirer l\'annulation</a>';
            }

            $output .= '</div>';
        }
        $output .= '</div></div>';

        return $output;
    }
}
