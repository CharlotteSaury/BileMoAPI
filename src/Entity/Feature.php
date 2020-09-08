<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\FeatureRepository;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Annotation\ExclusionPolicy;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=FeatureRepository::class)
 * @ExclusionPolicy("all")
 */
class Feature
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Expose
     * @Groups({"product"})
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity=Product::class, mappedBy="features")
     */
    private $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
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

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->addFeature($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            $product->removeFeature($this);
        }

        return $this;
    }
}
