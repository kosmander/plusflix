<?php

namespace App\Model;

use App\Model\Model;

class Rating extends Model
{
    protected static string $tableName = 'ratings';
    protected array $columns = ['id_produkcji', 'ocena', 'tresc', 'data', 'status_moderacji'];

    protected ?int $id_produkcji = null;
    protected ?int $ocena = null;
    protected ?string $tresc = null;
    protected ?string $data = null;
    protected ?string $status_moderacji = null;

    public function getIdProdukcji(): ?int
    {
        return $this->id_produkcji;
    }

    public function setIdProdukcji(?int $id): self
    {
        $this->id_produkcji = $id;
        return $this;
    }

    public function getOcena(): ?int
    {
        return $this->ocena;
    }

    public function setOcena(?int $ocena): self
    {
        $this->ocena = $ocena;
        return $this;
    }

    public function getTresc(): ?string
    {
        return $this->tresc;
    }

    public function setTresc(?string $tresc): self
    {
        $this->tresc = $tresc;
        return $this;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

    public function setData(?string $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function getStatusModeracji(): ?string
    {
        return $this->status_moderacji;
    }

    public function setStatusModeracji(?string $status): self
    {
        $this->status_moderacji = $status;
        return $this;
    }
}