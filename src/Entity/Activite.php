<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ActiviteRepository;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ActiviteRepository::class)]
#[ORM\Table(name: 'activite')]
class Activite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    private float $duree;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: RapportJournalier::class)]
    #[ORM\JoinColumn(name: 'rapport_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?RapportJournalier $rapport = null;

    #[ORM\ManyToOne(targetEntity: OrdreDefabrication::class)]
    #[ORM\JoinColumn(name: 'of_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    private ?OrdreDefabrication $ordreDeFabrication = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $type;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDuree(): float
    {
        return $this->duree;
    }

    public function setDuree(float $duree): self
    {
        $this->duree = $duree;
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

    public function getRapport(): ?RapportJournalier
    {
        return $this->rapport;
    }

    public function setRapport(?RapportJournalier $rapport): self
    {
        $this->rapport = $rapport;
        return $this;
    }

    public function getOrdreDeFabrication(): ?OrdreDefabrication
    {
        return $this->ordreDeFabrication;
    }

    public function setOrdreDeFabrication(?OrdreDefabrication $ordreDeFabrication): self
    {
        $this->ordreDeFabrication = $ordreDeFabrication;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }
}
