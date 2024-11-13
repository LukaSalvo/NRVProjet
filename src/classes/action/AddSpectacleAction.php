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
                return "<p class='text-red-500 text-center'>Accès refusé : droits administrateur requis.</p>";
            }

        } catch (\Exception $e) {
            return "<p class='text-red-500 text-center'>Erreur : " . $e->getMessage() . "</p>";
        }

        return ($_SERVER['REQUEST_METHOD'] === 'POST') ? $this->addSpectacle() : $this->displayForm();
    }

    private function displayForm(): string {
        return '
        <div class="container mx-auto my-8 p-6 bg-white shadow-lg rounded-lg">
            <h2 class="text-2xl font-semibold text-purple-700 mb-4">Ajouter un Spectacle</h2>
            <form method="POST" action="">
                <div class="mb-4">
                    <label for="nomSpec" class="block text-gray-700">Nom du spectacle :</label>
                    <input type="text" id="nomSpec" name="nomSpec" class="w-full border border-gray-300 p-2 rounded" required>
                </div>
                <div class="mb-4">
                    <label for="style" class="block text-gray-700">Style musical :</label>
                    <input type="text" id="style" name="style" class="w-full border border-gray-300 p-2 rounded" required>
                </div>
                <div class="mb-4">
                    <label for="duree" class="block text-gray-700">Durée (minutes) :</label>
                    <input type="number" id="duree" name="duree" class="w-full border border-gray-300 p-2 rounded" required>
                </div>
                <div class="mb-4">
                    <label for="description" class="block text-gray-700">Description :</label>
                    <textarea id="description" name="description" class="w-full border border-gray-300 p-2 rounded" required></textarea>
                </div>
                <div class="mb-4">
                    <label for="artistes" class="block text-gray-700">Artistes (séparés par des virgules) :</label>
                    <input type="text" id="artistes" name="artistes" class="w-full border border-gray-300 p-2 rounded">
                </div>
                <button type="submit" class="bg-purple-700 text-white py-2 px-4 rounded hover:bg-purple-800">Créer le spectacle</button>
            </form>
        </div>';
    }
}

