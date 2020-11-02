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
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * Class PostPoolConnector
 *
 * @package App\DTO
 * @ApiResource(normalizationContext={"skip_null_values"=false})
 */
class PostPoolConnector
{
    /**
     * The pool's UUID
     * @var string
     * @ApiProperty(writable=false, openapiContext={"format"="uuid"})
     */
    public string $id;

    /**
     * The pool's title
     * @var string
     * @ApiProperty(writable=false)
     */
    public string $title;

    /**
     * UUID of the next post in the pool
     * @var string|null
     * @ApiProperty(writable=false, openapiContext={"format"="uuid"})
     */
    public ?string $next;

    /**
     * UUID of the previous post in the pool
     * @var string|null
     * @ApiProperty(writable=false, openapiContext={"format"="uuid"})
     */
    public ?string $previous;
}