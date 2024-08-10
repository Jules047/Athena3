<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
#[ORM\Table(name: 'commande')]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['default'])]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups(['default'])]
    private ?\DateTimeInterface $dateCommande = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Groups(['default'])]
    private float $montantTotal;

    #[ORM\Column(type: 'string', columnDefinition: "ENUM('En attente', 'Validée', 'Annulée')", options: ['default' => 'En attente'])]
    #[Groups(['default'])]
    private string $statut;

    #[ORM\ManyToOne(targetEntity: Collaborateur::class)]
    #[ORM\JoinColumn(name: 'collaborateur_id', referencedColumnName: 'id')]
    #[Groups(['default'])]
    private ?Collaborateur $collaborateur = null;

    #[ORM\ManyToOne(targetEntity: OrdreDefabrication::class)]
    #[ORM\JoinColumn(name: 'of_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[Groups(['default'])]
    private ?OrdreDefabrication $ordreDefabrication = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCommande(): ?\DateTimeInterface
    {
        return $this->dateCommande;
    }

    public function setDateCommande(\DateTimeInterface $dateCommande): self
    {
        $this->dateCommande = $dateCommande;
        return $this;
    }

    public function getMontantTotal(): float
    {
        return $this->montantTotal;
    }

    public function setMontantTotal(float $montantTotal): self
    {
        $this->montantTotal = $montantTotal;
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

    public function getCollaborateur(): ?Collaborateur
    {
        return $this->collaborateur;
    }

    public function setCollaborateur(?Collaborateur $collaborateur): self
    {
        $this->collaborateur = $collaborateur;
        return $this;
    }

    public function getOrdreDefabrication(): ?OrdreDefabrication
    {
        return $this->ordreDefabrication;
    }

    public function setOrdreDefabrication(?OrdreDefabrication $ordreDefabrication): self
    {
        $this->ordreDefabrication = $ordreDefabrication;
        return $this;
    }
}
