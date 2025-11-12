<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length:100)]
    private ?string $nom = null;

    #[ORM\Column(length:150, unique:true)]
    private ?string $email = null;

    #[ORM\Column(length:255)]
    private ?string $password = null;

   

    #[ORM\Column(type: 'boolean')]
    private bool $actif = true;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Session::class)]
    private Collection $sessions;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Vente::class)]
    private Collection $ventes;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: CommandeTable::class)]
    private Collection $commandes;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $prenom = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $roles = null;

    public function __construct()
    {
        $this->sessions = new ArrayCollection();
        $this->ventes = new ArrayCollection();
        $this->commandes = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }

    public function getPassword(): ?string { return $this->password; }
    public function setPassword(string $password): self { $this->password = $password; return $this; }

    public function isActif(): bool { return $this->actif; }
    public function setActif(bool $actif): self { $this->actif = $actif; return $this; }

    /** @return Collection|Session[] */
    public function getSessions(): Collection { return $this->sessions; }
    public function addSession(Session $session): self { 
        if (!$this->sessions->contains($session)) {
            $this->sessions->add($session);
            $session->setUser($this);
        }
        return $this;
    }
    public function removeSession(Session $session): self {
        if ($this->sessions->removeElement($session)) {
            if ($session->getUser() === $this) {
                $session->setUser(null);
            }
        }
        return $this;
    }

    /** @return Collection|Vente[] */
    public function getVentes(): Collection { return $this->ventes; }
    public function addVente(Vente $vente): self {
        if (!$this->ventes->contains($vente)) {
            $this->ventes->add($vente);
            $vente->setUser($this);
        }
        return $this;
    }
    public function removeVente(Vente $vente): self {
        if ($this->ventes->removeElement($vente)) {
            if ($vente->getUser() === $this) {
                $vente->setUser(null);
            }
        }
        return $this;
    }

    /** @return Collection|CommandeTable[] */
    public function getCommandes(): Collection { return $this->commandes; }
    public function addCommande(CommandeTable $commande): self {
        if (!$this->commandes->contains($commande)) {
            $this->commandes->add($commande);
            $commande->setUser($this);
        }
        return $this;
    }
    public function removeCommande(CommandeTable $commande): self {
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

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(?array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }
}
