<?php

namespace iutnc\nrv\auth;

use iutnc\nrv\exception\AuthnException;

class Authz {

    /**
     * Vérifie si l'utilisateur connecté a un rôle spécifique
     * @param string $role
     * @throws AuthnException
     */
    public static function checkRole(string $role): void {
        if (!isset($_SESSION['user'])) {
            throw new AuthnException("Aucun utilisateur n'est authentifié.");
        }

        if ($_SESSION['user']['role'] !== $role) {
            throw new AuthnException("Accès refusé : rôle insuffisant.");
        }
    }

    /**
     * Vérifie si l'utilisateur connecté est administrateur
     * @return bool
     * @throws AuthnException
     */
    public static function isAdmin(): bool {
        if (!isset($_SESSION['user'])) {
            throw new AuthnException("Aucun utilisateur n'est authentifié.");
        }

        return $_SESSION['user']['role'] === 'admin';
    }
}
