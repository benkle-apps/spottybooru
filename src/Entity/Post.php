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

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\DBAL\Types\SafetyEnum;
use App\Repository\PostRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource()
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
     */
    public string $title = '';

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    public ?string $description = null;

    /**
     * @ORM\Column(type="SafetyEnum")
     * @Assert\Choice(choices=\App\DBAL\Types\SafetyEnum::CHOICES, message="Invalid safety value")
     */
    public string $safety = SafetyEnum::SAFE;

    /**
     * @ORM\Column(type="jsonb")
     * @var string[]
     * @Assert\All({
     *     @Assert\Type(type="string", message="Tags must be character strings"),
     *     @Assert\Length(min=3, allowEmptyString=false)
     * })
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
     */
    public int $width = 0;

    /**
     * @ORM\Column(type="integer")
     * @ApiProperty(writable=false)
     */
    public int $height = 0;

    /**
     * @ORM\Column(type="string", length=32)
     * @ApiProperty(writable=false)
     */
    public string $mime = '';

    /**
     * @ORM\Column(type="datetimetz")
     * @ApiProperty(writable=false)
     * @Gedmo\Timestampable(on="create")
     */
    public DateTime $created;

    /**
     * @ORM\Column(type="datetimetz")
     * @ApiProperty(writable=false)
     * @Gedmo\Timestampable(on="update")
     */
    public DateTime $updated;

    /**
     * @return UuidInterface|null
     */
    public function getId(): ?UuidInterface
    {
        return $this->id;
    }
}
