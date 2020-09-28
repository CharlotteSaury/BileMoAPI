<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Swagger\Annotations as SWG;
use JMS\Serializer\Annotation\Since;
use App\Repository\ProductRepository;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\ExclusionPolicy;
use Doctrine\Common\Collections\ArrayCollection;
use Hateoas\Configuration\Annotation as Hateoas;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 * @UniqueEntity(fields={"name"}, message="This product already exists.")
 * @ExclusionPolicy("all")
 *
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "app_products_show",
 *          parameters={"id"="expr(object.getId())"},
 *          absolute = true
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups = {"product", "products_list"})
 * )
 * @Hateoas\Relation(
 *      "list",
 *      href = @Hateoas\Route(
 *          "app_products_list",
 *          absolute = true
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups = {"product"})
 * )
 * @Hateoas\Relation(
 *      "create",
 *      href = @Hateoas\Route(
 *          "app_products_create",
 *          absolute = true
 *      ),
 *      exclusion = @Hateoas\Exclusion(
 *          groups = {"product", "products_list"},
 *          excludeIf = "expr(not is_granted('ROLE_ADMIN'))"
 *      )
 * )
 * @Hateoas\Relation(
 *      "delete",
 *      href = @Hateoas\Route(
 *          "app_products_delete",
 *          parameters={"id"="expr(object.getId())"},
 *          absolute = true
 *      ),
 *      exclusion = @Hateoas\Exclusion(
 *          groups = {"product", "products_list"},
 *          excludeIf = "expr(not is_granted('ROLE_ADMIN'))"
 *      )
 * )
 */
class Product
{
    const ATTRIBUTES = ['name', 'description', 'screen', 'das', 'weight', 'length', 'width', 'height', 'wifi', 'video4k', 'bluetooth', 'camera', 'manufacturer'];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Expose
     * @Groups({"product", "products_list"})
     * 
     * @var Int
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
     * @Groups({"product", "products_list"})
     * 
     * @SWG\Property(description="Phone name")
     *
     * @Since("1.0")
     * 
     * @var String
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
     * @Groups({"product", "products_list"})
     * 
     * @SWG\Property(description="Phone description")
     *
     * @Since("1.0")
     * 
     * @var String
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
     * @Groups({"product", "products_list"})
     * 
     * @SWG\Property(description="Phone manufacturer")
     *
     * @Since("1.0")
     * 
     * @var String
     */
    private $manufacturer;

    /**
     * @ORM\Column(type="datetime")
     * @Expose
     * @Groups({"product", "products_list"})
     * 
     * @SWG\Property(description="Phone creation date in Bilemo catalog")
     *
     * @Since("1.0")
     * 
     * @var DateTime
     */
    private $createdAt;

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
     * @Groups({"product", "products_list"})
     * 
     * @SWG\Property(description="phone screen size in pouce")
     *
     * @Since("1.0")
     * 
     * @var Float
     */
    private $screen;

    /**
     * @ORM\Column(type="float")
     *
     * @Assert\NotBlank
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
     * @SWG\Property(description="Phone DAS value")
     *
     * @Since("1.0")
     * 
     * @var Float
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
     * @Groups({"product", "products_list"})
     * 
     * @SWG\Property(description="Phone weight in gram")
     *
     * @Since("1.0")
     * 
     * @var Float
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
     * @Groups({"product", "products_list"})
     * 
     * @SWG\Property(description="Phone length in millimeter")
     *
     * @Since("1.0")
     * 
     * @var Float
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
     * @Groups({"product", "products_list"})
     * 
     * @SWG\Property(description="Phone width in millimeter")
     *
     * @Since("1.0")
     * 
     * @var Float
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
     * @Groups({"product", "products_list"})
     * 
     * @SWG\Property(description="Phone height in millimeter")
     *
     * @Since("1.0")
     * 
     * @var Float
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
     * @Groups({"product", "products_list"})
     * 
     * @SWG\Property(description="Wifi feature (true/false)")
     *
     * @Since("1.0")
     * 
     * @var Bool
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
     * @Groups({"product", "products_list"})
     * 
     * @SWG\Property(description="Video4k feature (true/false)")
     *
     * @Since("1.0")
     * 
     * @var Bool
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
     * @Groups({"product", "products_list"})
     * 
     * @SWG\Property(description="Bluetooth feature (true/false)")
     *
     * @Since("1.0")
     * 
     * @var Bool
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
     * @Groups({"product", "products_list"})
     * 
     * @SWG\Property(description="Camera feature (true/false)")
     *
     * @Since("1.0")
     * 
     * @var Bool
     */
    private $camera;

    /**
     * @ORM\OneToMany(targetEntity=Configuration::class, mappedBy="product", orphanRemoval=true, cascade={"persist"})
     *
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Count(
     *      min = 1,
     *      minMessage = "You must specify at least one configuration")
     * @Assert\Valid
     *
     * @Expose
     * @Groups({"product", "products_list"})
     * 
     * @SWG\Property(description="Related configurations")
     *
     * @Since("1.0")
     * 
     * @var ArrayCollection
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

    public function getScreen(): ?float
    {
        return $this->screen;
    }

    public function setScreen(float $screen): self
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

    public function getCamera(): ?bool
    {
        return $this->camera;
    }

    public function setCamera(?bool $camera): self
    {
        $this->camera = $camera;

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
