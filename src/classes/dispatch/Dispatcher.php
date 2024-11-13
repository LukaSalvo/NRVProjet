<?php

namespace iutnc\nrv\dispatch;

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
                $action = new LogoutAction();
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
            default:
                $action = new DefaultAction();
                break;
        }
        $res = $action->execute();
        $this->renderPage($res);
    }

    public function renderPage(string $res): void
    {
        $user = null;
        $isAdmin = false;  // Définition initiale de $isAdmin

        if (isset($_SESSION['user'])) {
            try {
                $user = unserialize($_SESSION['user']);
                $authz = new Authz($user);
                $isAdmin = $authz->isAdmin();
            } catch (\Exception $e) {

            }
        }

            $output = '
            <html>
            <head>

                <title>NRV RockNRoll</title> 

                
                

            </head>
            <body>
                <nav>
                    <a href="?action=default">Accueil</a>        
                    ';

        if ($user !== null) {
            $output .= '<a href="?action=logout">Se Déconnecter</a>
                        <a href="?action=filterByLocation">Depuis une localisation</a>
                        <a href="?action=mes-favoris"> Ma liste de préférences</a>';
            if ($isAdmin) {
                $output .= '<a href="?action=addSoiree">Ajouter une Soirée</a>';
                $output .= '<a href="?action=addSpectacle">Ajouter un Spectacle</a>';
                $output .= '<a href="?action=editSoireeList">Modifier ou Annuler une Soirée</a>';
            }
            
        } else {
            $output .= '
             <a href="?action=login">Connexion</a>
             <a href="?action=register">Inscription</a>';
        }

        $output .= '
            </nav>
            <main>' . $res . '</main>
        <footer>
            <p>&copy; 2023 NRVFestival. Tous droits réservés.</p>
        </footer>
        </body>
        </html>';

        echo $output;
    }
}
