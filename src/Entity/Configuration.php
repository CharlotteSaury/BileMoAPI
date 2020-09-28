<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Swagger\Annotations as SWG;
use JMS\Serializer\Annotation\Since;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use App\Repository\ConfigurationRepository;
use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Annotation\ExclusionPolicy;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ConfigurationRepository::class)
 * @ExclusionPolicy("all")
 */
class Configuration
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * 
     * @var Int
     */
    private $id;

    /**
     * @ORM\Column(type="integer", length=255)
     *
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Positive
     * @Assert\Type(
     *      type="integer",
     *      message="This value should be an integer value"
     * )
     *
     * @Expose
     * @Groups({"product", "products_list"})
     * 
     * @SWG\Property(description="Memory capacity of phone in GB")
     *
     * @Since("1.0")
     * 
     * @var Int
     */
    private $memory;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Length(
     *      min="2",
     *      max="50",
     *      minMessage="Manufacturer name must contain at least 2 characters",
     *      maxMessage="Manufacturer name should not contain more than 50 characters"
     * )
     *
     * @Expose
     * @Groups({"product", "products_list"})
     * 
     * @SWG\Property(description="Phone color")
     *
     * @Since("1.0")
     * 
     * @var String
     */
    private $color;

    /**
     * @ORM\Column(type="float")
     *
     * @Assert\NotNull
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
     * @Groups({"product", "products_list"})
     * 
     * @SWG\Property(description="Phone price in €")
     *
     * @Since("1.0")
     * 
     * @var Float
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="configurations", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Expose
     * @Groups({"product", "products_list"})
     * 
     * @SWG\Property(description="Related product")
     *
     * @Since("1.0")
     * 
     * @var Product
     */
    private $product;

    /**
     * @ORM\OneToMany(targetEntity=Image::class, mappedBy="configuration", orphanRemoval=true, cascade={"persist"})
     * @Assert\NotBlank
     * @Assert\Valid
     * @Expose
     * @Groups({"product", "products_list"})
     * 
     * @SWG\Property(description="Illustrations of phone configuration")
     *
     * @Since("1.0")
     * 
     * @var ArrayCollection
     */
    private $images;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMemory(): ?int
    {
        return $this->memory;
    }

    public function setMemory(int $memory): self
    {
        $this->memory = $memory;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setConfiguration($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getConfiguration() === $this) {
                $image->setConfiguration(null);
            }
        }

        return $this;
    }
}
