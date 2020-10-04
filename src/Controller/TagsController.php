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

namespace App\Controller;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use App\Filter\ContainsFilterTrait;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TagsController
{
    use ContainsFilterTrait;

    /**
     * @param Request        $request
     * @param PostRepository $postRepository
     *
     * @return JsonResponse
     * @Route(name="api_tags_stats", path="/api/posts/tags", methods={"GET"})
     */
    public function tagStats(Request $request, PostRepository $postRepository): JsonResponse
    {
        $params = $request->query->get('tags', [
            'all'  => $request->query->get('all', []),
            'some' => $request->query->get('some', []),
            'none' => $request->query->get('none', []),
            'not'  => $request->query->get('not', []),
        ]);

        $qb = $postRepository->createQueryBuilder('o')
                             ->select('JSONB_ARRAY_ELEMENTS_TEXT(o.tags) title', 'count(o.id) amount')
                             ->groupBy('title')
        ;
        $this->buildContainsQueries($qb, $params, new QueryNameGenerator(), 'tags');
        $tags = $qb->getQuery()->getArrayResult();

        return new JsonResponse($tags);
    }

    /**
     * @param string         $id
     * @param PostRepository $postRepository
     *
     * @return JsonResponse
     * @Route(name="api_tags_stats_for_post", path="/api/posts/{id}/tags", methods={"GET"})
     */
    public function tagStatsForPost(string $id, PostRepository $postRepository): JsonResponse
    {
        $tags = [];
        $post = $postRepository->find($id);
        if (null !== $post) {
            $qb = $postRepository->createQueryBuilder('o')
                                 ->select('JSONB_ARRAY_ELEMENTS_TEXT(o.tags) title', 'count(o.id) amount')
                                 ->groupBy('title')
            ;
            $tags = $qb->getQuery()->getArrayResult();
            $tags = array_values(array_filter($tags, static function($row) use ($post) {
                return in_array($row['title'], $post->tags, true);
            }));
        }
        return new JsonResponse($tags);
    }
}
