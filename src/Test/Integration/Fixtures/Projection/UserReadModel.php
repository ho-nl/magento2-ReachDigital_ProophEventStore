<?php
declare(strict_types=1);


namespace ReachDigital\ProophEventStore\Test\Integration\Fixtures\Projection;


use Prooph\EventStore\Projection\AbstractReadModel;
use ReachDigital\ProophEventStore\Infrastructure\Pdo;

class UserReadModel extends AbstractReadModel
{
    public const TABLE = 'user_read';
    
    private $connection;

    public function __construct(Pdo $connection)
    {
        $this->connection = $connection;
    }

    public function init(): void
    {
        $tableName = self::TABLE;
        $sql = <<<EOT
CREATE TABLE `$tableName` (
  `id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
EOT;
        $this->connection->query($sql);
    }
    public function isInitialized(): bool
    {
        $tableName = self::TABLE;
        $sql = "SHOW TABLES LIKE '$tableName';";
        $statement = $this->connection->query($sql);
        $result = $statement->fetch();
        if (false === $result) {
            return false;
        }
        return true;
    }
    public function reset(): void
    {
        $tableName = self::TABLE;
        $sql = "TRUNCATE TABLE $tableName;";
        $statement = $this->connection->query($sql);
    }
    public function delete(): void
    {
        $tableName = self::TABLE;
        $sql = "DROP TABLE $tableName;";
        $statement = $this->connection->query($sql);
    }
    protected function insert(array $data): void
    {
        $stmt = $this->connection->prepare(
            'INSERT INTO user_read(id, email, name, password) VALUES (:id , :email, :name, :password)'
        );
        $stmt->bindParam('id', $data['id']);
        $stmt->bindParam('email', $data['email']);
        $stmt->bindParam('name', $data['name']);
        $stmt->bindParam('password', $data['password']);
        $stmt->execute();
    }
    protected function changeEmail(array $data): void
    {
        $stmt = $this->connection->prepare('UPDATE user_read SET email = :email WHERE id = :id');
        $stmt->bindValue('id', $data['id']);
        $stmt->bindValue('email', $data['email']);
        $stmt->execute();
    }
}
