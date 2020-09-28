<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Since;
use Swagger\Annotations as SWG;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ClientRepository::class)
 * @UniqueEntity(fields={"email"}, message="This client already exists")
 * @ExclusionPolicy("all")
 *
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "app_clients_show",
 *          parameters={"id"="expr(object.getId())"},
 *          absolute = true
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups = {"client", "clients_list"})
 * )
 * @Hateoas\Relation(
 *      "list",
 *      href = @Hateoas\Route(
 *          "app_customers_list",
 *          absolute = true
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups = {"client"})
 * )
 * @Hateoas\Relation(
 *      "create",
 *      href = @Hateoas\Route(
 *          "app_clients_create",
 *          absolute = true
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups = {"client", "clients_list"})
 * )
 * @Hateoas\Relation(
 *      "delete",
 *      href = @Hateoas\Route(
 *          "app_clients_delete",
 *          parameters={"id"="expr(object.getId())"},
 *          absolute = true
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups = {"client", "clients_list"})
 * )
 * @Hateoas\Relation(
 *      "update",
 *      href = @Hateoas\Route(
 *          "app_clients_update",
 *          parameters={"id"="expr(object.getId())"},
 *          absolute = true
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups = {"client", "clients_list"})
 * )
 */
class Client implements UserInterface
{
    const ATTRIBUTES = ['email', 'company'];
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Expose
     * @Groups({"customer", "client", "clients_list"})
     *
     * @Since("1.0")
     * 
     * @var Int
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank
     * @Assert\Email
     * @Expose
     * @Groups({"client", "clients_list", "login"})
     *
     * @SWG\Property(description="Email address of client")
     *
     * @Since("1.0")
     * 
     * @var String
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank
     * @Assert\Length(
     *      min="6",
     *      max="30",
     *      minMessage="Password must contain at least 6 characters",
     *      maxMessage="Password should not contain more than 30 characters"
     * )
     * @Expose
     * @Groups({"login"})
     *
     * @SWG\Property(description="Password of client")
     *
     * @Since("1.0")
     * 
     * @var String
     */
    private $password;

    /**
     * @ORM\Column(type="datetime")
     * @Expose
     * @Groups({"client", "clients_list"})
     *
     * @SWG\Property(description="Client's creation date")
     *
     * @Since("1.0")
     * 
     * @var DateTime
     */
    private $createdAt;

    /**
     * @ORM\Column(type="json")
     * @Groups({"client"})
     *
     * @SWG\Property(description="Client's role")
     *
     * @Since("1.0")
     * 
     * @var ArrayCollection
     */
    private $roles;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank
     * @Assert\Length(
     *      min="2",
     *      max="50",
     *      minMessage="Company name must contain at least 2 characters",
     *      maxMessage="Company name should not contain more than 50 characters"
     * )
     *
     * @Expose
     * @Groups({"customer", "client", "clients_list"})
     *
     * @SWG\Property(description="Client's company name")
     *
     * @Since("1.0")
     * 
     * @var String
     */
    private $company;

    /**
     * @ORM\ManyToMany(targetEntity=Customer::class, inversedBy="clients")
     *
     * @Expose
     * @Groups({"client"})
     *
     * @SWG\Property(description="Client's related customers")
     *
     * @Since("1.0")
     * 
     * @var ArrayCollection
     */
    private $customers;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->customers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUsername(): ?string
    {
        return $this->email;
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getRoles(): ?array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(string $company): self
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return Collection|Customer[]
     */
    public function getCustomers(): Collection
    {
        return $this->customers;
    }

    public function addCustomer(Customer $customer): self
    {
        if (!$this->customers->contains($customer)) {
            $this->customers[] = $customer;
        }

        return $this;
    }

    public function removeCustomer(Customer $customer): self
    {
        if ($this->customers->contains($customer)) {
            $this->customers->removeElement($customer);
        }

        return $this;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     * 
     * @return void
     */
    public function eraseCredentials()
    {
    }
}
