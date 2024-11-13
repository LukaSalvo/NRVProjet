<?php

namespace iutnc\nrv\action;

use iutnc\nrv\auth\Authz;
use iutnc\nrv\auth\AuthnProvider;
use iutnc\nrv\repository\NRVRepository;

class AddSoireeAction extends Action {

    public function execute(): string {
        try {
            $currentUser = AuthnProvider::getSignedInUser();
            $authz = new Authz($currentUser);
            if (!$authz->isAdmin()) {
                return "<p class='text-red-500 text-center'>Accès refusé : droits administrateur nécessaires pour accéder à cette page.</p>";
            }
        } catch (\Exception $e) {
            return "<p class='text-red-500 text-center'>Erreur : " . $e->getMessage() . "</p>";
        }

        return ($_SERVER['REQUEST_METHOD'] === 'GET') ? $this->displayForm() : $this->addSoiree();
    }

    private function displayForm(): string {
        return '
        <div class="container mx-auto my-8 p-6 bg-white shadow-lg rounded-lg">
            <h2 class="text-2xl font-semibold text-purple-700 mb-4">Ajouter une Soirée</h2>
            <form method="POST" action="">
                <div class="mb-4">
                    <label for="nom" class="block text-gray-700">Nom de la soirée:</label>
                    <input type="text" id="nom" name="nom" class="w-full border border-gray-300 p-2 rounded" required>
                </div>
                <div class="mb-4">
                    <label for="date" class="block text-gray-700">Date:</label>
                    <input type="date" id="date" name="date" class="w-full border border-gray-300 p-2 rounded" required>
                </div>
                <div class="mb-4">
                    <label for="lieu" class="block text-gray-700">Lieu:</label>
                    <input type="text" id="lieu" name="lieu" class="w-full border border-gray-300 p-2 rounded" required>
                </div>
                <div class="mb-4">
                    <label for="nb_place" class="block text-gray-700">Nombre de places:</label>
                    <input type="number" id="nb_place" name="nb_place" class="w-full border border-gray-300 p-2 rounded" required>
                </div>
                <div class="mb-4">
                    <label for="nom_emplacement" class="block text-gray-700">Nom de l\'emplacement:</label>
                    <input type="text" id="nom_emplacement" name="nom_emplacement" class="w-full border border-gray-300 p-2 rounded" required>
                </div>
                <div class="mb-4">
                    <label for="code_postal" class="block text-gray-700">Code postal:</label>
                    <input type="number" id="code_postal" name="code_postal" class="w-full border border-gray-300 p-2 rounded" required>
                </div>
                <button type="submit" class="bg-purple-700 text-white py-2 px-4 rounded hover:bg-purple-800">Ajouter</button>
            </form>
        </div>';
    }
}
