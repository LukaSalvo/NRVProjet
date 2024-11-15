<?php

namespace iutnc\nrv\action;

use iutnc\nrv\auth\Authz;
use iutnc\nrv\auth\AuthnProvider;
use iutnc\nrv\repository\NRVRepository;

class AddSpectacleAction extends Action {

    public function execute(): string {
        try {
            $currentUser = AuthnProvider::getSignedInUser();
            $authz = new Authz($currentUser);

            if (!$authz->isAdmin()) {
                return "<p class='text-red-500 text-center'>Accès refusé : droits administrateur nécessaires.</p>";
            }
        } catch (\Exception $e) {
            return "<p class='text-red-500 text-center'>Erreur : " . $e->getMessage() . "</p>";
        }

        $repo = NRVRepository::getInstance();

        // Gestion du formulaire d'ajout
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->addSpectacle($repo); // Correction ici : nom de la méthode.
        }

        // Récupération des options pour le formulaire
        $soirees = $repo->getSoirees();
        $styles = $repo->getStyles();
        $artistes = $repo->getArtistes();

        // Affichage du formulaire
        return $this->renderForm($styles, $soirees, $artistes);
    }

    private function renderForm(array $styles, array $soirees, array $artistes): string {
        $form = '
        <div class="container mx-auto my-8 p-6 bg-white shadow-lg rounded-lg">
            <h2 class="text-2xl font-semibold text-purple-700 mb-4">Ajouter un Spectacle</h2>
            <form method="POST" action="">
                <div class="mb-4">
                    <label for="nomSpec" class="block text-gray-700">Nom du spectacle :</label>
                    <input type="text" id="nomSpec" name="nomSpec" class="w-full border border-gray-300 p-2 rounded" required>
                </div>
                <div class="mb-4">
                    <label for="style" class="block text-gray-700">Style :</label>
                    <select id="style" name="style" class="w-full border border-gray-300 p-2 rounded" required>
                        <option value="">Sélectionnez un style</option>';
                        foreach ($styles as $style) {
                            $form .= '<option value="' . $style['id_style'] . '">' . htmlspecialchars($style['nom_style']) . '</option>';
                        }
        $form .= '
                    </select>
                </div>
                <div class="mb-4">
                    <label for="duree" class="block text-gray-700">Durée (minutes) :</label>
                    <input type="number" id="duree" name="duree" class="w-full border border-gray-300 p-2 rounded" required>
                </div>
                <div class="mb-4">
                    <label for="artistes" class="block text-gray-700">Artistes existants :</label>
                    <select id="artistes" name="artistes[]" class="w-full border border-gray-300 p-2 rounded" multiple>
                        <option value="">Sélectionnez un ou plusieurs artistes</option>';
                        foreach ($artistes as $artiste) {
                            $form .= '<option value="' . $artiste['id_artiste'] . '">' . htmlspecialchars($artiste['nom_artiste']) . '</option>';
                        }
        $form .= '
                    </select>
                    <small class="text-gray-500">Maintenez Ctrl (Windows) ou Commande (Mac) pour sélectionner plusieurs artistes.</small>
                </div>
                <div class="mb-4">
                    <label for="newArtiste" class="block text-gray-700">Artiste inexistant ?</label>
                    <input type="text" id="newArtiste" name="newArtiste" class="w-full border border-gray-300 p-2 rounded" placeholder="Entrez le nom du nouvel artiste">
                </div>
                <div class="mb-4">
                    <label for="soiree" class="block text-gray-700">Soirée associée :</label>
                    <select id="soiree" name="soiree" class="w-full border border-gray-300 p-2 rounded" required>
                        <option value="">Sélectionnez une soirée</option>';
                        foreach ($soirees as $soiree) {
                            $form .= '<option value="' . $soiree['id_soiree'] . '">' . htmlspecialchars($soiree['nom_soiree']) . '</option>';
                        }
        $form .= '
                    </select>
                </div>
                <button type="submit" class="bg-purple-700 text-white py-2 px-4 rounded hover:bg-purple-800">Ajouter le spectacle</button>
            </form>
        </div>';
        return $form;
    }

    private function addSpectacle(NRVRepository $repo): string {
        $nomSpec = htmlspecialchars($_POST['nomSpec']);
        $id_style = (int)$_POST['style'];
        $duree = (int)$_POST['duree'];
        $soireeId = (int)$_POST['soiree'];
        $artistes = $_POST['artistes'] ?? []; // Artistes existants

        // Gestion du nouvel artiste
        $newArtiste = trim($_POST['newArtiste'] ?? '');
        if (!empty($newArtiste)) {
            try {
                $newArtisteId = $repo->createArtiste($newArtiste); // Ajout à la base de données
                $artistes[] = $newArtisteId; // Ajout de l'ID à la liste
            } catch (\PDOException $e) {
                return "<p class='text-red-500 text-center'>Erreur lors de l'ajout du nouvel artiste : " . $e->getMessage() . "</p>";
            }
        }

        try {
            $spectacleId = $repo->createSpectacle($nomSpec, $id_style, $duree, $artistes, $soireeId);
            return "<p class='text-green-500 text-center'>Spectacle ajouté avec succès ! <a href='?action=displaySpectacleDetail&id_spectacle={$spectacleId}' class='text-blue-500 underline'>Voir le spectacle</a></p>";
        } catch (\PDOException $e) {
            return "<p class='text-red-500 text-center'>Erreur lors de l'ajout du spectacle : " . $e->getMessage() . "</p>";
        }
    }
}
