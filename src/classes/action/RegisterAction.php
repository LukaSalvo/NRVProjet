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
                return <<<HTML
                    <div class="text-center text-red-500 font-semibold mt-4">
                        <p>Erreur : Les mots de passe ne correspondent pas.</p>
                    </div>
                HTML;
            }

            $repo = NRVRepository::getInstance();

            try {
                if ($repo->getUserByEmail($email)) {
                    return <<<HTML
                        <div class="text-center text-red-500 font-semibold mt-4">
                            <p>Erreur : Cet email est déjà utilisé.</p>
                        </div>
                    HTML;
                }

                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $repo->createUser($email, $username, $hashedPassword);

                return <<<HTML
                    <div class="text-center text-green-500 font-semibold mt-4">
                        <p>Inscription réussie ! Vous pouvez maintenant vous connecter.</p>
                    </div>
                HTML;

            } catch (AuthnException $e) {
                return <<<HTML
                    <div class="text-center text-red-500 font-semibold mt-4">
                        <p>Erreur lors de l'inscription : {$e->getMessage()}</p>
                    </div>
                HTML;
            }
        }

        // Formulaire d'inscription avec Tailwind CSS
        return <<<HTML
            <div class="flex items-center justify-center min-h-screen bg-gray-100">
                <div class="max-w-md w-full bg-white p-8 rounded-lg shadow-lg">
                    <h2 class="text-2xl font-bold text-center text-purple-700 mb-6">Inscription sur NRV</h2>
                    <form method="POST" action="?action=register" class="space-y-4">
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email :</label>
                            <input type="email" id="email" name="email" required 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700">Nom d'utilisateur :</label>
                            <input type="text" id="username" name="username" required 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe :</label>
                            <input type="password" id="password" name="password" required 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        
                        <div>
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirmer le mot de passe :</label>
                            <input type="password" id="confirm_password" name="confirm_password" required 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        
                        <div>
                            <button type="submit" 
                                class="w-full py-2 px-4 bg-purple-600 text-white font-semibold rounded-lg shadow-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-opacity-50">
                                S'inscrire
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        HTML;
    }
}
