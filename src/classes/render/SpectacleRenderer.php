<?php
namespace iutnc\nrv\render;

use iutnc\nrv\festival\Spectacle;

class SpectacleRenderer implements Renderer {
    private Spectacle $spectacle;

    public function __construct(Spectacle $spectacle) {
        $this->spectacle = $spectacle;
    }

    public function render(): string {
        $output = '
            <html>
            <head>
                <title>NRV - ' . $this->spectacle->__get('nomSpectacle') . '</title>
            </head>
            <body>
                <nav>
                    <a href="?action=default">Accueil</a>
                    <a href="?action=soiree">Retour à la soirée</a>
                </nav>
                <main>
                    <h1>' . $this->spectacle->__get('nomSpectacle') . '</h1>
                    <div class="spectacle-details">
                        <p>Style: ' . $this->spectacle->__get('style') . '</p>
                        <p>Durée: ' . $this->spectacle->__get('duree') . ' minutes</p>
                    </div>
                </main>
            </body>
            </html>';

        return $output;
    }
}