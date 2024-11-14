<?php
namespace iutnc\nrv\action;

use iutnc\nrv\auth\Authz;
use iutnc\nrv\auth\AuthnProvider;
use iutnc\nrv\repository\NRVRepository;

class AddSpectacleAction extends Action {

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

        $repo = NRVRepository::getInstance();

        // Traitement du formulaire d'ajout de spectacle
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nomSpec'], $_POST['style'], $_POST['duree'], $_POST['description'], $_POST['artistes'], $_POST['soiree'])) {
            $nomSpec = filter_var($_POST['nomSpec'], FILTER_SANITIZE_STRING);
            $id_style = filter_var($_POST['style'], FILTER_SANITIZE_NUMBER_INT);
            $duree = filter_var($_POST['duree'], FILTER_SANITIZE_NUMBER_INT);
            $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
            $artistes = array_map('trim', explode(',', filter_var($_POST['artistes'], FILTER_SANITIZE_STRING))); // Liste d'artistes séparés par des virgules
            $soireeId = filter_var($_POST['soiree'], FILTER_SANITIZE_NUMBER_INT);

            if ($nomSpec === false || $id_style === false || $duree === false || $description === false || $soireeId === false) {
                return "<p>Erreur : données du formulaire invalides.</p>";
            }

            try {
                $spectacleId = $repo->createSpectacle($nomSpec, $id_style, $duree, $description, $artistes, $soireeId);
                return "<p>Spectacle ajouté avec succès ! <a href='?action=displaySpectacleDetail&id_spectacle={$spectacleId}'>Voir le spectacle</a></p>";
            } catch (\PDOException $e) {
                return "<p>Erreur lors de l'ajout du spectacle : " . $e->getMessage() . "</p>";
            }
        }

        // Récupérer les soirées disponibles
        $soirees = $repo->getSoirees();

        // Récupérer les styles disponibles
        $styles = $repo->getStyles();

        // Formulaire de création de spectacle
        $form = '
        <form action="" method="post">
            <div class="form-group">
                <label for="nomSpec">Nom du spectacle</label>
                <input type="text" id="nomSpec" name="nomSpec" required>
            </div>
            <div class="form-group">
                <label for="style">Style</label>
                <select id="style" name="style" required>
                    <option value="">Sélectionnez un style</option>';
                    foreach ($styles as $style) {
                        $form .= '<option value="' . $style['id_style'] . '">' . $style['nom_style'] . '</option>';
                    }
        $form .= '
                </select>
            </div>
            <div class="form-group">
                <label for="duree">Durée (minutes)</label>
                <input type="number" id="duree" name="duree" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" required></textarea>
            </div>
            <div class="form-group">
                <label for="artistes">Artistes (séparés par des virgules)</label>
                <input type="text" id="artistes" name="artistes" required>
            </div>
            <div class="form-group">
                <label for="soiree">Soirée</label>
                <select id="soiree" name="soiree" required>
                    <option value="">Sélectionnez une soirée</option>';
                    foreach ($soirees as $soiree) {
                        $form .= '<option value="' . $soiree['id_soiree'] . '">' . $soiree['nom_soiree'] . '</option>';
                    }
        $form .= '
                </select>
            </div>
            <div class="form-group text-center">
                <button type="submit" class="btn btn-primary">Ajouter le spectacle</button>
            </div>
        </form>';

        return $form;
    }
}