<?php

namespace iutnc\nrv\action;

use iutnc\nrv\auth\AuthnProvider;
use iutnc\nrv\auth\Authz;
use iutnc\nrv\exception\AuthnException;

class LogInAction extends Action {

    public function execute(): string {
        if (isset($_SESSION['user'])) {
            return "Vous êtes déjà connecté en tant que " . $_SESSION['user']['nomUtilisateur'];
        }

        if (isset($_POST['email']) && isset($_POST['password'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];

            try {
                $userId = AuthnProvider::signin($email, $password);

                if (Authz::isAdmin($userId)) {
                    return "Bienvenue, Administrateur!";
                } else {
                    return "Bienvenue, Utilisateur!";
                }

            } catch (AuthnException $e) {
                if ($e->getMessage() === "Aucun compte trouvé pour cet email.") {
                    return <<<HTML
                        <p>Aucun compte trouvé pour cet email. <a href="?action=register">Créer un compte</a></p>
                    HTML;
                } else {
                    return "Erreur : " . $e->getMessage();
                }
            }

        return <<<HTML
            <form method="POST" action="?action=login">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                
                <label for="password">Mot de passe:</label>
                <input type="password" id="password" name="password" required>
                
                <button type="submit">Se connecter</button>
            </form>
        HTML;
    }
}
