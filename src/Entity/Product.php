<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProductRepository;
use DateTime;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Annotation\ExclusionPolicy;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 * @UniqueEntity(fields={"name"}, message="This product already exists.")
 * @ExclusionPolicy("all")
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Expose
     * @Groups({"product"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     * @Assert\NotBlank
     * @Assert\Length(
     *      min="2",
     *      max="50",
     *      minMessage="Product name must contain at least 2 characters",
     *      maxMessage="Product name should not contain more than 50 characters"
     * )
     * 
     * @Expose
     * @Groups({"product"})
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     * 
     * @Assert\NotBlank
     * @Assert\Length(
     *      min="6",
     *      max="3000",
     *      minMessage="Product description must contain at least 6 characters",
     *      maxMessage="Product description should not contain more than 3000 characters"
     * )
     * 
     * @Expose
     * @Groups({"product"})
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     * @Assert\NotBlank
     * @Assert\Length(
     *      min="2",
     *      max="50",
     *      minMessage="Manufacturer name must contain at least 2 characters",
     *      maxMessage="Manufacturer name should not contain more than 50 characters"
     * )
     * 
     * @Expose
     * @Groups({"product"})
     */
    private $manufacturer;

    /**
     * @ORM\Column(type="datetime")
     * @Expose
     * @Groups({"product"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Expose
     * @Groups({"product"})
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="float", length=255)
     * 
     * @Assert\NotBlank
     * @Assert\Positive
     * @Assert\Type(
     *      type="numeric",
     *      message="This value should be a numeric value"
     *      )
     * @Assert\Type(
     *     type="float",
     *     message="This value is not a valid float number"
     * )
     * 
     * @Expose
     * @Groups({"product"})
     */
    private $screen;

    /**
     * @ORM\Column(type="float")
     * 
     * @Assert\NotBlank
     * @Assert\Positive
     * @Assert\Type(
     *      type="numeric",
     *      message="This value should be a numeric value"
     *      )
     * @Assert\Type(
     *     type="float",
     *     message="This value is not a valid float number"
     * )
     * 
     * @Expose
     * @Groups({"product"})
     */
    private $das;

    /**
     * @ORM\Column(type="float")
     * 
     * @Assert\NotBlank
     * @Assert\Positive
     * @Assert\Type(
     *      type="numeric",
     *      message="This value should be a numeric value"
     *      )
     * @Assert\Type(
     *     type="float",
     *     message="This value is not a valid float number"
     * )
     * 
     * @Expose
     * @Groups({"product"})
     */
    private $weight;

    /**
     * @ORM\Column(type="float")
     * 
     * @Assert\NotBlank
     * @Assert\Positive
     * @Assert\Type(
     *      type="numeric",
     *      message="This value should be a numeric value"
     *      )
     * @Assert\Type(
     *     type="float",
     *     message="This value is not a valid float number"
     * )
     * 
     * @Expose
     * @Groups({"product"})
     */
    private $length;

    /**
     * @ORM\Column(type="float")
     * 
     * @Assert\NotBlank
     * @Assert\Positive
     * @Assert\Type(
     *      type="numeric",
     *      message="This value should be a numeric value"
     *      )
     * @Assert\Type(
     *     type="float",
     *     message="This value is not a valid float number"
     * )
     * 
     * @Expose
     * @Groups({"product"})
     */
    private $width;

    /**
     * @ORM\Column(type="float")
     * 
     * @Assert\NotBlank
     * @Assert\Positive
     * @Assert\Type(
     *      type="numeric",
     *      message="This value should be a numeric value"
     *      )
     * @Assert\Type(
     *     type="float",
     *     message="This value is not a valid float number"
     * )
     * 
     * @Expose
     * @Groups({"product"})
     */
    private $height;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * 
     * @Assert\NotNull
     * @Assert\Type(
     *      type="bool",
     *      message="This value should be a boolean"
     * )
     * 
     * @Expose
     * @Groups({"product"})
     */
    private $wifi;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * 
     * @Assert\NotNull
     * @Assert\Type(
     *      type="bool",
     *      message="This value should be a boolean"
     * )
     * 
     * @Expose
     * @Groups({"product"})
     */
    private $video4k;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * 
     * @Assert\NotNull
     * @Assert\Type(
     *      type="bool",
     *      message="This value should be a boolean"
     * )
     * 
     * @Expose
     * @Groups({"product"})
     */
    private $bluetooth;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * 
     * @Assert\NotNull
     * @Assert\Type(
     *      type="bool",
     *      message="This value should be a boolean"
     * )
     * 
     * @Expose
     * @Groups({"product"})
     */
    private $lte4G;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * 
     * @Assert\NotNull
     * @Assert\Type(
     *      type="bool",
     *      message="This value should be a boolean"
     * )
     * 
     * @Expose
     * @Groups({"product"})
     */
    private $camera;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * 
     * @Assert\NotNull
     * @Assert\Type(
     *      type="bool",
     *      message="This value should be a boolean"
     * )
     * 
     * @Expose
     * @Groups({"product"})
     */
    private $nfc;

    /**
     * @ORM\OneToMany(targetEntity=Configuration::class, mappedBy="product", orphanRemoval=true, cascade={"persist"})
     * 
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Valid
     * 
     * @Expose
     * @Groups({"product"})
     */
    private $configurations;

    public function __construct()
    {
        $this->configurations = new ArrayCollection();
        $this->createdAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getScreen(): ?string
    {
        return $this->screen;
    }

    public function setScreen(string $screen): self
    {
        $this->screen = $screen;

        return $this;
    }

    public function getDas(): ?float
    {
        return $this->das;
    }

    public function setDas(float $das): self
    {
        $this->das = $das;

        return $this;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(float $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getLength(): ?float
    {
        return $this->length;
    }

    public function setLength(float $length): self
    {
        $this->length = $length;

        return $this;
    }

    public function getWidth(): ?float
    {
        return $this->width;
    }

    public function setWidth(float $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): ?float
    {
        return $this->height;
    }

    public function setHeight(float $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getWifi(): ?bool
    {
        return $this->wifi;
    }

    public function setWifi(?bool $wifi): self
    {
        $this->wifi = $wifi;

        return $this;
    }

    public function getVideo4k(): ?bool
    {
        return $this->video4k;
    }

    public function setVideo4k(?bool $video4k): self
    {
        $this->video4k = $video4k;

        return $this;
    }

    public function getBluetooth(): ?bool
    {
        return $this->bluetooth;
    }

    public function setBluetooth(?bool $bluetooth): self
    {
        $this->bluetooth = $bluetooth;

        return $this;
    }

    public function getLte4G(): ?bool
    {
        return $this->lte4G;
    }

    public function setLte4G(?bool $lte4G): self
    {
        $this->lte4G = $lte4G;

        return $this;
    }

    public function getCamera(): ?bool
    {
        return $this->camera;
    }

    public function setCamera(?bool $camera): self
    {
        $this->camera = $camera;

        return $this;
    }

    public function getNfc(): ?bool
    {
        return $this->nfc;
    }

    public function setNfc(?bool $nfc): self
    {
        $this->nfc = $nfc;

        return $this;
    }

    /**
     * @return ArrayCollection|Configuration[]
     */
    public function getConfigurations(): ArrayCollection
    {
        return $this->configurations;
    }

    public function addConfiguration(Configuration $configuration): self
    {
        if (!$this->configurations->contains($configuration)) {
            $this->configurations[] = $configuration;
            $configuration->setProduct($this);
        }

        return $this;
    }

    public function removeConfiguration(Configuration $configuration): self
    {
        if ($this->configurations->contains($configuration)) {
            $this->configurations->removeElement($configuration);
            // set the owning side to null (unless already changed)
            if ($configuration->getProduct() === $this) {
                $configuration->setProduct(null);
            }
        }

        return $this;
    }

    public function getManufacturer(): ?string
    {
        return $this->manufacturer;
    }

    public function setManufacturer(string $manufacturer): self
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }
}
