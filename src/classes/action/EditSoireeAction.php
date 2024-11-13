<?php

namespace iutnc\nrv\action;

use iutnc\nrv\auth\Authz;
use iutnc\nrv\auth\AuthnProvider;
use iutnc\nrv\repository\NRVRepository;

class EditSoireeAction extends Action {

    public function execute(): string {
        $currentUser = AuthnProvider::getSignedInUser();
        $authz = new Authz($currentUser);

        if (!$authz->isAdmin()) {
            return "<p>Accès refusé : vous n'avez pas les droits nécessaires pour accéder à cette page.</p>";
        }

        $repo = NRVRepository::getInstance();
        $soireeId = (int)$_GET['id_soiree'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = $_POST['nom'];
            $date = $_POST['date'];
            $lieu = $_POST['lieu'];
            $nb_place = (int)$_POST['nb_place'];
            $repo->updateSoiree($soireeId, $nom, $date, $lieu, $nb_place);
            return "<p>Soirée modifiée avec succès !</p>";
        }

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
