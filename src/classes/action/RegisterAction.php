<?php

namespace iutnc\nrv\action;

use iutnc\nrv\auth\AuthnProvider;
use iutnc\nrv\exception\AuthnException;
use iutnc\nrv\repository\NRVRepository;

class RegisterAction extends Action {
    public function execute(): string {
        if (isset($_POST['email'], $_POST['username'], $_POST['password'], $_POST['confirm_password'])) {
            $email = $_POST['email'];
            $username = $_POST['username'];
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirm_password'];

            if ($password !== $confirmPassword) {
                return "Erreur : Les mots de passe ne correspondent pas.";
            }

            $repo = NRVRepository::getInstance();

            try {
                if ($repo->getUserByEmail($email)) {
                    return "Erreur : Cet email est déjà utilisé.";
                }

                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $repo->createUser($email, $username, $hashedPassword);

                return "Inscription réussie ! Vous pouvez maintenant vous connecter.";

            } catch (AuthnException $e) {
                return "Erreur lors de l'inscription : " . $e->getMessage();
            }
        }

        return <<<HTML
            <form method="POST" action="?action=register">
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required>
                
                <label for="username">Nom d'utilisateur :</label>
                <input type="text" id="username" name="username" required>
                
                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" required>
                
                <label for="confirm_password">Confirmer le mot de passe :</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                
                <button type="submit">S'inscrire</button>
            </form>
        HTML;
    }
}
