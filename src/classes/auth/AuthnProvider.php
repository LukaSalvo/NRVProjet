<?php

namespace iutnc\nrv\auth;

use iutnc\nrv\repository\NRVRepository;
use iutnc\nrv\exception\AuthnException;
use iutnc\nrv\User\User;

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
    public static function signin(string $email, string $password): int {
        $repo = NRVRepository::getInstance();
        try {
            $user = $repo->getUserByEmail($email);

            if (!$user) {
                throw new AuthnException("Aucun compte trouvé pour cet email.");
            }

            if (!password_verify($password, $user['mdp'])) {
                throw new AuthnException("Mot de passe incorrect.");
            }

            $userType = new User($user['id_user'], $user['adresseMailUtilisateur'], $user['nomUtilisateur'], $user['mdp'], $user['role']);
            $_SESSION['user'] = serialize($userType);

            return $user['id_user'];
        } catch (PDOException $e) {
            throw new AuthnException("Erreur de base de données : " . $e->getMessage());
        }
    }

    public static function register(string $email, string $mdp): void {
        $taille_mini = 8;
        NRVRepository::setConfig(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "db.config.ini");
        $pdo = NRVRepository::getInstance();

        // Vérifie si un utilisateur existe déjà avec cet email
        $stmt = $pdo->getPDO()->prepare("SELECT * FROM user WHERE adresseMailUtilisateur = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            throw new AuthnException("Un utilisateur avec cet email existe déjà.");
        }

        // Vérifie la sécurité du mot de passe
        if (self::checkPasswordStrength($mdp, $taille_mini)) {
            $hashedMdp = password_hash($mdp, PASSWORD_DEFAULT);
            $stmt = $pdo->getPDO()->prepare("INSERT INTO user(adresseMailUtilisateur, mdp) VALUES(?, ?);");
            $stmt->execute([$email, $hashedMdp]);
        } else {
            throw new AuthnException("Le mot de passe n'est pas assez sécurisé.");
        }
    }

    public static function checkPasswordStrength(string $pass, int $taille): bool {
        $length = strlen($pass) >= $taille; // longueur minimale
        $digit = preg_match("#\d#", $pass); // au moins un chiffre
        $special = preg_match("#\W#", $pass); // au moins un caractère spécial
        $lower = preg_match("#[a-z]#", $pass); // au moins une minuscule
        $upper = preg_match("#[A-Z]#", $pass); // au moins une majuscule

        // On retourne `true` si toutes les conditions sont remplies
        return $length && $digit && $special && $lower && $upper;
    }

    public static function getSignedInUser(): User {
        $user = unserialize($_SESSION['user']);
        if ($user) {
            return $user;
        } else {
            throw new \Exception("Utilisateur inconnu");
        }
    }
}