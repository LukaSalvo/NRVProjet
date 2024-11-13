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
        <div class="flex items-center justify-center min-h-screen bg-gray-100">
            <div class="max-w-md w-full bg-white p-8 rounded-lg shadow-lg">
                <h1 class="text-3xl font-bold text-center text-purple-700 mb-6">Bienvenue sur NRV !</h1>
                <h2 class="text-xl text-center text-gray-700 mb-4">Connexion à NRV</h2>
                
                <form method="POST" action="?action=login" enctype="multipart/form-data" class="space-y-6">
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Adresse Mail :</label>
                        <input type="email" name="email" id="email" required 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                    </div>
                    
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe :</label>
                        <input type="password" name="password" id="password" required 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                    </div>
                    
                    <div>
                        <button type="submit" 
                            class="w-full py-2 px-4 bg-purple-600 text-white font-semibold rounded-lg shadow-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-opacity-50">
                            Connexion
                        </button>
                    </div>
                    
                    <div class="text-center text-gray-600 mt-4">
                        <a href="?action=register" class="text-purple-600 hover:underline">Je n'ai pas encore de compte.</a>
                    </div>
                    
                </form>
            </div>
        </div>
        HTML;
    }

    public function permettreConnexion(): string {
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = filter_var($_POST['password'], FILTER_SANITIZE_SPECIAL_CHARS);

        // Vérification de l'email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return <<<HTML
                <div class="text-center text-red-500 font-semibold mt-4">
                    <p>Adresse mail invalide !</p>
                </div>
            HTML;
        }

        try {
            // Authentification
            $userId = AuthnProvider::signin($email, $password);

            // Vérification du rôle de l'utilisateur
            $user = unserialize($_SESSION['user']);
            $authz = new Authz($user);

            if ($authz->isAdmin()) {
                return <<<HTML
                    <div class="text-center text-green-500 font-semibold mt-4">
                        <p>Bienvenue, Administrateur!</p>
                    </div>
                HTML;
            } else {
                return <<<HTML
                    <div class="text-center text-green-500 font-semibold mt-4">
                        <p>Bienvenue, Utilisateur!</p>
                    </div>
                HTML;
            }

        } catch (AuthnException $e) {
            if ($e->getMessage() === "Aucun compte trouvé pour cet email.") {
                return <<<HTML
                    <div class="text-center text-red-500 font-semibold mt-4">
                        <p>Aucun compte trouvé pour cet email. <a href="?action=register" class="text-purple-600 hover:underline">Créer un compte</a></p>
                    </div>
                HTML;
            } else {
                return <<<HTML
                    <div class="text-center text-red-500 font-semibold mt-4">
                        <p>Erreur : {$e->getMessage()}</p>
                    </div>
                HTML;
            }
        }
    }
}
