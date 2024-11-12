<?php

namespace iutnc\nrv\action;

use iutnc\nrv\auth\Authz;
use iutnc\nrv\auth\AuthnProvider;
use iutnc\nrv\festival\Soiree;
use iutnc\nrv\festival\Lieu;
use iutnc\nrv\repository\NRVRepository;

class AddSoireeAction extends Action {

    public function execute(): string {
        try {
            // Vérification de l'utilisateur et de ses droits
            $currentUser = AuthnProvider::getSignedInUser();
            $authz = new Authz($currentUser);
            if (!$authz->isAdmin()) {
                return "<p>Accès refusé : droits administrateur nécessaires pour accéder à cette page.</p>";
            }
        } catch (\Exception $e) {
            return "<p>Erreur : " . $e->getMessage() . "</p>";
        }

        // Affiche le formulaire ou traite la requête POST
        return ($_SERVER['REQUEST_METHOD'] === 'GET') ? $this->displayForm() : $this->addSoiree();
    }

    private function displayForm(): string {
        return '
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
            <label for="code_postal">Code postal:</label>
            <input type="number" id="code_postal" name="code_postal" required>
            <button type="submit">Ajouter</button>
        </form>';
    }

    private function addSoiree(): string {
        // Récupération et nettoyage des données
        $nom = filter_var($_POST['nom'], FILTER_SANITIZE_SPECIAL_CHARS);
        $date = filter_var($_POST['date'], FILTER_SANITIZE_SPECIAL_CHARS);
        $lieu = filter_var($_POST['lieu'], FILTER_SANITIZE_SPECIAL_CHARS);
        $nb_place = filter_var($_POST['nb_place'], FILTER_SANITIZE_NUMBER_INT);
        $nom_emplacement = filter_var($_POST['nom_emplacement'], FILTER_SANITIZE_SPECIAL_CHARS);
        $code_postal = filter_var($_POST['code_postal'], FILTER_SANITIZE_NUMBER_INT);

        $repository = NRVRepository::getInstance();
        $pdo = $repository->getPDO();

        // Vérifie si le lieu existe déjà pour éviter les doublons
        $stmt = $pdo->prepare("SELECT id_lieu FROM lieu WHERE nom_lieu = :nom_lieu AND adresse = :adresse");
        $stmt->execute(['nom_lieu' => $lieu, 'adresse' => $nom_emplacement]);
        $existingLieu = $stmt->fetch();

        // Si le lieu n'existe pas, on l'ajoute
        if (!$existingLieu) {
            $stmt = $pdo->prepare("INSERT INTO lieu (nom_lieu, adresse, nb_place) VALUES (:nom_lieu, :adresse, :nb_place)");
            $stmt->execute(['nom_lieu' => $lieu, 'adresse' => $nom_emplacement, 'nb_place' => $nb_place]);
            $id_lieu = $pdo->lastInsertId();
        } else {
            $id_lieu = $existingLieu['id_lieu'];
        }

        // Insertion de la soirée avec l'ID du lieu
        $stmt = $pdo->prepare("INSERT INTO soiree (nom_soiree, id_lieu, date) VALUES (:nom_soiree, :id_lieu, :date)");
        $stmt->execute(['nom_soiree' => $nom, 'id_lieu' => $id_lieu, 'date' => $date]);

        return "<p>Soirée ajoutée avec succès ! <a href='?action=viewSoiree&id={$pdo->lastInsertId()}'>Voir la soirée</a></p>";
    }
}
