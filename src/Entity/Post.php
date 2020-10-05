<?php
/*
 * Copyright 2020 Benjamin Kleiner
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies
 * or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO
 * THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR
 * THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\DBAL\Types\SafetyEnum;
use App\Filter\ContainsFilter;
use App\Repository\PostRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;

/**
 * @ApiResource(
 *     output="App\DTO\Post",
 *     input="App\DTO\Post",
 *     collectionOperations={
 *         "get",
 *         "post"={
 *             "controller"=\App\Controller\CreatePostAction::class,
 *             "validation_groups"={"Default"},
 *             "deserialize"=false,
 *             "openapi_context"={
 *                 "consumes"={
 *                     "multipart/form-data",
 *                 },
 *                 "parameters"={
 *                     {
 *                         "in"="formData",
 *                         "name"="file",
 *                         "type"="string",
 *                         "format"="binary",
 *                         "description"="The file to upload",
 *                     }
 *                 },
 *             }
 *         }
 *     }
 * )
 * @ORM\Entity(repositoryClass=PostRepository::class)
 */
class Post
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     * @ORM\Column(type="uuid")
     */
    private ?UuidInterface $id = null;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @ApiFilter(SearchFilter::class, strategy=SearchFilter::STRATEGY_PARTIAL)
     */
    public string $title = '';

    /**
     * @ORM\Column(type="text", nullable=true)
     * @ApiFilter(SearchFilter::class, strategy=SearchFilter::STRATEGY_PARTIAL)
     */
    public ?string $description = null;

    /**
     * @ORM\Column(type="SafetyEnum")
     * @Assert\Choice(choices=\App\DBAL\Types\SafetyEnum::CHOICES, message="Invalid safety value")
     * @ApiFilter(SearchFilter::class, strategy=SearchFilter::STRATEGY_EXACT)
     */
    public string $safety = SafetyEnum::SAFE;

    /**
     * @ORM\Column(type="jsonb")
     * @var string[]
     * @Assert\All({
     *     @Assert\Type(type="string", message="Tags must be character strings"),
     *     @Assert\Length(min=3, allowEmptyString=false)
     * })
     * @ApiFilter(ContainsFilter::class)
     */
    public array $tags = [];

    /**
     * @ORM\Column(type="string", length=64)
     * @ApiProperty(writable=false)
     */
    public string $checksum = '';

    /**
     * @ORM\Column(type="integer")
     * @ApiProperty(writable=false)
     */
    public int $size = 0;

    /**
     * @ORM\Column(type="integer")
     * @ApiProperty(writable=false)
     * @ApiFilter(RangeFilter::class)
     */
    public int $width = 0;

    /**
     * @ORM\Column(type="integer")
     * @ApiProperty(writable=false)
     * @ApiFilter(RangeFilter::class)
     */
    public int $height = 0;

    /**
     * @ORM\Column(type="string", length=32)
     * @ApiProperty(writable=false)
     * @ApiFilter(SearchFilter::class, strategy=SearchFilter::STRATEGY_EXACT)
     */
    public string $mime = '';

    /**
     * @ORM\Column(type="datetimetz")
     * @ApiProperty(writable=false)
     * @Gedmo\Timestampable(on="create")
     * @ApiFilter(DateFilter::class)
     */
    public DateTime $created;

    /**
     * @ORM\Column(type="datetimetz")
     * @ApiProperty(writable=false)
     * @Gedmo\Timestampable(on="update")
     * @ApiFilter(DateFilter::class)
     */
    public DateTime $updated;

    /**
     * @ORM\Column(type="string")
     * @ApiProperty(writable=false)
     */
    public string $file;

    /**
     * @ORM\Column(type="string")
     * @ApiProperty(writable=false)
     */
    public string $thumbnail;

    /**
     * @ORM\OneToMany(targetEntity=PoolPost::class, mappedBy="post", orphanRemoval=true)
     * @ApiProperty(readable=true, writable=false)
     */
    private Collection $pools;

    public function __construct()
    {
        $this->pools = new ArrayCollection();
    }

    /**
     * @return UuidInterface|null
     */
    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    /**
     * @return Collection|PoolPost[]
     */
    public function getPools(): Collection
    {
        return $this->pools;
    }

    public function addPool(PoolPost $pool): self
    {
        if (!$this->pools->contains($pool)) {
            $this->pools[] = $pool;
            $pool->post = $this;
        }

        return $this;
    }

    public function removePool(PoolPost $pool): self
    {
        if ($this->pools->contains($pool)) {
            $this->pools->removeElement($pool);
            // set the owning side to null (unless already changed)
            if ($pool->post === $this) {
                $pool->post = null;
            }
        }

        return $this;
    }
}
