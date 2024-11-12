<?php

namespace iutnc\nrv\repository;

use PDO;

use iutnc\nrv\exception\InvalidPropertyNameException;

class NRVRepository {

    private PDO $pdo;
    private static ?NRVRepository $instance = null;
    private static array $config = [];

    private function __construct(array $conf) {
        $this->pdo = new PDO($conf['dsn'], $conf['user'], $conf['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }

    public static function getInstance(): ?NRVRepository {
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
        if (!isset($conf['host'], $conf['dbname'], $conf['username'], $conf['password'])) {
            throw new \Exception("Le fichier de configuration ne contient pas toutes les clés nécessaires.");
        }

        self::$config = [
            'dsn' => "mysql:host=" . $conf['host'] . ";dbname=" . $conf['dbname'],
            'user' => $conf['username'],
            'pass' => $conf['password']
        ];
    }

    public function getUserByEmail(string $email): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM user WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function createUser(string $email, string $username, string $hashedPassword, string $role = 'user'): bool {
        $stmt = $this->pdo->prepare("INSERT INTO user (email, nom_user, password, role) VALUES (:email, :username, :password, 1)");
        return $stmt->execute([
            'email' => $email,
            'username' => $username,
            'password' => $hashedPassword,
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

    public function getPDO(): PDO {
        return $this->pdo;
    }

    public function getSpectaclesByDate(string $date): array {
        $stmt = $this->pdo->prepare("SELECT id_spectacle, nomSpec, style, duree FROM spectacle JOIN soiree2spectacle ON spectacle.id_spectacle = soiree2spectacle.id_spectacle JOIN soiree ON soiree2spectacle.id_soiree = soiree.id_soiree WHERE soiree.date = :date");
        $stmt->execute(['date' => $date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSpectaclesByStyle(string $style, int $excludeSpectacleId = null): array {
        $query = "SELECT spectacle.id_spectacle, spectacle.nomSpec, style.nom_style, spectacle.duree 
        FROM spectacle
        INNER JOIN style ON spectacle.id_style = style.id_style
        WHERE style.nom_style = :styleName";
        
        if ($excludeSpectacleId !== null) {
            $query .= " AND id_spectacle != :excludeId";
        }
    
        $stmt = $this->pdo->prepare($query);
    
        $params = ['style' => $style];
        if ($excludeSpectacleId !== null) {
            $params['excludeId'] = $excludeSpectacleId;
        }
    
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSpectaclesByLocation(string $location, int $excludeSpectacleId = null): array {
        $query = "SELECT id_spectacle, nomSpec, style, duree FROM spectacle 
                  INNER JOIN soiree2spectacle ON spectacle.id_spectacle = soiree2spectacle.id_spectacle 
                  INNER JOIN soiree ON soiree2spectacle.id_soiree = soiree.id_soiree 
                  INNER JOIN lieu ON soiree.id_lieu = lieu.id_lieu 
                  WHERE lieu.nom_lieu = :location";
                  
        if ($excludeSpectacleId !== null) {
            $query .= " AND spectacle.id_spectacle != :excludeId";
        }
    
        $stmt = $this->pdo->prepare($query);
    
        $params = ['location' => $location];
        if ($excludeSpectacleId !== null) {
            $params['excludeId'] = $excludeSpectacleId;
        }
    
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSpectacleById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT spectacle.id_spectacle, spectacle.nomSpec, spectacle.style, spectacle.duree, spectacle.description, soiree.date, lieu.nom_lieu FROM spectacle JOIN soiree2spectacle ON spectacle.id_spectacle = soiree2spectacle.id_spectacle JOIN soiree ON soiree2spectacle.id_soiree = soiree.id_soiree JOIN lieu ON soiree.id_lieu = lieu.id_lieu WHERE spectacle.id_spectacle = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getArtistsBySpectacleId(int $spectacleId): array {
        $stmt = $this->pdo->prepare("SELECT artiste.nom_artiste FROM artiste JOIN spectable2artiste ON artiste.id_artiste = spectable2artiste.id_artiste WHERE spectable2artiste.id_spectacle = :spectacleId");
        $stmt->execute(['spectacleId' => $spectacleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}