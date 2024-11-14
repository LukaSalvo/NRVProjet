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
            if (!$authz->isAdmin()) {
                return "<p class='alert alert-danger text-red-600 font-bold'>Accès refusé : droits administrateur nécessaires pour accéder à cette page.</p>";
            }
        } catch (\Exception $e) {
            return "<p class='alert alert-danger text-red-600 font-bold'>Erreur : " . $e->getMessage() . "</p>";
        }

        if (!isset($_GET['id_soiree'])) {
            return "<p class='alert alert-warning text-yellow-600 font-bold'>Erreur : ID de la soirée non fourni.</p>";
        }

        $repo = NRVRepository::getInstance();
        $soireeId = (int)$_GET['id_soiree'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = filter_var($_POST['nom'], FILTER_SANITIZE_STRING);
            $date = filter_var($_POST['date'], FILTER_SANITIZE_STRING);
            $lieu = filter_var($_POST['lieu'], FILTER_SANITIZE_STRING);
            $nb_place = filter_var($_POST['nb_place'], FILTER_VALIDATE_INT);
            $adresse = filter_var($_POST['adresse'], FILTER_SANITIZE_STRING);
            $code_postal = filter_var($_POST['code_postal'], FILTER_SANITIZE_STRING);

            if ($nom === false || $date === false || $lieu === false || $nb_place === false || $adresse === false || $code_postal === false) {
                return "<p class='alert alert-danger text-red-600 font-bold'>Erreur : données du formulaire invalides.</p>";
            }

            $repo->updateSoiree($soireeId, $nom, $date, $lieu, $nb_place, $adresse, $code_postal);
            return "<p class='alert alert-success text-green-600 font-bold'>La soirée a été modifiée avec succès.</p>";
        }

        $soiree = $repo->getSoireeById($soireeId);

        return $this->renderEditForm($soiree);
    }

    private function renderEditForm($soiree): string {
        return <<<HTML
        <form action="" method="post">
            <div class="form-group">
                <label for="nom">Nom de la soirée</label>
                <input type="text" id="nom" name="nom" value="{$soiree['nom_soiree']}" required>
            </div>
            <div class="form-group">
                <label for="date">Date</label>
                <input type="date" id="date" name="date" value="{$soiree['date']}" required>
            </div>
            <div class="form-group">
                <label for="lieu">Lieu</label>
                <input type="text" id="lieu" name="lieu" value="{$soiree['nom_lieu']}" required>
            </div>
            <div class="form-group">
                <label for="nb_place">Nombre de places</label>
                <input type="number" id="nb_place" name="nb_place" value="{$soiree['nb_place']}" required>
            </div>
            <div class="form-group">
                <label for="adresse">Adresse</label>
                <input type="text" id="adresse" name="adresse" value="{$soiree['adresse']}" required>
            </div>
            <div class="form-group">
                <label for="code_postal">Code postal</label>
                <input type="text" id="code_postal" name="code_postal" value="{$soiree['code_postal']}" required>
            </div>
            <div class="form-group text-center">
                <button type="submit" class="btn btn-primary">Modifier la soirée</button>
            </div>
        </form>
        HTML;
    }
}