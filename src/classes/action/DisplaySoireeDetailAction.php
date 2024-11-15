<?php

namespace iutnc\nrv\action;

use iutnc\nrv\repository\NRVRepository;

class DisplaySoireeDetailAction extends Action {

    public function execute(): string {
        if (!isset($_GET['id_soiree'])) {
            return "<p class='alert alert-danger'>Erreur : ID de la soirée non fourni.</p>";
        }

        $repo = NRVRepository::getInstance();
        $id_soiree = (int)$_GET['id_soiree'];
        $soiree = $repo->getSoireeById($id_soiree);
        $spectacles = $repo->getSpectaclesBySoireeId($id_soiree);

        if (!$soiree) {
            return "<p class='alert alert-danger'>Erreur : Soirée non trouvée.</p>";
        }

        $output = '<div class="container mx-auto my-8 p-6 bg-white rounded-lg shadow-lg">
                    <h2 class="text-3xl font-bold text-indigo-700 mb-6">' . htmlspecialchars($soiree['nom_soiree']) . '</h2>
                    <p class="text-gray-700 mb-4"><strong>Lieu:</strong> ' . htmlspecialchars($soiree['nom_lieu']) . '</p>
                    <p class="text-gray-700 mb-4"><strong>Date:</strong> ' . htmlspecialchars($soiree['date']) . '</p>';

        if ($soiree['annuler'] == 0) {
            $output .= '<p class="text-red-500 font-semibold mb-4">Cette soirée est annulée.</p>';
        }

        $output .= '<h3 class="text-2xl font-bold text-indigo-600 mb-4">Spectacles associés</h3>';
        if (empty($spectacles)) {
            $output .= '<p class="text-gray-700">Aucun spectacle associé à cette soirée.</p>';
        } else {
            $output .= '<ul class="space-y-4">';
            foreach ($spectacles as $spectacle) {
                if (isset($spectacle['id_spectacle'], $spectacle['nomSpec'], $spectacle['nom_style'], $spectacle['duree'])) {
                    $output .= '
                        <li class="bg-gray-100 p-4 rounded-lg shadow hover:bg-gray-200 transition">
                            <a href="?action=displaySpectacleDetail&id_spectacle=' . htmlspecialchars($spectacle['id_spectacle']) . '" class="block text-gray-800 hover:text-indigo-600">
                                <h4 class="text-xl font-semibold">' . htmlspecialchars($spectacle['nomSpec']) . '</h4>
                                <p class="text-gray-700"><strong>Style:</strong> ' . htmlspecialchars($spectacle['nom_style']) . '</p>
                                <p class="text-gray-700"><strong>Durée:</strong> ' . htmlspecialchars($spectacle['duree']) . ' minutes</p>
                            </a>
                        </li>';
                } else {
                    $output .= '<li class="text-red-500">Données invalides pour ce spectacle.</li>';
                }
            }
            $output .= '</ul>';
        }

        $output .= '</div>';

        return $output;
    }
}
