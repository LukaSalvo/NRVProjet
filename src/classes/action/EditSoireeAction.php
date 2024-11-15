<?php
namespace iutnc\nrv\action;

use iutnc\nrv\auth\AuthnProvider;
use iutnc\nrv\auth\Authz;
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
            $nom = filter_var($_POST['nom'], FILTER_SANITIZE_STRING);
            $date = filter_var($_POST['date'], FILTER_SANITIZE_STRING);
            $lieu = filter_var($_POST['lieu'], FILTER_SANITIZE_STRING);
            $nb_place = filter_var($_POST['nb_place'], FILTER_VALIDATE_INT);
            $adresse = filter_var($_POST['adresse'], FILTER_SANITIZE_STRING);
            $code_postal = filter_var($_POST['code_postal'], FILTER_SANITIZE_STRING);

            if ($nom === false || $date === false || $lieu === false || $nb_place === false || $adresse === false || $code_postal === false) {
                return "<p class='alert alert-danger text-red-600 font-bold'>Erreur : données du formulaire invalides.</p>";
            }

            $repo->updateSoiree($soireeId, $nom, $date, $lieu, $nb_place, $adresse, $code_postal);
            return "<p class='alert alert-success text-green-600 font-bold'>La soirée a été modifiée avec succès.</p>";
        }

        $soiree = $repo->getSoireeById($soireeId);

        return $this->renderEditForm($soiree);
    }

    private function renderEditForm($soiree): string {
        return <<<HTML
        <div class="container mx-auto my-8 p-6 bg-white shadow-lg rounded-lg">
            <h2 class="text-2xl font-semibold text-purple-700 mb-4">Modifier la Soirée</h2>
            <form method="POST" action="">
                <div class="mb-4">
                    <label for="nom" class="block text-gray-700">Nom de la soirée:</label>
                    <input type="text" id="nom" name="nom" class="w-full border border-gray-300 p-2 rounded" value="{$soiree['nom_soiree']}" required>
                </div>
                <div class="mb-4">
                    <label for="date" class="block text-gray-700">Date:</label>
                    <input type="date" id="date" name="date" class="w-full border border-gray-300 p-2 rounded" value="{$soiree['date']}" required>
                </div>
                <div class="mb-4">
                    <label for="lieu" class="block text-gray-700">Lieu:</label>
                    <input type="text" id="lieu" name="lieu" class="w-full border border-gray-300 p-2 rounded" value="{$soiree['nom_lieu']}" required>
                </div>
                <div class="mb-4">
                    <label for="nb_place" class="block text-gray-700">Nombre de places:</label>
                    <input type="number" id="nb_place" name="nb_place" class="w-full border border-gray-300 p-2 rounded" value="{$soiree['nb_place']}" required>
                </div>
                <div class="mb-4">
                    <label for="adresse" class="block text-gray-700">Adresse:</label>
                    <input type="text" id="adresse" name="adresse" class="w-full border border-gray-300 p-2 rounded" value="{$soiree['adresse']}" required>
                </div>
                <div class="mb-4">
                    <label for="code_postal" class="block text-gray-700">Code postal:</label>
                    <input type="text" id="code_postal" name="code_postal" class="w-full border border-gray-300 p-2 rounded" value="{$soiree['code_postal']}" required>
                </div>
                <button type="submit" class="bg-purple-700 text-white py-2 px-4 rounded hover:bg-purple-800">Modifier</button>
            </form>
        </div>
        HTML;
    }
}