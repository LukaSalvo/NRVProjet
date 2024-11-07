<?php

namespace iutnc\nrv\action;

use iutnc\nrv\auth\Authz;
use iutnc\nrv\repository\NRVRepository;

class AddSpectacleAction extends Action {

    public function execute(): string {
        // Vérifie si l'utilisateur est un admin
        if (!isset($_SESSION['user']) || !Authz::isAdmin($_SESSION['user']['id_user'])) {
            return "<p>Accès refusé : vous n'avez pas les droits nécessaires pour accéder à cette page.</p>";
        }

        $repo = NRVRepository::getInstance();

        // Traitement du formulaire d'ajout de spectacle
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nomSpec'], $_POST['style'], $_POST['duree'], $_POST['description'], $_POST['artistes'])) {
            $nomSpec = $_POST['nomSpec'];
            $style = $_POST['style'];
            $duree = (int)$_POST['duree'];
            $description = $_POST['description'];
            $artistes = explode(',', $_POST['artistes']); // Liste d'artistes séparés par des virgules

            $spectacleId = $repo->createSpectacle($nomSpec, $style, $duree, $description, $artistes);
            return "<p>Spectacle ajouté avec succès ! <a href='?action=displaySpectacleDetail&id_spectacle={$spectacleId}'>Voir le spectacle</a></p>";
        }

        // Formulaire de création de spectacle
        return <<<HTML
            <form method="POST" action="?action=addSpectacle">
                <label for="nomSpec">Nom du spectacle :</label>
                <input type="text" id="nomSpec" name="nomSpec" required>

                <label for="style">Style musical :</label>
                <input type="text" id="style" name="style" required>

                <label for="duree">Durée (minutes) :</label>
                <input type="number" id="duree" name="duree" required>

                <label for="description">Description :</label>
                <textarea id="description" name="description" required></textarea>

                <label for="artistes">Artistes (séparés par des virgules) :</label>
                <input type="text" id="artistes" name="artistes">

                <button type="submit">Créer le spectacle</button>
            </form>
        HTML;
    }
}
