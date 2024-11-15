<?php
namespace iutnc\nrv\action;

use iutnc\nrv\auth\Authz;
use iutnc\nrv\auth\AuthnProvider;
use iutnc\nrv\repository\NRVRepository;

class LikeAction extends Action {

    private Authz $a;
    private $pdo;

    public function __construct() {
        parent::__construct();
        $repo = NRVRepository::getInstance();
        $this->pdo = $repo->getPDO();
        try {
            $user = AuthnProvider::getSignedInUser();
            $this->a = new Authz($user);
        } catch (\Exception $e) {
            // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
            header('Location: ?action=login');
            exit();
        }
    }

    public function execute(): string {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (!isset($_SESSION['user'])) {
                return "<p>Vous devez être connecté pour liker des spectacles. <a href='?action=login'>Se connecter</a></p>";
            }
            
            if (!isset($_GET['id_spectacle'])) {
                return "<p>Erreur : aucun spectacle spécifié.</p>";
            }
            
            return $this->handleLike((int)$_GET['id_spectacle']);
        }
        return "<p>Erreur : requête non valide.</p>";
    }

    private function handleLike(int $spectacleId): string {
        try {
            $user = AuthnProvider::getSignedInUser();
        } catch (\Exception $e) {
            return "<p>Erreur : utilisateur non connecté. <a href='?action=login'>Se connecter</a></p>";
        }

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
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM user2spectacleLike WHERE id_user = :userId AND id_spectacle = :spectacleId");
        $stmt->execute([':userId' => $userId, ':spectacleId' => $spectacleId]);
        return $stmt->fetchColumn() > 0;
    }

    private function likeSpectacle(int $userId, int $spectacleId): string {
        $stmt = $this->pdo->prepare("INSERT INTO user2spectacleLike (id_user, id_spectacle) VALUES (:userId, :spectacleId)");
        $stmt->execute([':userId' => $userId, ':spectacleId' => $spectacleId]);
        return "<p>Vous avez liké ce spectacle.</p>";
    }

    private function unlikeSpectacle(int $userId, int $spectacleId): string {
        $stmt = $this->pdo->prepare("DELETE FROM user2spectacleLike WHERE id_user = :userId AND id_spectacle = :spectacleId");
        $stmt->execute([':userId' => $userId, ':spectacleId' => $spectacleId]);
        return "<p>Vous avez unliké ce spectacle.</p>";
    }
}