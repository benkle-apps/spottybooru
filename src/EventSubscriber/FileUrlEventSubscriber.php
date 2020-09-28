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

namespace App\EventSubscriber;


use ApiPlatform\Core\EventListener\EventPriorities;
use ApiPlatform\Core\Util\RequestAttributesExtractor;
use App\Entity\Post;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use function is_a;

class FileUrlEventSubscriber implements EventSubscriberInterface
{
    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['onPreSerialize', EventPriorities::PRE_SERIALIZE],
        ];
    }

    public function onPreSerialize(ViewEvent $event): void
    {
        $posts = $event->getControllerResult();
        $request = $event->getRequest();

        if (
            $posts instanceof Response ||
            !$request->attributes->getBoolean('_api_respond', true) ||
            !($attributes = RequestAttributesExtractor::extractAttributes($request)) ||
            !is_a($attributes['resource_class'], Post::class, true)
        ) {
            return;
        }

        if ($posts instanceof \IteratorAggregate) {
            $posts = $posts->getIterator();
        }
        if ($posts instanceof \Traversable) {
            $posts = iterator_to_array($posts);
        }
        $posts = new ArrayCollection(is_iterable($posts) ? $posts : [$posts]);
        $posts
            ->filter(static function ($post) {
                return $post instanceof Post;
            })
            ->forAll(static function ($_, Post $post) use ($request) {
                $post->file = $request->getUriForPath('/files/' . $post->file);
                $post->thumbnail = $request->getUriForPath('/thumbnails/' . $post->thumbnail);
            })
        ;
    }
}