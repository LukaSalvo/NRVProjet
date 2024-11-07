<?php

namespace iutnc\nrv\dispatch;

use iutnc\nrv\action\AddSoireeAction;
use iutnc\nrv\action\DefaultAction;

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
            default:
                $action = new DefaultAction();
                break;
        }
        $res = $action->execute();
        $this->renderPage($res);
    }

    public function renderPage(string $res): void {
        $output = '
            <html>
            <head>
                <title>NRV</title>
            </head>
            <body>
                <nav>
                    <a href="?action=default">Accueil</a>';
        $output .= '
                </nav>
                <main>'.$res.'</main>
            </body>
            </html>';

        echo $output;
    }
}