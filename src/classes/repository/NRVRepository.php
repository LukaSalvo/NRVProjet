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
        // echo "Chemin du fichier de configuration : " . __DIR__ . '/../../../config/db.config.ini';
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
    
}
