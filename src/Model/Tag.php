<?php

namespace App\Model;

use App\Model\Model;

class Tag extends Model
{
    protected static string $tableName = 'tags';
    protected array $columns = ['nazwa'];

    protected ?string $nazwa = null;

    public function getNazwa(): ?string { return $this->nazwa; }
    public function setNazwa(?string $nazwa): self { $this->nazwa = $nazwa; return $this; }
}