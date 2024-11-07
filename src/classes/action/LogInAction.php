<?php

namespace iutnc\nrv\action;

use iutnc\nrv\auth\AuthnProvider;
use iutnc\nrv\auth\Authz;
use iutnc\nrv\exception\AuthnException;

class LogInAction extends Action {

    public function execute(): string {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->permettreConnexion();
        }

        return <<<HTML
        <h1>Bienvenue sur NRV !</h1>
        <div>
        <br>
        <h2>Connexion à NRV</h2>
        <form method="POST" action="?action=login" enctype="multipart/form-data">

            <label for="email">Adresse Mail :</label><br>
            <input type="email" name="email" id="email" required><br><br>
            
            <label for="password">Mot de passe :</label><br>
            <input type="password" name="password" id="password" required><br><br>
            <br>
            
            <input type="submit" value="Connexion">
            <br>
            <a href="?action=register">Je n'ai pas encore de compte.</a>
            
        </form>
        <br>
        
        </div>
        HTML;
    }

    public function permettreConnexion(): string {
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = filter_var($_POST['password'], FILTER_SANITIZE_SPECIAL_CHARS);

        // Vérification de l'email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "<p>Adresse mail invalide !</p>";
        }

        try {
            // Authentification
            $userId = AuthnProvider::signin($email, $password);

            // Vérification du rôle de l'utilisateur
            $user = unserialize($_SESSION['user']);
            $authz = new Authz($user);

            if ($authz->isAdmin()) {
                return "<p>Bienvenue, Administrateur!</p>";
            } else {
                return "<p>Bienvenue, Utilisateur!</p>";
            }

        } catch (AuthnException $e) {
            if ($e->getMessage() === "Aucun compte trouvé pour cet email.") {
                return <<<HTML
                    <p>Aucun compte trouvé pour cet email. <a href="?action=register">Créer un compte</a></p>
                HTML;
            } else {
                return "<p>Erreur : " . $e->getMessage() . "</p>";
            }
        }
    }
}