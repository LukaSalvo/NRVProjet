<?php

namespace iutnc\nrv\action;

use iutnc\nrv\auth\Authz;
use iutnc\nrv\repository\NRVRepository;

class EditSoireeAction extends Action {

    public function execute(): string {
        try {
            $currentUser = unserialize($_SESSION['user']);
            $authz = new Authz($currentUser);

            if (!$authz->isAdmin()) {
                return "<p class='text-red-500'>Accès refusé : droits administrateur nécessaires.</p>";
            }

            $repo = NRVRepository::getInstance();

            // Vérifie si la requête est un POST
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Vérifie si toutes les données attendues sont présentes
                $id_soiree = isset($_POST['id_soiree']) ? (int)$_POST['id_soiree'] : null;
                $nom_soiree = isset($_POST['nom_soiree']) ? htmlspecialchars($_POST['nom_soiree']) : '';
                $id_lieu = isset($_POST['id_lieu']) ? (int)$_POST['id_lieu'] : null;
                $date = isset($_POST['date']) ? htmlspecialchars($_POST['date']) : '';
                $annuler = isset($_POST['annuler']) ? 0 : 1;

                if (empty($id_soiree) || empty($nom_soiree) || empty($id_lieu) || empty($date)) {
                    return "<p class='text-red-500'>Erreur : Tous les champs requis doivent être remplis.</p>";
                }

                // Appelle la méthode pour mettre à jour la soirée
                $repo->updateSoiree($id_soiree, $nom_soiree, $id_lieu, $date, $annuler);

                return "<p class='text-green-500'>La soirée a été modifiée avec succès.</p><a href='?action=displaySoiree'>Retour à la liste des soirées</a>";
            }

            // Vérifie si un ID de soirée est passé via GET
            if (!isset($_GET['id_soiree'])) {
                return "<p class='text-red-500'>Erreur : ID de la soirée non fourni.</p>";
            }

            $id_soiree = (int)$_GET['id_soiree'];
            $soiree = $repo->getSoireeById($id_soiree);

            if (!$soiree) {
                return "<p class='text-red-500'>Erreur : Soirée non trouvée.</p>";
            }

            $lieux = $repo->getLieux();
            return $this->renderForm($soiree, $lieux);

        } catch (\Exception $e) {
            return "<p class='text-red-500'>Erreur : " . $e->getMessage() . "</p>";
        }
    }

    private function renderForm(array $soiree, array $lieux): string {
        $form = '
        <div class="container mx-auto my-8 p-6 bg-white shadow-lg rounded-lg">
            <h2 class="text-2xl font-semibold text-purple-700 mb-4">Modifier une Soirée</h2>
            <form method="POST" action="">
                <input type="hidden" name="id_soiree" value="' . htmlspecialchars($soiree['id_soiree']) . '">
                <div class="mb-4">
                    <label for="nom_soiree" class="block text-gray-700">Nom de la soirée :</label>
                    <input type="text" id="nom_soiree" name="nom_soiree" class="w-full border border-gray-300 p-2 rounded" value="' . htmlspecialchars($soiree['nom_soiree']) . '" required>
                </div>
                <div class="mb-4">
                    <label for="id_lieu" class="block text-gray-700">Lieu :</label>
                    <select id="id_lieu" name="id_lieu" class="w-full border border-gray-300 p-2 rounded" required>';
                    foreach ($lieux as $lieu) {
                        $selected = $soiree['id_lieu'] == $lieu['id_lieu'] ? 'selected' : '';
                        $form .= '<option value="' . $lieu['id_lieu'] . '" ' . $selected . '>' . htmlspecialchars($lieu['nom_lieu']) . '</option>';
                    }
        $form .= '
                    </select>
                </div>
                <div class="mb-4">
                    <label for="date" class="block text-gray-700">Date :</label>
                    <input type="date" id="date" name="date" class="w-full border border-gray-300 p-2 rounded" value="' . htmlspecialchars($soiree['date']) . '" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Annulation :</label>
                    <input type="checkbox" id="annuler" name="annuler" ' . ($soiree['annuler'] == 0 ? 'checked' : '') . '>
                    <label for="annuler" class="text-gray-700">Annuler cette soirée</label>
                </div>
                <button type="submit" class="bg-purple-700 text-white py-2 px-4 rounded hover:bg-purple-800">Enregistrer les modifications</button>
            </form>
        </div>';
        return $form;
    }
}
