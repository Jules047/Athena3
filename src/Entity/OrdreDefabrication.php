<?php

namespace App\Entity;

use App\Repository\OrdreDefabricationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: OrdreDefabricationRepository::class)]
#[ORM\Table(name: 'ordredefabrication')]
class OrdreDefabrication
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['default'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['default'])]
    private string $nom;

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups(['default'])]
    private \DateTime $dateCreation;

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP', 'onUpdate' => 'CURRENT_TIMESTAMP'])]
    #[Groups(['default'])]
    private \DateTime $dateModification;

    #[ORM\Column(type: 'string', length: 20)]
    #[Groups(['default'])]
    private string $statut;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['default'])]
    private string $client;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['default'])]
    private ?string $description = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, options: ['default' => 0])]
    #[Groups(['default'])]
    private float $dureeEstimee;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'createur_id', referencedColumnName: 'id')]
    #[Groups(['default'])]
    private ?Utilisateur $createur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getDateCreation(): \DateTime
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTime $dateCreation): self
    {
        $this->dateCreation = $dateCreation;
        return $this;
    }

    public function getDateModification(): \DateTime
    {
        return $this->dateModification;
    }

    public function setDateModification(\DateTime $dateModification): self
    {
        $this->dateModification = $dateModification;
        return $this;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    public function getClient(): string
    {
        return $this->client;
    }

    public function setClient(string $client): self
    {
        $this->client = $client;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getDureeEstimee(): float
    {
        return $this->dureeEstimee;
    }

    public function setDureeEstimee(float $dureeEstimee): self
    {
        $this->dureeEstimee = $dureeEstimee;
        return $this;
    }

    public function getCreateur(): ?Utilisateur
    {
        return $this->createur;
    }

    public function setCreateur(?Utilisateur $createur): self
    {
        $this->createur = $createur;
        return $this;
    }
}
