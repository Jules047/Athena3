<?php

namespace App\Entity;

use App\Repository\CollaborateurRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CollaborateurRepository::class)]
#[ORM\Table(name: 'collaborateur')]
class Collaborateur
{
    #[ORM\Id]
    #[ORM\OneToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[Groups(['default'])]
    private ?Utilisateur $id = null;

    #[ORM\Column(type: 'string', columnDefinition: "ENUM('T', 'OS', 'M')", nullable: false)]
    #[Groups(['default'])]
    private string $qualification;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: false)]
    #[Groups(['default'])]
    private float $coutHoraire;

    #[ORM\Column(type: 'decimal', precision: 3, scale: 2, nullable: false)]
    #[ORM\Check(constraints: ["chk_coef_qualification" => "coef_qualification IN (0.8, 1.0, 1.5)"])]
    #[Groups(['default'])]
    private float $coefQualification;

    public function getId(): ?Utilisateur
    {
        return $this->id;
    }

    public function setId(?Utilisateur $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getQualification(): string
    {
        return $this->qualification;
    }

    public function setQualification(string $qualification): self
    {
        $this->qualification = $qualification;
        return $this;
    }

    public function getCoutHoraire(): float
    {
        return $this->coutHoraire;
    }

    public function setCoutHoraire(float $coutHoraire): self
    {
        $this->coutHoraire = $coutHoraire;
        return $this;
    }

    public function getCoefQualification(): float
    {
        return $this->coefQualification;
    }

    public function setCoefQualification(float $coefQualification): self
    {
        $this->coefQualification = $coefQualification;
        return $this;
    }
}
