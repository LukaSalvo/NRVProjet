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

            // Vérifie si l'utilisateur est un administrateur
            if (!$authz->isAdmin()) {
                return "<p>Accès refusé : vous n'avez pas les droits nécessaires pour accéder à cette page.</p>";
            }

        } catch (\Exception $e) {
            return "<p>Erreur : " . $e->getMessage() . "</p>";
        }

        // Vérifie si l'ID de la soirée est fourni
        if (!isset($_GET['id_soiree'])) {
            return "<p>Erreur : ID de la soirée non fourni.</p>";
        }

        $repo = NRVRepository::getInstance();
        $soireeId = (int)$_GET['id_soiree'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Met à jour la soirée avec les nouvelles valeurs du formulaire
            $nom = filter_var($_POST['nom'], FILTER_SANITIZE_SPECIAL_CHARS);
            $date = filter_var($_POST['date'], FILTER_SANITIZE_SPECIAL_CHARS);
            $lieu = filter_var($_POST['lieu'], FILTER_SANITIZE_SPECIAL_CHARS);
            $nb_place = (int)$_POST['nb_place'];

            $repo->updateSoiree($soireeId, $nom, $date, $lieu, $nb_place);
            return "<p>Soirée modifiée avec succès !</p>";
        }

        // Affiche le formulaire de modification
        $soiree = $repo->getSoireeById($soireeId);
        return $this->renderForm($soiree);
    }

    private function renderForm(array $soiree): string {
        return '
            <form method="POST" action="">
                <label for="nom">Nom de la soirée :</label>
                <input type="text" name="nom" id="nom" value="' . htmlspecialchars($soiree['nom_soiree']) . '" required>

                <label for="date">Date :</label>
                <input type="date" name="date" id="date" value="' . htmlspecialchars($soiree['date']) . '" required>

                <label for="lieu">Lieu :</label>
                <input type="text" name="lieu" id="lieu" value="' . htmlspecialchars($soiree['nom_lieu']) . '" required>

                <label for="nb_place">Nombre de places :</label>
                <input type="number" name="nb_place" id="nb_place" value="' . (int)$soiree['nb_place'] . '" required>

                <button type="submit">Enregistrer les modifications</button>
            </form>';
    }
}
