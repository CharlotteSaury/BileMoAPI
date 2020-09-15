<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Since;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use App\Repository\CustomerRepository;
use JMS\Serializer\Annotation\ExclusionPolicy;
use Hateoas\Configuration\Annotation as Hateoas;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=CustomerRepository::class)
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
 *      "client",
 *      href = @Hateoas\Route(
 *          "app_clients_show",
 *          parameters={"id"="expr(object.getClient().getId())"},
 *          absolute = true
 *      ),
 *      exclusion = @Hateoas\Exclusion(
 *          groups = {"customer", "customers_list"},
 *          excludeIf = "expr(not is_granted('ROLE_ADMIN'))"
 *      )
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
 *      "client",
 *      embedded = @Hateoas\Embedded("expr(object.getClient())"),
 *      exclusion = @Hateoas\Exclusion(
 *          groups = {"customer", "customers_list"},
 *          excludeIf = "expr(not is_granted('ROLE_ADMIN'))"
 *      )
 * )
 */
class Customer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Expose
     * @Groups({"customer", "customers_list", "client"})
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
     * @Since("1.0")
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
     * @Since("1.0")
     */
    private $firstName;

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
     * @Since("1.0")
     */
    private $lastName;

    /**
     * @ORM\Column(type="datetime")
     * @Expose
     * @Groups({"customer", "customers_list", "client"})
     * 
     * @Since("1.0")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="customers", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     * @Expose
     * @Groups({"customer", "customers_list"})
     * 
     * @Since("1.0")
     */
    private $client;

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

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

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

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }
}
