<?php

namespace iutnc\nrv\auth;

use Exception;
use iutnc\nrv\exception\AuthnException;
use iutnc\nrv\users\User;
use PDO;
use iutnc\nrv\repository\NRVRepository;
use PDOException;

class AuthnProvider {

    private NRVRepository $NRVRepository;

    public function __construct() {
        $this->NRVRepository = NRVRepository::getInstance();
    }

    /**
     * Fonction de connexion pour authentifier l'utilisateur
     * @param string $email
     * @param string $password
     * @throws AuthnException
     */
    public static function signin(string $email, string $password): void {
        $repo = NRVRepository::getInstance();
        try {
            $stmt = $repo->getPdo()->prepare("SELECT * FROM user WHERE adresseMailUtilisateur = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || !password_verify($password, $user['mdp'])) {
                throw new AuthnException("Email ou mot de passe incorrect.");
            }

            $_SESSION['user'] = [
                'id_user' => $user['id_user'],
                'adresseMailUtilisateur' => $user['adresseMailUtilisateur'],
                'nomUtilisateur' => $user['nomUtilisateur'],
                'role' => $user['role']
            ];
        } catch (PDOException $e) {
            throw new AuthnException("Erreur de base de données : " . $e->getMessage());
        }
    }

    /**
     * Fonction d'inscription pour enregistrer un nouvel utilisateur
     * @param string $email
     * @param string $username
     * @param string $password
     * @param string $role
     * @throws AuthnException
     */
    public static function signup(string $email, string $username, string $password, string $role = 'user'): void {
        $repo = NRVRepository::getInstance();
        try {
            $stmt = $repo->getPdo()->prepare("SELECT * FROM user WHERE adresseMailUtilisateur = :email");
            $stmt->execute(['email' => $email]);
            if ($stmt->fetch()) {
                throw new AuthnException("Email déjà enregistré.");
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $repo->getPdo()->prepare("INSERT INTO user (adresseMailUtilisateur, nomUtilisateur, mdp, role) 
                                              VALUES (:email, :username, :password, :role)");
            $stmt->execute([
                'email' => $email,
                'username' => $username,
                'password' => $hashedPassword,
                'role' => $role
            ]);
        } catch (PDOException $e) {
            throw new AuthnException("Erreur de base de données : " . $e->getMessage());
        }
    }

    /**
     * Fonction de déconnexion
     */
    public static function signout(): void {
        unset($_SESSION['user']);
        session_destroy();
    }
}

