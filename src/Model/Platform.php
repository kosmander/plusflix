<?php

namespace App\Model;

use App\Model\Model;

class Platform extends Model
{
    protected static string $tableName = 'platforms';
    protected array $columns = ['nazwa', 'logo_url', 'platform_url'];

    protected ?string $nazwa = null;
    protected ?string $logo_url = null;
    protected ?string $platform_url = null;

    public function getNazwa(): ?string
    {
        return $this->nazwa;
    }

    public function setNazwa(?string $nazwa): self
    {
        $this->nazwa = $nazwa;
        return $this;
    }

    public function getLogoUrl(): ?string
    {
        return $this->logo_url;
    }

    public function setLogoUrl(?string $logo_url): self
    {
        $this->logo_url = $logo_url;
        return $this;
    }

    public function getPlatformUrl(): ?string
    {
        return $this->platform_url;
    }

    public function setPlatformUrl(?string $platform_url): self
    {
        $this->platform_url = $platform_url;
        return $this;
    }
}