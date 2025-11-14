<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:write']]
)]

#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements PasswordAuthenticatedUserInterface, UserInterface
{
    // Predefined roles
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_CASHIER = 'ROLE_CASHIER';
    const ROLE_SERVER = 'ROLE_SERVER';
    const ROLE_USER = 'ROLE_USER';

    const ROLES_HIERARCHY = [
        self::ROLE_ADMIN => [self::ROLE_CASHIER, self::ROLE_SERVER, self::ROLE_USER],
        self::ROLE_CASHIER => [self::ROLE_USER],
        self::ROLE_SERVER => [self::ROLE_USER],
        self::ROLE_USER => []
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $nom = null;

    #[ORM\Column(length: 150, unique: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:write'])]
    private ?string $password = null;



    #[ORM\Column(type: 'boolean')]
    #[Groups(['user:read', 'user:write'])]
    private bool $actif = true;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Session::class)]
    #[Groups(['user:read'])]
    private Collection $sessions;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Vente::class)]
    #[Groups(['user:read'])]
    private Collection $ventes;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: CommandeTable::class)]
    #[Groups(['user:read'])]
    private Collection $commandes;

    #[ORM\Column(length: 20, nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $prenom = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[Groups(['user:read', 'user:write'])]
    private array $roles = [];

    public function __construct()
    {
        $this->sessions = new ArrayCollection();
        $this->ventes = new ArrayCollection();
        $this->commandes = new ArrayCollection();
    }

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

    public function getEmail(): ?string
    {
        return $this->email;
    }
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function isActif(): bool
    {
        return $this->actif;
    }
    public function setActif(bool $actif): self
    {
        $this->actif = $actif;
        return $this;
    }

    /** @return Collection|Session[] */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }
    public function addSession(Session $session): self
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions->add($session);
            $session->setUser($this);
        }
        return $this;
    }
    public function removeSession(Session $session): self
    {
        if ($this->sessions->removeElement($session)) {
            if ($session->getUser() === $this) {
                $session->setUser(null);
            }
        }
        return $this;
    }

    /** @return Collection|Vente[] */
    public function getVentes(): Collection
    {
        return $this->ventes;
    }
    public function addVente(Vente $vente): self
    {
        if (!$this->ventes->contains($vente)) {
            $this->ventes->add($vente);
            $vente->setUser($this);
        }
        return $this;
    }
    public function removeVente(Vente $vente): self
    {
        if ($this->ventes->removeElement($vente)) {
            if ($vente->getUser() === $this) {
                $vente->setUser(null);
            }
        }
        return $this;
    }

    /** @return Collection|CommandeTable[] */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }
    public function addCommande(CommandeTable $commande): self
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes->add($commande);
            $commande->setUser($this);
        }
        return $this;
    }
    public function removeCommande(CommandeTable $commande): self
    {
        if ($this->commandes->removeElement($commande)) {
            if ($commande->getUser() === $this) {
                $commande->setUser(null);
            }
        }
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

       /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data (e.g. plain password), clear it here.
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(string $role): bool
    {
        return in_array($role, $this->getRoles());
    }

    /**
     * Check if user has admin role
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    /**
     * Check if user has cashier role
     */
    public function isCashier(): bool
    {
        return $this->hasRole(self::ROLE_CASHIER) || $this->isAdmin();
    }

    /**
     * Check if user has server role
     */
    public function isServer(): bool
    {
        return $this->hasRole(self::ROLE_SERVER) || $this->isAdmin();
    }
}