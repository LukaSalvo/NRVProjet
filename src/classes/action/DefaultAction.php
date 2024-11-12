<?php


namespace iutnc\nrv\action;
use iutnc\nrv\repository\NRVRepository;

class DefaultAction extends Action{

    public function execute(): string {
        $repository = NRVRepository::getInstance();
        $soirees = array_slice($repository->getAllSoirees(), 0, 3);
        $spectacleRock = $repository->getSpectaclesByStyle('Rock', null);
        $spectacleHardRock = $repository->getSpectaclesByStyle('Hard Rock', null);
        $spectacleTechno = $repository->getSpectaclesByStyle('Techno', null);
        $html ='
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Bienvenu sur le site officiel de NRV</title>
            
        </head>
        <body>
            <div class="container">
                <h1>Le Festival qui va donner des vibrations</h1>
                <p class="introduction">
                    blablabla !
                    <br>
                    Commencez votre aventure musicale maintenant !
                    
                </p>
                <h2> Nos soirées ayant le plus de succès </h2>';
        foreach ($soirees as $soiree) {
            $html .= '<div class="soiree">
                        <br>
                        <strong>Nom de la soirée :</strong> '.htmlspecialchars($soiree['nom_soiree']).'<br>
                        <strong>Lieu :</strong> ' . htmlspecialchars($soiree['nom_lieu']) . '<br>
                        <strong>Date :</strong> ' . htmlspecialchars($soiree['date']) . '<br>
                       
                    
                      </div>';
        }


        $html .= '<h2>Nos recommandations de spéctacle</h2>';
        foreach ($spectacleRock as $spectacle) {
            $html .= '<div class="spectacle">
                        <br>
                        <strong>Nom :</strong> ' . htmlspecialchars($spectacle['nomSpec']) . '<br>
                        <strong>Style :</strong> ' . htmlspecialchars($spectacle['style']) . '<br>
                        <strong>Durée :</strong> ' . htmlspecialchars($spectacle['duree']) . ' min <br>
                        <a href="?action=displaySpectacleDetail&id_spectacle=' . $spectacle['id_spectacle'] . '">Voir plus de détails</a>
                      </div>';
        }
        foreach ($spectacleHardRock as $spectacle) {
            $html .= '<div class="spectacle">
                        <br>
                        <strong>Nom :</strong> ' . htmlspecialchars($spectacle['nomSpec']) . '<br>
                        <strong>Style :</strong> ' . htmlspecialchars($spectacle['style']) . '<br>
                        <strong>Durée :</strong> ' . htmlspecialchars($spectacle['duree']) . ' min <br>
                        <a href="?action=displaySpectacleDetail&id_spectacle=' . $spectacle['id_spectacle'] . '">Voir plus de détails</a>
                      </div>';
        }
        foreach ($spectacleTechno as $spectacle) {
            $html .= '<div class="spectacle">
                        <br>
                        <strong>Nom :</strong> ' . htmlspecialchars($spectacle['nomSpec']) . '<br>
                        <strong>Style :</strong> ' . htmlspecialchars($spectacle['style']) . '<br>
                        <strong>Durée :</strong> ' . htmlspecialchars($spectacle['duree']) . ' min <br>
                        <a href="?action=displaySpectacleDetail&id_spectacle=' . $spectacle['id_spectacle'] . '">Voir plus de détails</a>
                      </div>';
        }


        $html .= '
            </div>
        </body>
        </html>';

        return $html;
    }
    


}