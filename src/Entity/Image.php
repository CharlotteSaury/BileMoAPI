<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Swagger\Annotations as SWG;
use App\Repository\ImageRepository;
use JMS\Serializer\Annotation\Since;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\ExclusionPolicy;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 * @ExclusionPolicy("all")
 */
class Image
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
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\Url
     *
     * @Expose
     * @Groups({"product", "products_list"})
     * 
     * @SWG\Property(description="Phone picture url")
     *
     * @Since("1.0")
     * 
     * @var String
     */
    private $url;

    /**
     * @ORM\ManyToOne(targetEntity=Configuration::class, inversedBy="images")
     * @ORM\JoinColumn(nullable=false)
     * 
     * @SWG\Property(description="Related configuration")
     *
     * @Since("1.0")
     * 
     * @var Configuration
     */
    private $configuration;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getConfiguration(): ?Configuration
    {
        return $this->configuration;
    }

    public function setConfiguration(?Configuration $configuration): self
    {
        $this->configuration = $configuration;

        return $this;
    }
}
