<?php


namespace iutnc\nrv\action;

class DefaultAction extends Action{

    public function execute(): string {
        return '
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
            
            </div>
            
            <pre>

            </pre>
            
        </body>
        </html>';
    }


}