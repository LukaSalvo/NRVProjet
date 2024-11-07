<?php

namespace iutnc\nrv\action;

use iutnc\nrv\repository\NRVRepository;

class AddSpectacleAction extends Action {

    public function execute(): string {
        $repo = NRVRepository::getInstance();
        $spectacles = $repo->getAllSpectacles();

        // Vérifie si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['spectacle_id'], $_POST['soiree_id'])) {
            try {
                $spectacleId = (int)$_POST['spectacle_id'];
                $soireeId = (int)$_POST['soiree_id'];
                $repo->addSpectacleToSoiree($spectacleId, $soireeId);
                return $this->renderSuccessMessage($soireeId);
            } catch (\Exception $e) {
                return $this->renderErrorMessage();
            }
        } else {
            return $this->renderSpectacleList($spectacles);
        }
    }

    private function renderSuccessMessage(int $soireeId): string {
        return "
        <div class='content'>
            <p class='success-msg'>Spectacle ajouté avec succès à la soirée !</p>
            <a href='?action=displaySoiree&id_soiree={$soireeId}' class='btn-primary'>Retour à la soirée</a>
        </div>";
    }

    private function renderErrorMessage(): string {
        return "
        <div class='content'>
            <p class='error-msg'>Erreur : Spectacle déjà ajouté</p>
            <a href='?action=displaySoiree' class='btn-primary'>Retour aux soirées</a>
        </div>";
    }

    private function renderSpectacleList(array $spectacles): string {
        $formHtml = "<div class='content'><h2>Ajouter un spectacle à la soirée</h2><div class='spectacle-list'>";

        foreach ($spectacles as $spectacle) {
            $formHtml .= <<<HTML
            <div class="spectacle-item">
                <div class="spectacle-info">
                    <p class="spectacle-title">Spectacle: <strong>{$spectacle['nomSpec']}</strong></p>
                    <p class="spectacle-details">Style: {$spectacle['style']} | Durée: {$spectacle['durée']} min</p>
                </div>
                <form method="post" action="?action=addSpectacle" class="add-spectacle-form">
                    <input type="hidden" name="spectacle_id" value="{$spectacle['id_spectacle']}">
                    <input type="hidden" name="soiree_id" value="{$_GET['soiree_id']}">
                    <button type="submit" class="btn-primary">Ajouter</button>
                </form>
            </div>
            HTML;
        }

        $formHtml .= "</div></div>";
        return $formHtml;
    }
}
