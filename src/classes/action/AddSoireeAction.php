<?php

namespace iutnc\nrv\action;

use iutnc\nrv\auth\Authz;
use iutnc\nrv\auth\AuthnProvider;
use iutnc\nrv\festival\Soiree;
use iutnc\nrv\festival\Lieu;
use iutnc\nrv\repository\NRVRepository;

class AddSoireeAction extends Action {

    public function execute(): string {
        // Récupère l'utilisateur connecté
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

        // Affiche le formulaire ou traite la requête POST
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $_SESSION['soiree'] = [];
            return $this->displayForm();
        } else {
            return $this->addSoiree();
        }
    }

    private function displayForm(): string {
        return '
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Ajouter une soirée</title>
        </head>
        <body>
            <div class="container">
                <h1>Ajouter une soirée</h1>
                <form method="POST" action="">
                    <label for="nom">Nom de la soirée:</label>
                    <input type="text" id="nom" name="nom" required>
                    <label for="date">Date:</label>
                    <input type="date" id="date" name="date" required>
                    <label for="lieu">Lieu:</label>
                    <input type="text" id="lieu" name="lieu" required>
                    <label for="nb_place">Nombre de places:</label>
                    <input type="number" id="nb_place" name="nb_place" required>
                    <label for="nom_emplacement">Nom de l\'emplacement:</label>
                    <input type="text" id="nom_emplacement" name="nom_emplacement" required>
                    <label for="code_postal">Code postal : </label>
                    <input type="number" id="code_postal" name="code_postal" required>
                    <input type="submit" value="Ajouter">
                </form>
            </div>
        </body>
        </html>';
    }

    private function addSoiree(): string {
        $nom = filter_var($_POST['nom'], FILTER_SANITIZE_SPECIAL_CHARS);
        $date = filter_var($_POST['date'], FILTER_SANITIZE_SPECIAL_CHARS);
        $lieu = filter_var($_POST['lieu'], FILTER_SANITIZE_SPECIAL_CHARS);
        $nb_place = filter_var($_POST['nb_place'], FILTER_SANITIZE_NUMBER_INT);
        $nom_emplacement = filter_var($_POST['nom_emplacement'], FILTER_SANITIZE_SPECIAL_CHARS);
        $code_postal = filter_var($_POST['code_postal'], FILTER_SANITIZE_NUMBER_INT);

        // Appel à la base de données pour ajouter la soirée
        NRVRepository::setConfig(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "db.config.ini");
        $repository = NRVRepository::getInstance();
        $pdo = $repository->getPDO();

        $LieuSoiree = new Lieu($nb_place, $nom_emplacement, $lieu, $code_postal);
        $soiree = new Soiree($nom, $date);
        $soiree->setLieu($LieuSoiree);

        $_SESSION['soiree'] = serialize($soiree);

        // Insertion du lieu
        $inser1 = $pdo->prepare("INSERT INTO lieu (nom_lieu, adresse, nb_place) VALUES (:nom_lieu, :adresse, :nb_place)");
        $inser1->execute(['nom_lieu' => $lieu, 'adresse' => $nom_emplacement, 'nb_place' => $nb_place]);

        // Récupération de l'ID du lieu inséré
        $id_lieu = $pdo->lastInsertId();

        // Insertion de la soirée avec l'ID du lieu
        $inser2 = $pdo->prepare("INSERT INTO soiree (nom_soiree, id_lieu, date) VALUES (:nom_soiree, :id_lieu, :date)");
        $inser2->execute(['nom_soiree' => $nom, 'id_lieu' => $id_lieu, 'date' => $date]);

        return 'Soirée ajoutée avec succès';
    }
}
