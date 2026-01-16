<?php

namespace App\Model;

use App\Model\Model;

class Category extends Model
{
    protected static string $tableName = 'categories';
    protected array $columns = ['nazwa'];

    protected ?string $nazwa = null;

    public function getNazwa(): ?string { return $this->nazwa; }
    public function setNazwa(?string $nazwa): self { $this->nazwa = $nazwa; return $this; }
}