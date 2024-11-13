<?php

namespace iutnc\nrv\action;

use iutnc\nrv\repository\NRVRepository;

class DisplaySpectacleByDateAction extends Action {

    public function execute(): string {
        $repo = NRVRepository::getInstance();
        $dates = $repo->getAllDates();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['date'])) {
            $date = $_POST['date'];
            $spectacles = $repo->getSpectaclesByDate($date);

            $output = "<h2 class='text-2xl font-bold text-purple-600 my-4'>Spectacles pour la date : {$date}</h2><div class='space-y-4'>";
            foreach ($spectacles as $spectacle) {
                $output .= "
                <div class='bg-white p-4 rounded shadow-md'>
                    <a href='?action=displaySpectacleDetail&id_spectacle={$spectacle['id_spectacle']}' class='text-blue-600 hover:underline font-semibold'>
                        {$spectacle['nomSpec']} - {$spectacle['style']} ({$spectacle['duree']} minutes)
                    </a>
                </div>";
            }
            $output .= "</div><a href='?action=filterByDate' class='text-blue-600 underline mt-4 block'>Retour à la sélection de date</a>";
            return $output;

        } else {
            $dateOptions = '';
            foreach ($dates as $date) {
                $dateOptions .= "<option value='{$date['date']}'>{$date['date']}</option>";
            }

            return <<<HTML
                <form method="POST" action="?action=filterByDate" class="space-y-4 bg-gray-100 p-6 rounded-lg shadow-lg">
                    <label for="date" class="block text-lg font-medium text-gray-700">Sélectionner une date :</label>
                    <select id="date" name="date" required class="w-full p-2 border border-gray-300 rounded">
                        $dateOptions
                    </select>
                    <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded mt-4">Filtrer</button>
                </form>
            HTML;
        }
    }
}
