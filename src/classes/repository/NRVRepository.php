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

    public function getSoireeById(int $id_soiree): ?array {
        $stmt = $this->pdo->prepare("
            SELECT s.id_soiree, s.nom_soiree, s.id_lieu, s.date, s.annuler, l.nom_lieu
            FROM soiree s
            INNER JOIN lieu l ON s.id_lieu = l.id_lieu
            WHERE s.id_soiree = :id_soiree
        ");
        $stmt->execute([':id_soiree' => $id_soiree]);
        $soiree = $stmt->fetch(PDO::FETCH_ASSOC);
        return $soiree ?: null;
    }
    
    
    
    public function getAllSoirees(): array {
        $stmt = $this->pdo->query("
            SELECT soiree.id_soiree, soiree.nom_soiree, lieu.nom_lieu, soiree.date, soiree.annuler
            FROM soiree 
            INNER JOIN lieu ON soiree.id_lieu = lieu.id_lieu
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        $stmt = $this->pdo->prepare("INSERT INTO soiree2spectacle (id_spectacle,id_soiree) VALUES (:spectacleId,:soireeId)");
        return $stmt->execute(['spectacleId' => $spectacleId,'soireeId' => $soireeId]);
    }


    public function createSpectacle($nomSpec, $id_style, $duree, $artistes, $soireeId): int {
        // Vérifie que l'ID de la soirée existe dans la table 'soiree'
        $soireeExists = $this->pdo->prepare("SELECT COUNT(*) FROM soiree WHERE id_soiree = :id_soiree");
        $soireeExists->execute([':id_soiree' => $soireeId]);
        if ($soireeExists->fetchColumn() == 0) {
            throw new \Exception("La soirée spécifiée n'existe pas.");
        }
    
        // Insérer le spectacle dans la table spectacle
        $stmt = $this->pdo->prepare("
            INSERT INTO spectacle (nomSpec, id_style, duree) 
            VALUES (:nom, :style, :duree)
        ");
        $stmt->execute([
            ':nom' => $nomSpec,
            ':style' => $id_style,
            ':duree' => $duree,
    
        ]);
        $spectacleId = $this->pdo->lastInsertId();
        $this->addSpectacleToSoiree($spectacleId,$soireeId);
    
        // Insérer les artistes dans la table de liaison spectacle2artiste
        foreach ($artistes as $artiste) {
            $artiste = trim($artiste); // Supprimer les espaces inutiles
            $id_artiste = $this->getArtisteIdByName($artiste);
            if (!$id_artiste) {
                $id_artiste = $this->createArtiste($artiste);
            }
            $stmt = $this->pdo->prepare("
                INSERT INTO spectacle2artiste (id_spectacle, id_artiste) 
                VALUES (:spectacle_id, :artiste_id)
            ");
            $stmt->execute([
                ':spectacle_id' => $spectacleId,
                ':artiste_id' => $id_artiste
            ]);
        }
    
        return $spectacleId;
    }
    
    

    public function getArtisteIdByName(string $artisteName): ?int {
        $stmt = $this->pdo->prepare("SELECT id_artiste FROM artiste WHERE nom_artiste = :artiste");
        $stmt->execute([':artiste' => $artisteName]);
        $artisteRow = $stmt->fetch(PDO::FETCH_ASSOC);
        return $artisteRow ? $artisteRow['id_artiste'] : null;
    }


    public function createArtiste(string $artisteName): int {
        $stmt = $this->pdo->prepare("INSERT INTO artiste (nom_artiste) VALUES (:artiste)");
        $stmt->execute([':artiste' => $artisteName]);
        return $this->pdo->lastInsertId();
    }




    public function getStyleIdByName(string $styleName): ?int {
        $stmt = $this->pdo->prepare("SELECT id_style FROM style WHERE nom_style = :style");
        $stmt->execute([':style' => $styleName]);
        $styleRow = $stmt->fetch(PDO::FETCH_ASSOC);
        return $styleRow ? $styleRow['id_style'] : null;
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
    
    
    public function getSpectacleById(int $spectacleId): ?array {
        $stmt = $this->pdo->prepare("
            SELECT 
                s.id_spectacle,
                s.nomSpec,
                s.duree,
                st.nom_style AS style,
                sr.date,
                l.nom_lieu
            FROM 
                spectacle s
            JOIN 
                style st ON s.id_style = st.id_style
            JOIN 
                soiree2spectacle s2s ON s.id_spectacle = s2s.id_spectacle
            JOIN 
                soiree sr ON s2s.id_soiree = sr.id_soiree
            JOIN 
                lieu l ON sr.id_lieu = l.id_lieu
            WHERE 
                s.id_spectacle = :id
        ");
        $stmt->execute([':id' => $spectacleId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }
    

    public function getArtistsBySpectacleId(int $spectacleId): array {
        $stmt = $this->pdo->prepare("
            SELECT 
                a.nom_artiste 
            FROM 
                spectacle2artiste sa
            JOIN 
                artiste a ON sa.id_artiste = a.id_artiste
            WHERE 
                sa.id_spectacle = :id
        ");
        $stmt->execute([':id' => $spectacleId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
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

    public function updateSoiree(int $id_soiree, string $nom_soiree, int $id_lieu, string $date, int $annuler): void {
        $stmt = $this->pdo->prepare("
            UPDATE soiree 
            SET nom_soiree = :nom_soiree, id_lieu = :id_lieu, date = :date, annuler = :annuler 
            WHERE id_soiree = :id_soiree
        ");
        $stmt->execute([
            ':nom_soiree' => $nom_soiree,
            ':id_lieu' => $id_lieu,
            ':date' => $date,
            ':annuler' => $annuler,
            ':id_soiree' => $id_soiree,
        ]);
    }
    
    


    public function getFavorisByUserId($userId) {
        $sql = "SELECT spectacle.nomSpec , spectacle.duree , spectacle.id_spectacle , style.nom_style
                FROM spectacle 
                INNER JOIN user2spectacleLike  ON spectacle .id_spectacle = user2spectacleLike.id_spectacle
                INNER JOIN style ON spectacle.id_style = style.id_style
                WHERE user2spectacleLike.id_user = :user_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStyles(): array {
        $stmt = $this->pdo->query("SELECT id_style, nom_style FROM style");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function cancelSoiree(int $idSoiree): bool {
        $stmt = $this->pdo->prepare("UPDATE soiree SET annuler = 0 WHERE id_soiree = :id_soiree");
        return $stmt->execute(['id_soiree' => $idSoiree]);
    }



    public function isFavori(int $userId, int $spectacleId): bool {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM favoris WHERE id_user = :userId AND id_spectacle = :spectacleId");
        $stmt->execute([':userId' => $userId, ':spectacleId' => $spectacleId]);
        return $stmt->fetchColumn() > 0;
    }


    public function updateSoireeAnnulation(int $idSoiree, int $status): bool {
        $stmt = $this->pdo->prepare("UPDATE soiree SET annuler = :status WHERE id_soiree = :id_soiree");
        return $stmt->execute(['status' => $status, 'id_soiree' => $idSoiree]);
    }
    


    public function getSoirees(): array {
        $stmt = $this->pdo->prepare("SELECT id_soiree, nom_soiree FROM soiree");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function isSpectacleLiked(int $userId, int $spectacleId): bool {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM user2spectacleLike 
            WHERE id_user = :user_id AND id_spectacle = :spectacle_id"
        );
        $stmt->execute([
            'user_id' => $userId,
            'spectacle_id' => $spectacleId
        ]);
        return $stmt->fetchColumn() > 0;
    }

    public function getSpectaclesBySoireeId(int $id_soiree): array {
        $stmt = $this->pdo->prepare("
            SELECT s.id_spectacle, s.nomSpec, st.nom_style, s.duree
            FROM spectacle s
            JOIN style st ON s.id_style = st.id_style
            JOIN soiree2spectacle ss ON ss.id_spectacle = s.id_spectacle
            WHERE ss.id_soiree = :id_soiree
        ");
        $stmt->execute([':id_soiree' => $id_soiree]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    

    public function getArtistes(): array {
        $stmt = $this->pdo->prepare("SELECT id_artiste, nom_artiste FROM artiste");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function createSoiree(string $nom, int $id_lieu, string $date): void {
        $stmt = $this->pdo->prepare("INSERT INTO soiree (nom_soiree, id_lieu, date) VALUES (:nom, :id_lieu, :date)");
        $stmt->execute([
            ':nom' => $nom,
            ':id_lieu' => $id_lieu,
            ':date' => $date
        ]);
    }
    
    
    
    public function getAllLieux(): array {
        $stmt = $this->pdo->query("SELECT id_lieu, nom_lieu FROM lieu");
        return $stmt->fetchAll();
    }
    
    public function getLieux(): array {
        $stmt = $this->pdo->query("SELECT id_lieu, nom_lieu, adresse FROM lieu");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }    
    
}
