<?php

namespace iutnc\nrv\action;

use iutnc\nrv\repository\NRVRepository;

class DefaultAction extends Action {

    public function execute(): string {
        $repository = NRVRepository::getInstance();
        $soirees = array_slice($repository->getAllSoirees(), 0, 3);
        $spectacleRock = $repository->getSpectaclesByStyle('Rock', null);
        $spectacleHardRock = $repository->getSpectaclesByStyle('Hard Rock', null);
        $spectacleTechno = $repository->getSpectaclesByStyle('Techno', null);

        $html = '
        <div class="container mx-auto my-8 p-6 bg-gray-100 rounded-lg shadow-lg">
            <h1 class="text-4xl font-bold text-center text-purple-700 mb-4">Le Festival qui va donner des vibrations</h1>
            <p class="text-center text-gray-600 mb-6">blablabla !<br>Commencez votre aventure musicale maintenant !</p>
            <h2 class="text-2xl font-semibold text-purple-600 mb-4">Nos soirées ayant le plus de succès</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">';

        foreach ($soirees as $soiree) {
            $status = $soiree['annuler'] == 0 ? " <span class='badge bg-red-500 text-white p-1 rounded'>Annulée</span>" : "";
            $html .= '
                <div class="card bg-white shadow-lg rounded-lg p-4">
                    <h5 class="text-xl font-bold mb-2">'.htmlspecialchars($soiree['nom_soiree']).' '.$status.'</h5>
                    <p><strong>Lieu:</strong> ' . htmlspecialchars($soiree['nom_lieu']) . '</p>
                    <p><strong>Date:</strong> ' . htmlspecialchars($soiree['date']) . '</p>
                </div>';
        }

        $html .= '</div><h2 class="text-2xl font-semibold text-purple-600 my-6">Nos recommandations de spectacle</h2><div class="grid grid-cols-1 md:grid-cols-3 gap-6">';

        foreach ([$spectacleRock, $spectacleHardRock, $spectacleTechno] as $spectacles) {
            foreach ($spectacles as $spectacle) {
                $html .= '
                    <div class="card bg-white shadow-lg rounded-lg p-4">
                        <h5 class="text-xl font-bold mb-2">' . htmlspecialchars($spectacle['nomSpec']) . '</h5>
                        <p><strong>Style:</strong> ' . htmlspecialchars($spectacle['style']) . '</p>
                        <p><strong>Durée:</strong> ' . htmlspecialchars($spectacle['duree']) . ' min</p>
                        <a href="?action=displaySpectacleDetail&id_spectacle=' . $spectacle['id_spectacle'] . '" class="btn-primary mt-2 block text-center py-2 bg-purple-700 text-white rounded hover:bg-purple-800">Voir plus de détails</a>
                    </div>';
            }
        }

        $html .= '</div></div>';
        return $html;
    }
}
