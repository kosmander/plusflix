<?php
namespace App\Model;

use App\Service\Config;
use PDO;

class Administrator
{
    public ?int $id = null;
    public ?string $login = null;
    public ?string $password = null;
    public ?string $email = null;

    // polaczenie z baza
    private static function getConnection(): PDO
    {
        $dsn = Config::get('db_dsn');
        $user = Config::get('db_user');
        $pass = Config::get('db_pass');

        $pdo = new PDO($dsn, $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }

    // szukanie admina po loginie
    public static function findByLogin(string $login): ?self
    {
        $pdo = self::getConnection();
        $sql = "SELECT * FROM administrators WHERE login = :login";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['login' => $login]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        $admin = new self();
        $admin->id = $data['id'];
        $admin->login = $data['login'];
        $admin->password = $data['password'];
        $admin->email = $data['email'];

        return $admin;
    }

    // sprawdzanie hasla
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    // szukanie po id
    public static function find(int $id): ?self
    {
        $pdo = self::getConnection();
        $sql = "SELECT * FROM administrators WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        $admin = new self();
        $admin->id = $data['id'];
        $admin->login = $data['login'];
        $admin->password = $data['password'];
        $admin->email = $data['email'];

        return $admin;
    }
}
