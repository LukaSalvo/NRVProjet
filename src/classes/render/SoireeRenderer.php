<?php
namespace iutnc\nrv\render;

use iutnc\nrv\festival\Soiree;

class SoireeRenderer implements Renderer {
    private Soiree $soiree;

    public function __construct(Soiree $soiree) {
        $this->soiree = $soiree;
    }

    public function render(): string {
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
                <main>
                    <h1>' . $this->soiree->__get('nom') . '</h1>
                    <p>Date: ' . $this->soiree->__get('date') . '</p>
                    <p>Lieu: ' . $this->soiree->__get('lieu') . '</p>
                    <h2>Spectacles</h2>
                    <ul>';
        
        foreach ($this->soiree->__get('ListeSpectacle') as $spectacle) {
            $output .= '<li><a href="?action=spectacle&name=' . urlencode($spectacle->__get('nomSpectacle')) . '">' . $spectacle->__get('nomSpectacle') . '</a></li>';
        }

        $output .= '
                    </ul>
                </main>
            </body>
            </html>';

        return $output;
    }
}