<?php

namespace iutnc\nrv\auth;

use iutnc\nrv\exception\AuthnException;
use iutnc\nrv\repository\NRVRepository;
use iutnc\nrv\User\User;

class Authz {

    private User $authenticated_user;

    public function __construct(?User $user = null) {
        // Récupère l'utilisateur de session s'il n'est pas fourni
        if ($user === null) {
            $this->authenticated_user = AuthnProvider::getSignedInUser();
        } else {
            $this->authenticated_user = $user;
        }
    }

    /**
     * Vérifie si l'utilisateur connecté a un rôle spécifique
     * @param int $requiredRole
     * @return bool
     * @throws AuthnException
     */
    public function hasRole(int $requiredRole): bool {
        $repo = NRVRepository::getInstance();
        $userId = $this->authenticated_user->getId();

        try {
            $stmt = $repo->getPDO()->prepare("SELECT role FROM user WHERE id_user = :user_id");
            $stmt->execute(['user_id' => $userId]);
            $user = $stmt->fetch(); // permet d'obtenir la colonne role de la table user

            if ($user && $user['role'] >= $requiredRole) {
                return true;
            }

            return false;
        } catch (\PDOException $e) {
            throw new AuthnException("Erreur lors de la vérification du rôle de l'utilisateur.");
        }
    }

    /**
     * Vérifie si l'utilisateur connecté est administrateur
     * @return bool
     * @throws AuthnException
     */
    public function isAdmin(): bool {
        return $this->hasRole(100); // Supposons que le rôle d'administrateur est 100
    }
}
