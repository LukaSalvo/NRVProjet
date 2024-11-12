<?php
namespace iutnc\nrv\action;

use iutnc\nrv\auth\Authz;
use iutnc\nrv\repository\NRVRepository;

class LikeAction extends Action {
    private Authz $a;
    
    public function __construct() {
        parent::__construct();
        $user = isset($_SESSION['user']) ? unserialize($_SESSION['user']) : null;
        $this->a = new Authz($user);
    }

    public function execute(): string {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (!isset($_SESSION['user'])) {
                return "<p>Vous devez être connecté pour liker des spectacles.</p>";
            }
            
            if (!isset($_GET['id_spectacle'])) {
                return "<p>Erreur : aucun spectacle spécifié.</p>";
            }
            
            return $this->handleLike($_GET['id_spectacle']);
        }
        return "<p>Erreur : requête non valide.</p>";
    }

    private function handleLike(int $spectacleId): string {
        $user = $this->a->getAuthenticatedUser();
        $userId = $user->getId();

        NRVRepository::setConfig(__DIR__ . '/../../config/db.config.ini');
        $pdo = NRVRepository::getInstance();

        // Vérifie si déjà liké
        if ($this->isAlreadyLiked($userId, $spectacleId)) {
            // Unlike
            return $this->unlikeSpectacle($userId, $spectacleId);
        } else {
            // Like
            return $this->likeSpectacle($userId, $spectacleId);
        }
    }

    private function isAlreadyLiked(int $userId, int $spectacleId): bool {
        $pdo = NRVRepository::getInstance();
        $stmt = $pdo->getPDO()->prepare(
            "SELECT COUNT(*) FROM user2spectacleLike 
            WHERE id_user = ? AND id_spectacle = ?"
        );
        $stmt->execute([$userId, $spectacleId]);
        return $stmt->fetchColumn() > 0;
    }

    private function likeSpectacle(int $userId, int $spectacleId): string {
        try {
            $pdo = NRVRepository::getInstance();
            $stmt = $pdo->getPDO()->prepare(
                "INSERT INTO user2spectacleLike (id_user, id_spectacle) 
                VALUES (?, ?)"
            );
            $stmt->execute([$userId, $spectacleId]);
            return "<p>Spectacle ajouté aux favoris !</p>";
        } catch (\PDOException $e) {
            return "<p>Erreur lors de l'ajout aux favoris.</p>";
        }
    }

    private function unlikeSpectacle(int $userId, int $spectacleId): string {
        try {
            $pdo = NRVRepository::getInstance();
            $stmt = $pdo->getPDO()->prepare(
                "DELETE FROM user2spectacleLike 
                WHERE id_user = ? AND id_spectacle = ?"
            );
            $stmt->execute([$userId, $spectacleId]);
            return "<p>Spectacle retiré des favoris.</p>";
        } catch (\PDOException $e) {
            return "<p>Erreur lors du retrait des favoris.</p>";
        }
    }
}
