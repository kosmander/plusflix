<?php

namespace App\Model;

use App\Model\Model;

class Production extends Model
{
    protected static string $tableName = 'productions';
    protected array $columns = ['tytul', 'opis', 'typ', 'rok', 'plakat_url'];

    protected ?string $tytul = null;
    protected ?string $opis = null;
    protected ?string $typ = null;
    protected ?int $rok = null;
    protected ?string $plakat_url = null;
    private array $platforms = [];
    private array $categories = [];
    private array $tags = [];

    public function getTytul(): ?string { return $this->tytul; }
    public function setTytul(?string $tytul): self { $this->tytul = $tytul; return $this; }

    public function getOpis(): ?string { return $this->opis; }
    public function setOpis(?string $opis): self { $this->opis = $opis; return $this; }

    public function getTyp(): ?string { return $this->typ; }
    public function setTyp(?string $typ): self { $this->typ = $typ; return $this; }

    public function getRok(): ?int { return $this->rok; }
    public function setRok(?int $rok): self { $this->rok = $rok; return $this; }

    public function getPlakatUrl(): ?string { return $this->plakat_url; }
    public function setPlakatUrl(?string $plakat_url): self { $this->plakat_url = $plakat_url; return $this; }

    public function getPlatforms(): array { return $this->platforms; }

    public function getCategories(): array { return $this->categories; }

    public function getTags(): array { return $this->tags; }

    // Obsługa tablic asocjacyjnych
    // production_platform
    public function loadPlatforms(): array
    {
        // Przekazujemy: Klasę, tabelę łączącą, klucz źródła, klucz celu
        return $this->loadManyToMany(Platform::class, 'production_platform', 'id_produkcji', 'id_platformy');
    }
    public function addPlatform(int $platformId, ?int $season = null): void
    {
        $extra = $season ? ['dostepny_sezon' => $season] : [];
        $this->addRelation('production_platform', 'id_produkcji', 'id_platformy', $platformId, $extra);
    }
    public function removePlatform(int $platformId, ?int $season = null): void
    {
        $conditions = $season ? ['dostepny_sezon' => $season] : [];
        $this->removeRelation('production_platform', 'id_produkcji', 'id_platformy', $platformId, $conditions);
    }
    // production_category
    public function loadCategories(): array
    {
        return $this->loadManyToMany(Category::class, 'production_category', 'id_produkcji', 'id_kategorii');
    }
    public function addCategory(int $categoryId): void
    {
        $this->addRelation('production_category', 'id_produkcji', 'id_kategorii', $categoryId);
    }
    public function removeCategory(int $categoryId): void
    {
        $this->removeRelation('production_category', 'id_produkcji', 'id_kategorii', $categoryId);
    }
    // production_tag
    public function loadTags(): array
    {
        return $this->loadManyToMany(Tag::class, 'production_tag', 'id_produkcji', 'id_tagu');
    }
    public function addTag(int $tagId): void
    {
        $this->addRelation('production_tag', 'id_produkcji', 'id_tagu', $tagId);
    }
    public function removeTag(int $tagId): void
    {
        $this->removeRelation('production_tag', 'id_produkcji', 'id_tagu', $tagId);
    }
}