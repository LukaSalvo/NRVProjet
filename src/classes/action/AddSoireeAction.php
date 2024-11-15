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
        $repo = NRVRepository::getInstance();
        $lieux = $repo->getLieux(); // Retrieve available locations

        // Generate dropdown options for locations
        $lieuOptions = '';
        foreach ($lieux as $lieu) {
            $lieuOptions .= '<option value="' . $lieu['id_lieu'] . '">' . htmlspecialchars($lieu['nom_lieu']) . ' - ' . htmlspecialchars($lieu['adresse']) . '</option>';
        }

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
                    <select id="lieu" name="lieu" class="w-full border border-gray-300 p-2 rounded" required>
                        <option value="">Sélectionnez un lieu</option>
                        ' . $lieuOptions . '
                    </select>
                </div>
                <button type="submit" class="bg-purple-700 text-white py-2 px-4 rounded hover:bg-purple-800">Ajouter</button>
            </form>
        </div>';
    }

    private function addSoiree(): string {
        $nom = htmlspecialchars($_POST['nom']);
        $date = $_POST['date'];
        $id_lieu = (int)$_POST['lieu'];

        try {
            $repo = NRVRepository::getInstance();
            $repo->createSoiree($nom, $id_lieu, $date); // Use repository method to insert soirée
            return "<p class='text-green-500 text-center'>La soirée a été ajoutée avec succès !</p>";
        } catch (\PDOException $e) {
            return "<p class='text-red-500 text-center'>Erreur lors de l'ajout de la soirée : " . $e->getMessage() . "</p>";
        }
    }
}


