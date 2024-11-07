<?php

namespace iutnc\nrv\action;

class AddSoireeAction extends Action {

    public function execute() : string {
        if($_SERVER['REQUEST_METHOD']=== 'GET'){
            $_SESSION['soiree'] = [];
            return $this->displayForm();
        }
        else {
            return $this->addSoiree();
        }
    }

    private function checkPermissions(): bool {
        // Supposons que vous avez une classe Authz pour gérer les autorisations
        // et une méthode hasRole qui vérifie si l'utilisateur a un rôle spécifique
        return Authz::hasRole($_SESSION['user_id'], 100);
    }

    private function displayForm(): string {
        if(!$this->checkPermissions()){
            return 'Vous n\'avez pas les permissions nécessaires pour accéder à cette page';
        }
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
                <form method="POST" action="?action=addSoiree">
                    <label for="nom">Nom de la soirée</label>
                    <input type="text" name="nom" id="nom">
                    <label for="date">Date de la soirée</label>
                    <input type="date" name="date" id="date">
                    <label for="lieu">Lieu de la soirée</label>
                    <input type="text" name="lieu" id="lieu">
                    <label for="nb_place">Nombre de places</label>
                    <input type="number" name="nb_place" id="nb_place">
                    <label for="nom_emplacement">Nom de l\'emplacement</label>
                    <input type="text" name="nom_emplacement" id="nom_emplacement">
                    <input type="submit" value="Ajouter">
                </form>
            </div>
            
        </body>
        </html>';
    }

    private function addSoiree() : string {
        $nom = filter_var($_POST['nom'], FILTER_SANITIZE_STRING);
        $date = filter_var($_POST['date'] , FILTER_SANITIZE_STRING);
        $lieu = filter_var($_POST['lieu'], FILTER_SANITIZE_STRING);
        $nb_place = filter_var($_POST['nb_place'], FILTER_SANITIZE_NUMBER_INT);
        $nom_emplacement = filter_var($_POST['nom_emplacement'], FILTER_SANITIZE_STRING);
        $LieuSoiree = new Lieu($nb_place, $nom_emplacement, $lieu);
        $soiree = new Soiree($nom, $date);
        $soiree->setLieu($LieuSoiree);
        $_SESSION['soiree'] = serialize($soiree);
        return 'Soirée ajoutée avec succès';
    }




}