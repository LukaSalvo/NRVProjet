<?php

namespace iutnc\nrv\action;

use iutnc\nrv\repository\NRVRepository;

class DisplaySpectacleByStyleAction extends Action {

    public function execute(): string {
        $repo = NRVRepository::getInstance();
        $styles = $repo->getAllStyles();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['style'])) {
            $style = $_POST['style'];
            $spectacles = $repo->getSpectaclesByStyle($style);

            $output = "<h2 class='text-2xl font-bold text-purple-600 my-4'>Spectacles pour le style : {$style}</h2><div class='space-y-4'>";
            foreach ($spectacles as $spectacle) {
                $output .= "
                <div class='bg-white p-4 rounded shadow-md'>
                    <a href='?action=displaySpectacleDetail&id_spectacle={$spectacle['id_spectacle']}' class='text-blue-600 hover:underline font-semibold'>
                        {$spectacle['nomSpec']} - {$spectacle['style']} ({$spectacle['duree']} minutes)
                    </a>
                </div>";
            }
            $output .= "</div><a href='?action=filterByStyle' class='text-blue-600 underline mt-4 block'>Retour à la sélection de style</a>";
            return $output;

        } else {
            $styleOptions = '';
            foreach ($styles as $style) {
                $styleOptions .= "<option value='{$style['nom_style']}'>{$style['nom_style']}</option>";
            }

            return <<<HTML
                <form method="POST" action="?action=filterByStyle" class="space-y-4 bg-gray-100 p-6 rounded-lg shadow-lg">
                    <label for="style" class="block text-lg font-medium text-gray-700">Sélectionner un style :</label>
                    <select id="style" name="style" required class="w-full p-2 border border-gray-300 rounded">
                        $styleOptions
                    </select>
                    <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded mt-4">Filtrer</button>
                </form>
            HTML;
        }
    }
}
