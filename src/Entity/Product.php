<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProductRepository;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Annotation\ExclusionPolicy;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
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
     * @Expose
     * @Groups({"product"})
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     * @Expose
     * @Groups({"product"})
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     * @Expose
     * @Groups({"product"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @Expose
     * @Groups({"product"})
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=255)
     * @Expose
     * @Groups({"product"})
     */
    private $dimensions;

    /**
     * @ORM\Column(type="string", length=255)
     * @Expose
     * @Groups({"product"})
     */
    private $screen;

    /**
     * @ORM\Column(type="float")
     * @Expose
     * @Groups({"product"})
     */
    private $das;

    /**
     * @ORM\Column(type="float")
     * @Expose
     * @Groups({"product"})
     */
    private $weight;

    /**
     * @ORM\ManyToMany(targetEntity=Feature::class, inversedBy="products")
     * @Expose
     * @Groups({"product"})
     */
    private $features;

    /**
     * @ORM\ManyToOne(targetEntity=Manufacturer::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     * @Expose
     * @Groups({"product"})
     */
    private $manufacturer;

    /**
     * @ORM\OneToMany(targetEntity=Configuration::class, mappedBy="product", orphanRemoval=true, cascade={"persist"})
     * @Expose
     * @Groups({"product"})
     */
    private $configurations;

    public function __construct()
    {
        $this->features = new ArrayCollection();
        $this->configurations = new ArrayCollection();
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

    public function getDimensions(): ?string
    {
        return $this->dimensions;
    }

    public function setDimensions(string $dimensions): self
    {
        $this->dimensions = $dimensions;

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

    /**
     * @return Collection|Feature[]
     */
    public function getFeatures(): Collection
    {
        return $this->features;
    }

    public function addFeature(Feature $feature): self
    {
        if (!$this->features->contains($feature)) {
            $this->features[] = $feature;
        }

        return $this;
    }

    public function removeFeature(Feature $feature): self
    {
        if ($this->features->contains($feature)) {
            $this->features->removeElement($feature);
        }

        return $this;
    }

    public function getManufacturer(): ?Manufacturer
    {
        return $this->manufacturer;
    }

    public function setManufacturer(?Manufacturer $manufacturer): self
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }

    /**
     * @return Collection|Configuration[]
     */
    public function getConfigurations(): Collection
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
}
