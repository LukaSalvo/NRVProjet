<?php

namespace iutnc\nrv\action;

use iutnc\nrv\auth\AuthnProvider;
use iutnc\nrv\auth\Authz;
use iutnc\nrv\exception\AuthnException;

class LogInAction extends Action {

    public function execute(): string {
        if (isset($_POST['email']) && isset($_POST['password'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];

            try {
                AuthnProvider::signin($email, $password);

                if (Authz::isAdmin()) {
                    return "Bienvenue, Administrateur!";
                } else {
                    return "Bienvenue, Utilisateur!";
                }

            } catch (AuthnException $e) {
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
