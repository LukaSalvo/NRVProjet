<?php

namespace iutnc\nrv\repository;
use PDO;
use iutnc\deefy\repository\DeefyRepository;
use iutnc\nrv\exception\InvalidPropertyNameException;

class NRVRepository{

    private \PDO $pdo;
    private static ?NRVRepository $instance = null;
    private static array $config = [];

    private function __construct(array $conf) {
        $this->pdo = new PDO($conf['dsn'], $conf['user'], $conf['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }

    public static function getInstance():? NRVRepository {
        if (is_null(self::$instance)) {
            if (empty(self::$config)) {
                throw new \Exception("NRV Repository not configured");

            }
            self::$instance = new NRVRepository(self::$config);
        }
        return self::$instance;
    }

    public static function setConfig(string $file) {
        $conf = parse_ini_file($file);
        if ($conf === false) {
            throw new \Exception("Erreur pendant la lecture du fichier de configuration.");
        }
        if (!isset($conf['host'], $conf['dbname'], $conf['username'])) {
            throw new \Exception("Le fichier de configuration ne contient pas toutes les clés nécessaires.");
        }

        self::$config = [
            'dsn' => "mysql:host=" . $conf['host'] . ";dbname=" . $conf['dbname'] ,
            'user' => $conf['username'],
            'pass' => $conf['password']
        ];
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

    public function getUserRoleById(int $id): ?string {
        $stmt = $this->pdo->prepare("SELECT role FROM user WHERE id_user = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['role'] : null;
    }

    public function getAllSoirees(): array {
        $stmt = $this->pdo->query("SELECT soiree.id_soiree, lieu.nom_lieu, soiree.date FROM soiree JOIN lieu ON soiree.id_lieu = lieu.id_lieu");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getSoireeById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT soiree.id_soiree, lieu.nom_lieu, soiree.date FROM soiree JOIN lieu ON soiree.id_lieu = lieu.id_lieu WHERE soiree.id_soiree = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    
    public function findSpectaclesBySoireeId(int $soireeId): array {
        $stmt = $this->pdo->prepare("SELECT spectacle.nomSpec, spectacle.style, spectacle.duree FROM soiree2spectacle 
                                     JOIN spectacle ON soiree2spectacle.id_spectacle = spectacle.id_spectacle 
                                     WHERE soiree2spectacle.id_soiree = :soireeId");
        $stmt->execute(['soireeId' => $soireeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllSpectacles(): array {
        $stmt = $this->pdo->query("SELECT id_spectacle, nomSpec, style, duree FROM spectacle");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function addSpectacleToSoiree(int $spectacleId, int $soireeId): bool {
        $stmt = $this->pdo->prepare("INSERT INTO soiree2spectacle (id_soiree, id_spectacle) VALUES (:soireeId, :spectacleId)");
        return $stmt->execute(['soireeId' => $soireeId, 'spectacleId' => $spectacleId]);
    }
    
}
