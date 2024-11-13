<?php

namespace iutnc\nrv\action;

use iutnc\nrv\auth\Authz;
use iutnc\nrv\auth\AuthnProvider;
use iutnc\nrv\repository\NRVRepository;

class EditSoireeAction extends Action {

    public function execute(): string {
        try {
            $currentUser = AuthnProvider::getSignedInUser();
            $authz = new Authz($currentUser);
            if (!$authz->isAdmin()) {
                return "<p class='alert alert-danger text-red-600 font-bold'>Accès refusé : droits administrateur nécessaires pour accéder à cette page.</p>";
            }
        } catch (\Exception $e) {
            return "<p class='alert alert-danger text-red-600 font-bold'>Erreur : " . $e->getMessage() . "</p>";
        }

        if (!isset($_GET['id_soiree'])) {
            return "<p class='alert alert-warning text-yellow-600 font-bold'>Erreur : ID de la soirée non fourni.</p>";
        }

        $repo = NRVRepository::getInstance();
        $soireeId = (int)$_GET['id_soiree'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = $_POST['nom'];
            $date = $_POST['date'];
            $lieu = $_POST['lieu'];
            $nb_place = (int)$_POST['nb_place'];
            $adresse = $_POST['adresse'];
            $code_postal = $_POST['code_postal'];

            $repo->updateSoiree($soireeId, $nom, $date, $lieu, $nb_place, $adresse, $code_postal);
            return "<p class='alert alert-success text-green-600 font-bold'>La soirée a été modifiée avec succès.</p>";
        }

        $soiree = $repo->getSoireeById($soireeId);

        return $this->renderEditForm($soiree);
    }

    private function renderEditForm(array $soiree): string {
        return '
        <div class="container bg-gradient-to-r from-purple-100 to-purple-50 p-8 rounded-lg shadow-lg">
            <h2 class="text-3xl font-extrabold text-purple-800 mb-6 text-center">Modifier la Soirée</h2>
            <form method="POST" action="" class="space-y-6">
                <div class="mb-4">
                    <label for="nom" class="block font-semibold text-gray-700">Nom de la soirée:</label>
                    <input type="text" id="nom" name="nom" class="input-text" value="' . htmlspecialchars($soiree['nom_soiree']) . '" required>
                </div>
                <div class="mb-4">
                    <label for="date" class="block font-semibold text-gray-700">Date:</label>
                    <input type="date" id="date" name="date" class="input-text" value="' . htmlspecialchars($soiree['date']) . '" required>
                </div>
                <div class="mb-4">
                    <label for="lieu" class="block font-semibold text-gray-700">Lieu:</label>
                    <input type="text" id="lieu" name="lieu" class="input-text" value="' . htmlspecialchars($soiree['nom_lieu']) . '" required>
                </div>
                <div class="mb-4">
                    <label for="nb_place" class="block font-semibold text-gray-700">Nombre de places:</label>
                    <input type="number" id="nb_place" name="nb_place" class="input-text" value="' . (int)$soiree['nb_place'] . '" required>
                </div>
                <div class="mb-4">
                    <label for="adresse" class="block font-semibold text-gray-700">Adresse :</label>
                    <input type="text" id="adresse" name="adresse" class="input-text" value="' . htmlspecialchars($soiree['adresse']) . '" required>
                </div>
                <div class="mb-4">
                    <label for="code_postal" class="block font-semibold text-gray-700">Code postal :</label>
                    <input type="text" id="code_postal" name="code_postal" class="input-text" value="' . htmlspecialchars($soiree['code_postal']) . '" required>
                </div>
                <button type="submit" class="button-primary w-full">Enregistrer les modifications</button>
            </form>
        </div>';
    }
}
