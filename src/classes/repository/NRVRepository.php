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
        $stmt = $this->pdo->query("SELECT soiree.id_soiree, soiree.nom_soiree, lieu.nom_lieu, soiree.date 
                               FROM soiree 
                               INNER JOIN lieu ON soiree.id_lieu = lieu.id_lieu");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    public function getSoireeById(int $id): ?array {
        $stmt = $this->pdo->prepare("
            SELECT soiree.id_soiree, soiree.nom_soiree, soiree.date, soiree.annule, lieu.nom_lieu, lieu.nb_place, lieu.adresse
            FROM soiree 
            JOIN lieu ON soiree.id_lieu = lieu.id_lieu 
            WHERE soiree.id_soiree = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    

    public function findSpectaclesBySoireeId(int $soireeId): array {
        $stmt = $this->pdo->prepare("
            SELECT spectacle.nomSpec, style.nom_style AS style, spectacle.duree 
            FROM soiree2spectacle
            JOIN spectacle ON soiree2spectacle.id_spectacle = spectacle.id_spectacle
            JOIN style ON spectacle.id_style = style.id_style
            WHERE soiree2spectacle.id_soiree = :soireeId
        ");
        $stmt->execute(['soireeId' => $soireeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function getAllSpectacles(): array {
        $stmt = $this->pdo->query("
            SELECT spectacle.id_spectacle, spectacle.nomSpec, style.nom_style AS style, spectacle.duree 
            FROM spectacle 
            JOIN style ON spectacle.id_style = style.id_style
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    

    public function addSpectacleToSoiree(int $spectacleId, int $soireeId): bool {
        $stmt = $this->pdo->prepare("INSERT INTO soiree2spectacle (id_soiree, id_spectacle) VALUES (:soireeId, :spectacleId)");
        return $stmt->execute(['soireeId' => $soireeId, 'spectacleId' => $spectacleId]);
    }


    public function createSpectacle($nomSpec, $style, $duree, $description, $artistes, $soireeId): int {
        $stmt = $this->pdo->prepare("INSERT INTO spectacle (nomSpec, id_style, duree, description) VALUES (:nom, :style, :duree, :description)");
        $stmt->execute([
            ':nom' => $nomSpec,
            ':style' => $style,
            ':duree' => $duree,
            ':description' => $description
        ]);
        $spectacleId = $this->pdo->lastInsertId();
    
        // Associer le spectacle à la soirée
        $this->addSpectacleToSoiree($spectacleId, $soireeId);
    
        // Insérer les artistes dans la table de liaison spectacle2artiste
        foreach ($artistes as $artiste) {
            $stmt = $this->pdo->prepare("INSERT INTO spectacle2artiste (id_spectacle, artiste) VALUES (:spectacle_id, :artiste)");
            $stmt->execute([
                ':spectacle_id' => $spectacleId,
                ':artiste' => trim($artiste) // pour supprimer les espaces inutiles
            ]);
        }
    
        return $spectacleId;
    }















    public function getPDO(): PDO {
        return $this->pdo;
    }

    public function getSpectaclesByDate(string $date, int $excludeId = null): array {
        $query = "
            SELECT spectacle.id_spectacle, spectacle.nomSpec, style.nom_style AS style, spectacle.duree 
            FROM spectacle
            JOIN style ON spectacle.id_style = style.id_style
            JOIN soiree2spectacle ON spectacle.id_spectacle = soiree2spectacle.id_spectacle
            JOIN soiree ON soiree2spectacle.id_soiree = soiree.id_soiree
            WHERE soiree.date = :date
        ";
    
        // Ajout de la condition d'exclusion si $excludeId est fourni
        if ($excludeId !== null) {
            $query .= " AND spectacle.id_spectacle != :excludeId";
        }
    
        $stmt = $this->pdo->prepare($query);
    
        // Prépare les paramètres pour l'exécution
        $params = ['date' => $date];
        if ($excludeId !== null) {
            $params['excludeId'] = $excludeId;
        }
    
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    public function getSpectaclesByLocation(string $location, int $excludeId = null): array {
        $query = "
            SELECT spectacle.id_spectacle, spectacle.nomSpec, style.nom_style AS style, spectacle.duree 
            FROM spectacle
            JOIN style ON spectacle.id_style = style.id_style
            JOIN soiree2spectacle ON spectacle.id_spectacle = soiree2spectacle.id_spectacle
            JOIN soiree ON soiree2spectacle.id_soiree = soiree.id_soiree
            JOIN lieu ON soiree.id_lieu = lieu.id_lieu
            WHERE lieu.nom_lieu = :location
        ";
    
        // Ajout de la condition d'exclusion si $excludeId est fourni
        if ($excludeId !== null) {
            $query .= " AND spectacle.id_spectacle != :excludeId";
        }
    
        $stmt = $this->pdo->prepare($query);
    
        // Exécuter la requête avec ou sans l'exclusion d'ID
        $params = ['location' => $location];
        if ($excludeId !== null) {
            $params['excludeId'] = $excludeId;
        }
    
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    public function getSpectaclesByStyle(string $style, int $excludeId = null): array {
        $query = "
            SELECT spectacle.id_spectacle, spectacle.nomSpec, style.nom_style AS style, spectacle.duree 
            FROM spectacle
            JOIN style ON spectacle.id_style = style.id_style 
            WHERE style.nom_style = :style
        ";
    
        // Ajout de la condition d'exclusion si $excludeId est fourni
        if ($excludeId !== null) {
            $query .= " AND spectacle.id_spectacle != :excludeId";
        }
    
        $stmt = $this->pdo->prepare($query);
    
        // Préparation des paramètres pour l'exécution
        $params = ['style' => $style];
        if ($excludeId !== null) {
            $params['excludeId'] = $excludeId;
        }
    
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    public function getSpectacleById(int $id): ?array {
        $stmt = $this->pdo->prepare("
            SELECT spectacle.id_spectacle, spectacle.nomSpec, style.nom_style AS style, spectacle.duree, soiree.date, lieu.nom_lieu 
            FROM spectacle 
            JOIN style ON spectacle.id_style = style.id_style
            JOIN soiree ON spectacle.id_soiree = soiree.id_soiree 
            JOIN lieu ON soiree.id_lieu = lieu.id_lieu 
            WHERE spectacle.id_spectacle = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    
    
    
    

    public function getArtistsBySpectacleId(int $spectacleId): array {
        $stmt = $this->pdo->prepare("
            SELECT artiste.nom_artiste 
            FROM artiste 
            JOIN spectacle2artiste ON artiste.id_artiste = spectacle2artiste.id_artiste 
            WHERE spectacle2artiste.id_spectacle = :spectacleId
        ");
        $stmt->execute(['spectacleId' => $spectacleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function getAllStyles(): array {
        $stmt = $this->pdo->query("SELECT nom_style FROM style");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function getAllLocations(): array {
        $stmt = $this->pdo->query("SELECT DISTINCT nom_lieu FROM lieu");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAllDates(): array {
        $stmt = $this->pdo->query("SELECT DISTINCT date FROM soiree ORDER BY date");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateSoiree(int $id, string $nom, string $date, string $lieu, int $nb_place): bool {
        $stmt = $this->pdo->prepare("UPDATE soiree SET nom_soiree = :nom, date = :date, id_lieu = :lieu, nb_place = :nb_place WHERE id_soiree = :id");
        return $stmt->execute(['id' => $id, 'nom' => $nom, 'date' => $date, 'lieu' => $lieu, 'nb_place' => $nb_place]);
    }


    public function getFavorisByUserId($userId) {
        $sql = "SELECT spectacle .nomSpec 
                FROM spectacle 
                INNER JOIN user2spectacleLike  ON spectacle .id_spectacle = user2spectacleLike.id_spectacle
                WHERE user2spectacleLike.id_user = :user_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function cancelSoiree(int $id): bool {
        $stmt = $this->pdo->prepare("UPDATE soiree SET status = 'annulee' WHERE id_soiree = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getSoirees(): array {
        $stmt = $this->pdo->prepare("SELECT id_soiree, nom_soiree FROM soiree");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}
