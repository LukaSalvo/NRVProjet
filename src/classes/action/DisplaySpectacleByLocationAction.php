<?php

namespace iutnc\nrv\action;

use iutnc\nrv\repository\NRVRepository;

class DisplaySpectacleByLocationAction extends Action {

    public function execute(): string {
        $repo = NRVRepository::getInstance();
        $locations = $repo->getAllLocations();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['location'])) {
            $location = $_POST['location'];
            $spectacles = $repo->getSpectaclesByLocation($location);

            $output = "<h2 class='text-2xl font-bold text-purple-600 my-4'>Spectacles pour le lieu : {$location}</h2><div class='space-y-4'>";
            foreach ($spectacles as $spectacle) {
                $output .= "
                <div class='bg-white p-4 rounded shadow-md'>
                    <a href='?action=displaySpectacleDetail&id_spectacle={$spectacle['id_spectacle']}' class='text-blue-600 hover:underline font-semibold'>
                        {$spectacle['nomSpec']} - {$spectacle['style']} ({$spectacle['duree']} minutes)
                    </a>
                </div>";
            }
            $output .= "</div><a href='?action=filterByLocation' class='text-blue-600 underline mt-4 block'>Retour à la sélection de lieu</a>";
            return $output;

        } else {
            $locationOptions = '';
            foreach ($locations as $location) {
                $locationOptions .= "<option value='{$location['nom_lieu']}'>{$location['nom_lieu']}</option>";
            }

            return <<<HTML
                <form method="POST" action="?action=filterByLocation" class="space-y-4 bg-gray-100 p-6 rounded-lg shadow-lg">
                    <label for="location" class="block text-lg font-medium text-gray-700">Sélectionner un lieu :</label>
                    <select id="location" name="location" required class="w-full p-2 border border-gray-300 rounded">
                        $locationOptions
                    </select>
                    <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded mt-4">Filtrer</button>
                </form>
            HTML;
        }
    }
}
