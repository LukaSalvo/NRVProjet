<?php

namespace iutnc\nrv\dispatch;

use iutnc\nrv\action\DisplaySoireeDetailAction;
use iutnc\nrv\action\UncancelSoireeAction;
use iutnc\nrv\action\CancelSoireeAction;
use iutnc\nrv\action\EditSoireeAction;
use iutnc\nrv\action\DisplayMesFavorisAction;
use iutnc\nrv\action\EditSoireeListAction;
use iutnc\nrv\action\AddSoireeAction;
use iutnc\nrv\action\DefaultAction;
use iutnc\nrv\action\DisplaySoireeAction;
use iutnc\nrv\action\DisplaySpectacleByDateAction;
use iutnc\nrv\action\DisplaySpectacleByStyleAction;
use iutnc\nrv\action\DisplaySpectacleByLocationAction;
use iutnc\nrv\action\DisplaySpectacleDetailAction;
use iutnc\nrv\action\LogInAction;
use iutnc\nrv\action\LogOutAction;
use iutnc\nrv\action\RegisterAction;
use iutnc\nrv\action\AddSpectacleAction;
use iutnc\nrv\action\LikeAction;
use iutnc\nrv\auth\Authz;
use iutnc\nrv\auth\AuthnProvider;

class Dispatcher {

    private string $action;

    public function __construct() {
        $this->action = $_GET['action'] ?? 'default';
    }

    public function run(): void {
        switch ($this->action) {
            case 'mes-favoris':
                $action = new DisplayMesFavorisAction();
                break;
            case 'addSoiree':
                $action = new AddSoireeAction();
                break;
            case 'login':
                $action = new LogInAction();
                break;
            case 'register':
                $action = new RegisterAction();
                break;
            case 'logout':
                $action = new LogOutAction();
                break;
            case 'addSpectacle':
                $action = new AddSpectacleAction();
                break;
            case 'filterByDate':
                $action = new DisplaySpectacleByDateAction();
                break;
            case 'filterByStyle':
                $action = new DisplaySpectacleByStyleAction();
                break;
            case 'filterByLocation':
                $action = new DisplaySpectacleByLocationAction();
                break;
            case 'displaySpectacleDetail':
                $action = new DisplaySpectacleDetailAction();
                break;
            case 'like':
                $action = new LikeAction();
                break;
            case 'editSoireeList':
                $action = new EditSoireeListAction();
                break;
            case 'editSoiree':
                $action = new EditSoireeAction();
                break;
            case 'cancelSoiree':
                $action = new CancelSoireeAction();
                break;
            case 'displaySoiree':
                $action = new DisplaySoireeAction();
                break;
            case 'uncancelSoiree':
                $action = new UncancelSoireeAction();
                break;
            case 'displaySoireeDetail':
                $action = new DisplaySoireeDetailAction();
                break;            
            default:
                $action = new DefaultAction();
                break;
        }
        $res = $action->execute();
        $this->renderPage($res);
    }

    public function renderPage(string $res): void {
        $user = null;
        $isAdmin = false;
    
        if (isset($_SESSION['user'])) {
            try {
                $user = unserialize($_SESSION['user']);
                $authz = new Authz($user);
                $isAdmin = $authz->isAdmin();
            } catch (\Exception $e) {
                // Gestion des exceptions si besoin
            }
        }
    
        // Structure HTML avec Tailwind CSS
        $output = '
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>NRV RockNRoll</title>
            <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
            <link rel="stylesheet" href="style/styleNRV.css">
        </head>
        <body class="bg-gray-100 text-gray-900">
            <nav class="bg-gradient-to-r from-purple-700 to-purple-900 text-white py-4 shadow-lg">
                <div class="container mx-auto flex justify-between items-center">
                    <a href="?action=default" class="text-3xl font-extrabold hover:text-yellow-300 transition duration-300">NRV Festival</a>
                    <div class="hidden md:flex space-x-6 text-lg font-medium">
                        <a href="?action=displaySoiree" class="hover:text-yellow-300 transition duration-300">Soirées</a>
                        <a href="?action=filterByDate" class="hover:text-yellow-300 transition duration-300">Par Date</a>
                        <a href="?action=filterByStyle" class="hover:text-yellow-300 transition duration-300">Par Style</a>
                        <a href="?action=filterByLocation" class="hover:text-yellow-300 transition duration-300">Par Localisation</a>
                        ';
    
        if ($user !== null) {
            $output .= '
                        <a href="?action=mes-favoris" class="hover:text-yellow-300 transition duration-300">Mes Préférences</a>
                        <a href="?action=logout" class="hover:text-yellow-300 transition duration-300">Se Déconnecter</a>';
    
            if ($isAdmin) {
                $output .= '
                        <a href="?action=addSoiree" class="hover:text-yellow-300 transition duration-300">Ajouter Soirée</a>
                        <a href="?action=addSpectacle" class="hover:text-yellow-300 transition duration-300">Ajouter Spectacle</a>
                        <a href="?action=editSoireeList" class="hover:text-yellow-300 transition duration-300">Gérer Soirées</a>';
            }
        } else {
            $output .= '
                        <a href="?action=login" class="hover:text-yellow-300 transition duration-300">Connexion</a>
                        <a href="?action=register" class="hover:text-yellow-300 transition duration-300">Inscription</a>';
        }
    
        $output .= '
                    </div>
                </div>
            </nav>
            <main class="container mx-auto my-8 p-4 bg-white shadow-lg rounded">' . $res . '</main>
            <footer class="bg-purple-800 text-yellow-100 text-center py-3 mt-8">
                <p>&copy; 2023 NRVFestival. Tous droits réservés.</p>
            </footer>
        </body>
        </html>';
    
        echo $output;
    }
    
}
