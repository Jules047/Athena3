<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\Table(name: 'utilisateur')]
#[ORM\Index(name: 'idx_utilisateur_role', columns: ['role_id'])]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['default'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 50)]
    #[Groups(['default'])]
    private string $nom;

    #[ORM\Column(type: 'string', length: 50)]
    #[Groups(['default'])]
    private string $prenom;

    #[ORM\Column(type: 'string', length: 100, unique: true)]
    #[Groups(['default'])]
    private string $email;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['default'])]
    private string $mot_de_passe;

    #[ORM\ManyToOne(targetEntity: Role::class, inversedBy: 'utilisateurs')]
    #[ORM\JoinColumn(name: 'role_id', referencedColumnName: 'id')]
    #[Groups(['default'])]
    private ?Role $role = null;

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups(['default'])]
    private \DateTimeInterface $date_creation;

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups(['default'])]
    private \DateTimeInterface $date_modification;

    public function __construct()
    {
        $this->date_creation = new \DateTime();
        $this->date_modification = new \DateTime();
    }

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

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getMotDePasse(): string
    {
        return $this->mot_de_passe;
    }

    public function setMotDePasse(string $mot_de_passe): self
    {
        $this->mot_de_passe = $mot_de_passe;
        return $this;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): self
    {
        $this->role = $role;
        return $this;
    }

    public function getDateCreation(): \DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;
        return $this;
    }

    public function getDateModification(): \DateTimeInterface
    {
        return $this->date_modification;
    }

    public function setDateModification(\DateTimeInterface $date_modification): self
    {
        $this->date_modification = $date_modification;
        return $this;
    }

    // Méthodes requises par UserInterface
    public function getRoles(): array
    {
        return $this->role ? [$this->role->getNom()] : ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
        // Si vous stockez des données temporaires sensibles sur l'utilisateur, effacez-les ici
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    // Méthode requise par PasswordAuthenticatedUserInterface
    public function getPassword(): string
    {
        return $this->mot_de_passe;
    }

    // Méthodes utilitaires supplémentaires
    public function getNomComplet(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    public function updateDateModification(): void
    {
        $this->date_modification = new \DateTime();
    }
}
