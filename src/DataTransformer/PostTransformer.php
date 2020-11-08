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
use App\DTO\PostPoolConnector;
use App\Entity\PoolPost;
use App\Entity\Post as PostEntity;
use Symfony\Component\HttpFoundation\UrlHelper;
use function array_search;

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
            $result->id = (string)$object->getId();
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
            $result->links = $object->links;
            $result->pools = $object->getPools()
                ->map(function(PoolPost $poolPost) {
                    $pool = $poolPost->pool;
                    $poolPosts = $pool->getPosts()->toArray();
                    usort($poolPosts, static function(PoolPost $a, PoolPost $b) {
                        return $a->position <=> $b->position;
                    });
                    $here = array_search($poolPost, $poolPosts, true);
                    $previous = $poolPosts[$here - 1] ?? null;
                    $next = $poolPosts[$here + 1] ?? null;
                    $connector = new PostPoolConnector();
                    $connector->id = (string)$pool->getId();
                    $connector->title = $pool->title;
                    $connector->previous = null !== $previous ? (string)$previous->post->getId() : null;
                    $connector->next = null !== $next ? (string)$next->post->getId() : null;
                    return $connector;
                })
                ->toArray();
        }
        if (PostEntity::class === $to && $object instanceof PostDTO) {
            $result = array_key_exists('object_to_populate', $context) ? $context['object_to_populate'] : new PostEntity();
            foreach (['tags', 'title', 'description', 'safety', 'links'] as $property) {
                $reflection = new \ReflectionProperty(PostDTO::class, $property);
                if ($reflection->isInitialized($object)) {
                    $result->{$property} = $object->{$property};
                }
            }
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