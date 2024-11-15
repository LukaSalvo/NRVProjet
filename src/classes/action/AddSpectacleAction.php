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
                return "<p class='alert alert-danger'>Accès refusé : droits administrateur nécessaires.</p>";
            }
        } catch (\Exception $e) {
            return "<p class='alert alert-danger'>Erreur : " . $e->getMessage() . "</p>";
        }

        $repo = NRVRepository::getInstance();

        // Traitement du formulaire d'ajout de spectacle
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nomSpec'], $_POST['style'], $_POST['duree'], $_POST['artistes'], $_POST['soiree'])) {
            return $this->addSpectacle($repo);
        }

        // Récupération des options
        $soirees = $repo->getSoirees();
        $styles = $repo->getStyles();

        // Formulaire
        return $this->renderForm($styles, $soirees);
    }

    private function renderForm(array $styles, array $soirees): string {
        $form = '
        <div class="max-w-3xl mx-auto bg-white shadow-lg rounded-lg p-8">
            <h2 class="text-2xl font-bold text-purple-700 mb-6">Ajouter un Spectacle</h2>
            <form action="" method="post" class="space-y-6">
                <div>
                    <label for="nomSpec" class="block text-gray-700 font-semibold mb-2">Nom du spectacle</label>
                    <input type="text" id="nomSpec" name="nomSpec" required class="input-text">
                </div>
                <div>
                    <label for="style" class="block text-gray-700 font-semibold mb-2">Style</label>
                    <select id="style" name="style" required class="select-dropdown">
                        <option value="">Sélectionnez un style</option>';
                        foreach ($styles as $style) {
                            $form .= '<option value="' . $style['id_style'] . '">' . htmlspecialchars($style['nom_style']) . '</option>';
                        }
        $form .= '
                    </select>
                </div>
                <div>
                    <label for="duree" class="block text-gray-700 font-semibold mb-2">Durée (minutes)</label>
                    <input type="number" id="duree" name="duree" required class="input-text">
                </div>
                <div>
                    <label for="artistes" class="block text-gray-700 font-semibold mb-2">Artistes (séparés par des virgules)</label>
                    <input type="text" id="artistes" name="artistes" required class="input-text">
                </div>
                <div>
                    <label for="soiree" class="block text-gray-700 font-semibold mb-2">Soirée</label>
                    <select id="soiree" name="soiree" required class="select-dropdown">
                        <option value="">Sélectionnez une soirée</option>';
                        foreach ($soirees as $soiree) {
                            $form .= '<option value="' . $soiree['id_soiree'] . '">' . htmlspecialchars($soiree['nom_soiree']) . '</option>';
                        }
        $form .= '
                    </select>
                </div>
                <div class="text-center">
                    <button type="submit" class="button-primary">Ajouter le spectacle</button>
                </div>
            </form>
        </div>';
        return $form;
    }

    private function addSpectacle(NRVRepository $repo): string {
        $nomSpec = htmlspecialchars($_POST['nomSpec']);
        $id_style = (int)$_POST['style'];
        $duree = (int)$_POST['duree'];
        $artistes = array_map('trim', explode(',', $_POST['artistes']));
        $soireeId = (int)$_POST['soiree'];

        try {
            $spectacleId = $repo->createSpectacle($nomSpec, $id_style, $duree, $artistes, $soireeId);
            return "<p class='alert alert-success'>Spectacle ajouté avec succès ! <a href='?action=displaySpectacleDetail&id_spectacle={$spectacleId}' class='text-blue-500 underline'>Voir le spectacle</a></p>";
        } catch (\PDOException $e) {
            return "<p class='alert alert-danger'>Erreur lors de l'ajout du spectacle : " . $e->getMessage() . "</p>";
        }
    }
}
