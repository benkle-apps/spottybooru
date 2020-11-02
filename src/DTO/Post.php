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

namespace App\DTO;

use ApiPlatform\Core\Annotation\ApiProperty;
use DateTime;

class Post
{
    /**
     * The post's UUID
     * @var string
     * @ApiProperty(openapiContext={"format"="uuid"})
     */
    public string $id;

    /**
     * The post's title
     * @var string
     */
    public string $title;

    /**
     * The post's description (e.g. artist's comment)
     * @var string|null
     */
    public ?string $description;

    /**
     * The safety
     * @var string
     * @ApiProperty(openapiContext={"enum"={"safe", "sketchy", "unsafe"}})
     */
    public string $safety;

    /**
     * Tags describing the post
     * @var string[]
     */
    public array $tags;

    /**
     * Checksum used for duplication prevention
     * @var string
     * @ApiProperty(writable=false)
     */
    public string $checksum;

    /**
     * File size
     * @var int
     * @ApiProperty(writable=false)
     */
    public int $size;

    /**
     * Width in pixel (if available)
     * @var int
     * @ApiProperty(writable=false)
     */
    public int $width;

    /**
     * Height in pixel (if available)
     * @var int
     * @ApiProperty(writable=false)
     */
    public int $height;

    /**
     * Mime type
     * @var string
     * @ApiProperty(writable=false, openapiContext={"format"="mime"})
     */
    public string $mime;

    /**
     * Date and time of the post's creation
     * @var DateTime
     * @ApiProperty(writable=false)
     */
    public DateTime $created;

    /**
     * Date and time of the post's last update
     * @var DateTime
     * @ApiProperty(writable=false)
     */
    public DateTime $updated;

    /**
     * URI to the main file
     * @var string
     * @ApiProperty(writable=false, openapiContext={"format"="uri"})
     */
    public string $file;

    /**
     * URI of the thumbnail file
     * @var string
     * @ApiProperty(writable=false, openapiContext={"format"="uri"})
     */
    public string $thumbnail;

    /**
     * List of pools, with connecting metadata
     * @var PostPoolConnector[]
     * @ApiProperty(writable=false)
     */
    public array $pools;
}