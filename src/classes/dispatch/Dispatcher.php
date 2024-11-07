<?php


namespace iutnc\nrv\dispatch;


class Dispatcher{


    private string $action;


    public function __construct() {
        $this->action = $_GET['action'] ?? 'default';
    }

    public function run(): void
    {

        switch ($this->action) {
            case 'default':
                $action = DefaultAction();
                break;
        }
        $res = $action->execute();
        $this->renderPage($res);
    }

    public function renderPage(array $res): void{
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