<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Swagger\Annotations as SWG;
use JMS\Serializer\Annotation\Since;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Annotation\ExclusionPolicy;
use Doctrine\Common\Collections\ArrayCollection;
use Hateoas\Configuration\Annotation as Hateoas;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=CustomerRepository::class)
 * @UniqueEntity(fields={"email"}, message="This customer already exists")
 * @ExclusionPolicy("all")
 *
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "app_customers_show",
 *          parameters={"id"="expr(object.getId())"},
 *          absolute = true
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups = {"customer", "customers_list", "client"})
 * )
 * @Hateoas\Relation(
 *      "list",
 *      href = @Hateoas\Route(
 *          "app_customers_list",
 *          absolute = true
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups = "customer")
 * )
 * @Hateoas\Relation(
 *      "create",
 *      href = @Hateoas\Route(
 *          "app_customers_create",
 *          absolute = true
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups = {"customer", "customers_list"})
 * )
 * @Hateoas\Relation(
 *      "delete",
 *      href = @Hateoas\Route(
 *          "app_customers_delete",
 *          parameters={"id"="expr(object.getId())"},
 *          absolute = true
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups = {"customer", "customers_list"})
 * )
 * @Hateoas\Relation(
 *      "clients",
 *      embedded = @Hateoas\Embedded("expr(object.getClients())"),
 *      exclusion = @Hateoas\Exclusion(
 *          groups = {"customer"},
 *          excludeIf = "expr(not is_granted('ROLE_ADMIN'))"
 *      )
 * )
 */
class Customer
{
    const ATTRIBUTES = ['email', 'firstname', 'lastname'];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Expose
     * @Groups({"customer", "customers_list", "client"})
     * 
     * @var Int
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank
     * @Assert\Email
     *
     * @Expose
     * @Groups({"customer", "customers_list", "client"})
     * 
     * @SWG\Property(description="Customer email")
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
     *      min="2",
     *      max="30"
     * )
     *
     * @Expose
     * @Groups({"customer", "customers_list", "client"})
     * 
     * @SWG\Property(description="Customer first name")
     *
     * @Since("1.0")
     * 
     * @var String
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank
     * @Assert\Length(
     *      min="2",
     *      max="30"
     * )
     *
     * @Expose
     * @Groups({"customer", "customers_list", "client"})
     * 
     * @SWG\Property(description="Customer last name")
     *
     * @Since("1.0")
     * 
     * @var String
     */
    private $lastname;

    /**
     * @ORM\Column(type="datetime")
     * @Expose
     * @Groups({"customer", "customers_list", "client"})
     * 
     * @SWG\Property(description="Creation date")
     *
     * @Since("1.0")
     * 
     * @var DateTimeInterface
     */
    private $createdAt;

    /**
     * @ORM\ManyToMany(targetEntity=Client::class, mappedBy="customers")
     * @Groups({"customer", "customers_list", "client"})
     * 
     * @SWG\Property(description="Related clients")
     *
     * @Since("1.0")
     * 
     * @var ArrayCollection
     */
    private $clients;

    public function __construct()
    {
        $this->clients = new ArrayCollection();
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

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

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

    /**
     * @return Collection|Client[]
     */
    public function getClients(): Collection
    {
        return $this->clients;
    }

    public function addClient(Client $client): self
    {
        if (null == $this->clients) {
            $this->clients = new ArrayCollection();
        }
        if (!$this->clients->contains($client)) {
            $this->clients[] = $client;
            $client->addCustomer($this);
        }

        return $this;
    }

    public function removeClient(Client $client): self
    {
        if ($this->clients->contains($client)) {
            $this->clients->removeElement($client);
            $client->removeCustomer($this);
        }

        return $this;
    }
}
