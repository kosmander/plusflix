<?php

namespace App\Model;

use App\Service\Config;
use PDO;

abstract class Model
{
    protected ?int $id = null;
    protected static string $tableName;
    protected array $columns = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    protected static function getPdo(): PDO
    {
        return new PDO(Config::get('db_dsn'), Config::get('db_user'), Config::get('db_pass'));
    }

    public static function fromArray($array): Model
    {
        $model = new static();
        $model->fill($array);
        return $model;
    }

    public function fill($array): self
    {
        if (isset($array['id']) && !$this->getId()) {
            $this->setId($array['id']);
        }
        foreach ($array as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
        return $this;
    }

    public static function findAll(): array
    {
        $pdo = self::getPdo();
        $sql = "SELECT * FROM " . static::$tableName;
        $statement = $pdo->prepare($sql);
        $statement->execute();

        $results = [];
        $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $results[] = static::fromArray($row);
        }

        return $results;
    }

    public static function find($id): ?static
    {
        $pdo = self::getPdo();
        $sql = "SELECT * FROM " . static::$tableName . " WHERE id = :id";
        $statement = $pdo->prepare($sql);
        $statement->execute(['id' => $id]);

        $array = $statement->fetch(PDO::FETCH_ASSOC);
        return $array ? static::fromArray($array) : null;
    }

    public function save(): void
    {
        $pdo = self::getPdo(); // Pobranie połączenia
        $data = [];

        foreach ($this->columns as $column) {
            $data[$column] = $this->$column;
        }

        if (!$this->getId()) {
            $columnNames = implode(', ', $this->columns);
            $placeholders = [];

            foreach ($this->columns as $column) {
                $placeholders[] = ":" . $column;
            }
            $placeholderString = implode(', ', $placeholders);

            $sql = "INSERT INTO " . static::$tableName . " ($columnNames) VALUES ($placeholderString)";
            $statement = $pdo->prepare($sql);
            $statement->execute($data);

            $this->setId((int)$pdo->lastInsertId());
        } else {
            $updateParts = [];
            foreach ($this->columns as $column) {
                $updateParts[] = "$column = :$column";
            }
            $updateString = implode(', ', $updateParts);

            $sql = "UPDATE " . static::$tableName . " SET $updateString WHERE id = :id";

            $data['id'] = $this->getId();
            $statement = $pdo->prepare($sql);
            $statement->execute($data);
        }
    }

    public function delete(): void
    {
        $pdo = self::getPdo();
        $sql = "DELETE FROM " . static::$tableName . " WHERE id = :id";
        $statement = $pdo->prepare($sql);
        $statement->execute(['id' => $this->getId()]);

        $this->setId(null);
        foreach ($this->columns as $column) {
            $this->$column = null;
        }
    }

    /**
     * @param string $targetClass Klasa docelowa (np. Category::class)
     * @param string $associativeTable Tabela łącząca (np. production_category)
     * @param string $sourceKey Klucz bieżącego modelu w tabeli łączącej (np. id_produkcji)
     * @param string $targetKey Klucz modelu docelowego w tabeli łączącej (np. id_kategorii)
     */
    protected function loadManyToMany(string $targetClass, string $associativeTable, string $sourceKey, string $targetKey): array
    {
        $pdo = self::getPdo();
        $sql = "SELECT t.* FROM " . $targetClass::$tableName . " t
                JOIN $associativeTable at ON t.id = at.$targetKey
                WHERE at.$sourceKey = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $this->getId()]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $results = [];
        foreach ($rows as $row) {
            $results[] = $targetClass::fromArray($row);
        }
        return $results;
    }

    /**
     * @param string $associativeTable Tabela łącząca
     * @param string $sourceKey Klucz bieżącego modelu (np. id_produkcji)
     * @param string $targetKey Klucz modelu docelowego (np. id_platformy)
     * @param int $targetId ID obiektu docelowego
     * @param array $extraColumns Opcjonalne dodatkowe kolumny (np. ['dostepny_sezon' => 1])
     */
    public function addRelation(string $associativeTable, string $sourceKey, string $targetKey, int $targetId, array $extraColumns = []): void
    {
        $pdo = self::getPdo();

        $columns = [$sourceKey, $targetKey];
        $placeholders = [":source_id", ":target_id"];
        $params = ['source_id' => $this->getId(), 'target_id' => $targetId];

        foreach ($extraColumns as $col => $val) {
            $columns[] = $col;
            $placeholders[] = ":$col";
            $params[$col] = $val;
        }

        $sql = "INSERT OR IGNORE INTO $associativeTable (" . implode(', ', $columns) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";

        $pdo->prepare($sql)->execute($params);
    }

    /**
     * @param string $associativeTable Tabela łącząca
     * @param string $sourceKey Klucz bieżącego modelu
     * @param string $targetKey Klucz modelu docelowego
     * @param int $targetId ID obiektu docelowego
     * @param array $conditions Dodatkowe warunki (np. dla konkretnego sezonu)
     */
    public function removeRelation(string $associativeTable, string $sourceKey, string $targetKey, int $targetId, array $conditions = []): void
    {
        $pdo = self::getPdo();
        $sql = "DELETE FROM $associativeTable WHERE $sourceKey = :source_id AND $targetKey = :target_id";
        $params = ['source_id' => $this->getId(), 'target_id' => $targetId];

        foreach ($conditions as $col => $val) {
            $sql .= " AND $col = :$col";
            $params[$col] = $val;
        }

        $pdo->prepare($sql)->execute($params);
    }
}