<?php

namespace App\Entity;

use App\Repository\ParametreFacturationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParametreFacturationRepository::class)]
#[ORM\Table(name: 'parametrefacturation')]
class ParametreFacturation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 20, unique: true)]
    private string $type_activite;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $taux_horaire;

    #[ORM\ManyToOne(targetEntity: CategorieActivite::class)]
    #[ORM\JoinColumn(name: 'categorie_id', referencedColumnName: 'id')]
    private ?CategorieActivite $categorie = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeActivite(): string
    {
        return $this->type_activite;
    }

    public function setTypeActivite(string $type_activite): self
    {
        $this->type_activite = $type_activite;
        return $this;
    }

    public function getTauxHoraire(): float
    {
        return $this->taux_horaire;
    }

    public function setTauxHoraire(float $taux_horaire): self
    {
        $this->taux_horaire = $taux_horaire;
        return $this;
    }

    public function getCategorie(): ?CategorieActivite
    {
        return $this->categorie;
    }

    public function setCategorie(?CategorieActivite $categorie): self
    {
        $this->categorie = $categorie;
        return $this;
    }
}
