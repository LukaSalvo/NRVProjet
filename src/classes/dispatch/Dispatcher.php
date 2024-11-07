<?php

namespace iutnc\nrv\dispatch;
use iutnc\nrv\action\LogInAction;
use iutnc\nrv\action\AddSoireeAction;
use iutnc\nrv\action\AddSpectacleAction;
use iutnc\nrv\action\DisplaySoireeAction;
use iutnc\nrv\action\DefaultAction;
use iutnc\nrv\action\RegisterAction;
use iutnc\nrv\action\LogoutAction;

class Dispatcher {

    private string $action;

    public function __construct() {
        $this->action = $_GET['action'] ?? 'default';
    }

    public function run(): void {
        switch ($this->action) {
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
            case 'displaySoiree':
                $action = new DisplaySoireeAction();
                break;
            case 'addSpectacle':
                $action = new AddSpectacleActiob();
            default:
                $action = new DefaultAction();
                break;
        }
        $res = $action->execute();
        $this->renderPage($res);
    }

    public function renderPage(string $res): void
    {
        {
            $user = null;

            if (isset($_SESSION['user'])) {
                try {
                    $user = unserialize($_SESSION['user']);
                } catch (Exception $e) {
                }
            }

            $output = '
            <html>
            <head>
                <title>Deefy App</title>
                <link rel="stylesheet" href="src/style/style.css"> 
            </head>
            <body>
                <nav>
                    <a href="?action=default">Accueil</a>
                    <a href="?action=soiree">Soiree</a>';

            if ($user !== null) {
                $output .= '<a href =?action=logout>Se Deconnecter</a>
                           <a href =?action=playlist>Mon espace</a>';
            } else {
                $output .= '
             <a href = "?action=login">Connexion</a>
             <a href="?action=register">Inscription</a>
          
            ';
            }
            $output .= '
                </nav>
                <main>' . $res . '</main>
            </body>
            </html>';

            echo $output;
        }
    }
}
