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
use App\DTO\Pool as PoolDTO;
use App\Entity\Pool as PoolEntity;
use App\Entity\PoolPost;
use App\Entity\Post;
use Symfony\Component\HttpFoundation\UrlHelper;

class PoolTransformer implements DataTransformerInterface
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
        if (PoolDTO::class === $to && $object instanceof PoolEntity) {
            $result = new PoolDTO();
            $result->id = $object->getId()->toString();
            $result->thumbnail = $this->urlHelper->getAbsoluteUrl('/thumbnails/' . $object->getPosts()[0]->post->thumbnail);
            $result->title = $object->title;
            $result->posts = $object
                ->getPosts()
                ->map(function(PoolPost $pp) { return $this->iriConverter->getIriFromItem($pp->post); })
                ->toArray();
            return $result;
        }
        if (PoolEntity::class === $to && $object instanceof PoolDTO) {
            $result = new PoolEntity();
            $result->title = $object->title;
            foreach ($object->posts as $i => $post) {
                /** @var Post $post */
                $post = $this->iriConverter->getItemFromIri($post);
                $pp = new PoolPost();
                $pp->post = $post;
                $pp->position = 10 * $i;
                $result->addPost($pp);
            }
            return $result;
        }
    }

    /**
     * @inheritDoc
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if (PoolDTO::class === $to && $data instanceof PoolEntity) {
            return true;
        }

        if (PoolEntity::class === $to) {
            return true;
        }

        return false;
    }
}