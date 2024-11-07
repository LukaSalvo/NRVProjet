<?php



namespace iutnc\nrv\repository;

use iutnc\deefy\repository\DeefyRepository;

class NRVRepository{

    private PDO $pdo;
    private static ?NRVRepository $instance = null;
    private static array $config = [];

    private function __construct(array $conf) {
        $this->pdo = new PDO($conf['dsn'], $conf['user'], $conf['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }

    public function getUserByEmail(string $email): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM user WHERE adresseMailUtilisateur = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    
    public function createUser(string $email, string $username, string $hashedPassword, string $role = 'user'): bool {
        $stmt = $this->pdo->prepare("INSERT INTO user (adresseMailUtilisateur, nomUtilisateur, mdp, role) VALUES (:email, :username, :password, :role)");
        return $stmt->execute([
            'email' => $email,
            'username' => $username,
            'password' => $hashedPassword,
            'role' => $role
        ]);
    }
    
}
