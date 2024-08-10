<?php

namespace App\Entity;

use App\Repository\DocumentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: DocumentRepository::class)]
#[ORM\Table(name: 'document')]
class Document
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['default'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['default'])]
    private ?string $nom = null;

    #[ORM\Column(length: 50)]
    #[Groups(['default'])]
    private ?string $type = null;

    #[ORM\Column(type: 'text')]
    #[Groups(['default'])]
    private ?string $contenu = null;

    #[ORM\ManyToOne(targetEntity: OrdreDefabrication::class)]
    #[ORM\JoinColumn(name: 'of_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[Groups(['default'])]
    private ?OrdreDefabrication $of = null;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['default'])]
    private ?\DateTimeInterface $dateUpload = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;
        return $this;
    }

    public function getOf(): ?OrdreDefabrication
    {
        return $this->of;
    }

    public function setOf(?OrdreDefabrication $of): self
    {
        $this->of = $of;
        return $this;
    }

    public function getDateUpload(): ?\DateTimeInterface
    {
        return $this->dateUpload;
    }

    public function setDateUpload(?\DateTimeInterface $dateUpload): self
    {
        $this->dateUpload = $dateUpload;
        return $this;
    }
}
