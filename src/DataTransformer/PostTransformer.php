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

namespace App\DataTransformer;

use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\Post as PostDTO;
use App\Entity\PoolPost;
use App\Entity\Post as PostEntity;
use Symfony\Component\HttpFoundation\UrlHelper;

class PostTransformer implements DataTransformerInterface
{
    private IriConverterInterface $iriConverter;

    private UrlHelper $urlHelper;

    /**
     * PoolTransformer constructor.
     *
     * @param IriConverterInterface $iriConverter
     * @param UrlHelper             $urlHelper
     */
    public function __construct(IriConverterInterface $iriConverter, UrlHelper $urlHelper)
    {
        $this->iriConverter = $iriConverter;
        $this->urlHelper = $urlHelper;
    }

    /**
     * @inheritDoc
     */
    public function transform($object, string $to, array $context = [])
    {
        $result = null;
        if (PostDTO::class === $to && $object instanceof PostEntity) {
            $result = new PostDTO();
            $result->thumbnail = $this->urlHelper->getAbsoluteUrl('/thumbnails/' . $object->thumbnail);
            $result->file = $this->urlHelper->getAbsoluteUrl('/files/' . $object->file);
            $result->id = $object->getId()->toString();
            $result->tags = $object->tags;
            $result->checksum = $object->checksum;
            $result->title = $object->title;
            $result->mime = $object->mime;
            $result->width = $object->width;
            $result->size = $object->size;
            $result->updated = $object->updated;
            $result->created = $object->created;
            $result->description = $object->description;
            $result->height = $object->height;
            $result->safety = $object->safety;
            $result->pools = $object->getPools()
                ->map(function(PoolPost $poolPost) {
                    $pool = $poolPost->pool;
                    $poolPosts = $pool->getPosts()->toArray();
                    usort($poolPosts, static function(PoolPost $a, PoolPost $b) {
                        return $a->position <=> $b->position;
                    });
                    $here = \array_search($poolPost, $poolPosts, true);
                    $previous = $poolPosts[$here - 1] ?? null;
                    $next = $poolPosts[$here + 1] ?? null;
                    return [
                        'id' => $pool->getId(),
                        '@id' => $this->iriConverter->getIriFromItem($pool),
                        'title' => $pool->title,
                        '@previous' => $previous ? $this->iriConverter->getIriFromItem($previous->post) : null,
                        '@next' => $next ? $this->iriConverter->getIriFromItem($next->post) : null,
                        'previous' => $previous ? $previous->post->getId() : null,
                        'next' => $next ? $next->post->getId() : null,
                    ];
                })
                ->toArray();
        }
        if (PostEntity::class === $to && $object instanceof PostDTO) {
            $result = new PostEntity();
            $result->tags = $object->tags;
            $result->title = $object->title;
            $result->description = $object->description;
            $result->safety = $object->safety;
        }
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if (PostDTO::class === $to && $data instanceof PostEntity) {
            return true;
        }

        if (PostEntity::class === $to) {
            return true;
        }

        return false;
    }
}