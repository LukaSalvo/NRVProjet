<?php

namespace iutnc\nrv\action;

use iutnc\nrv\auth\Authz;
use iutnc\nrv\auth\AuthnProvider;
use iutnc\nrv\repository\NRVRepository;

class EditSoireeAction extends Action {

    public function execute(): string {
        // Récupère l'utilisateur connecté et vérifie le rôle
        try {
            $currentUser = AuthnProvider::getSignedInUser();
            $authz = new Authz($currentUser);
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

        // Traite la soumission du formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = $_POST['nom'];
            $date = $_POST['date'];
            $lieu = $_POST['lieu'];
            $nb_place = (int)$_POST['nb_place'];
            $adresse = $_POST['adresse'];
            $code_postal = $_POST['code_postal'];

            // Met à jour la soirée dans la base de données
            $repo->updateSoiree($soireeId, $nom, $date, $lieu, $nb_place, $adresse, $code_postal);
            return "<p>La soirée a été modifiée avec succès.</p>";
        }

        // Récupère les détails actuels de la soirée pour pré-remplir le formulaire
        $soiree = $repo->getSoireeById($soireeId);

        return $this->renderEditForm($soiree);
    }

    private function renderEditForm(array $soiree): string {
        return '
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Modifier la Soirée</title>
        </head>
        <body>
            <div class="container">
                <h1>Modifier la Soirée</h1>
                <form method="POST" action="">
                    <label for="nom">Nom de la soirée:</label>
                    <input type="text" id="nom" name="nom" value="' . htmlspecialchars($soiree['nom_soiree']) . '" required>
                    
                    <label for="date">Date:</label>
                    <input type="date" id="date" name="date" value="' . htmlspecialchars($soiree['date']) . '" required>
                    
                    <label for="lieu">Lieu:</label>
                    <input type="text" id="lieu" name="lieu" value="' . htmlspecialchars($soiree['nom_lieu']) . '" required>
                    
                    <label for="nb_place">Nombre de places:</label>
                    <input type="number" id="nb_place" name="nb_place" value="' . (int)$soiree['nb_place'] . '" required>
                    
                    <label for="adresse">Adresse :</label>
                    <input type="text" id="adresse" name="adresse" value="' . htmlspecialchars($soiree['adresse']) . '" required>
                    
                    <label for="code_postal">Code postal :</label>
                    <input type="text" id="code_postal" name="code_postal" value="' . htmlspecialchars($soiree['code_postal']) . '" required>

                    <input type="submit" value="Enregistrer les modifications">
                </form>
            </div>
        </body>
        </html>';
    }
}
