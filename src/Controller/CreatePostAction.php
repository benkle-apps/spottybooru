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


use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Post;
use App\Repository\PostRepository;
use App\Service\FileService;
use Doctrine\ORM\NonUniqueResultException;
use ImagickException;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

final class CreatePostAction
{
    /**
     * @param Request               $request
     * @param SerializerInterface   $serializer
     * @param ValidatorInterface    $validator
     * @param FileService           $fileService
     * @param PostRepository        $postRepository
     * @param IriConverterInterface $iriConverter
     *
     * @return Post
     * @throws FileNotFoundException
     * @throws ImagickException
     * @throws Throwable
     * @throws NonUniqueResultException
     * @throws FileExistsException
     */
    public function __invoke(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        FileService $fileService,
        PostRepository $postRepository,
        IriConverterInterface $iriConverter
    ): Post
    {
        $body = $request->getContent();
        $format = $request->getRequestFormat(null);
        if (!$body) {
            $body = $request->request->get('post');
            $contentType = $request->headers->get('X-Post-Content-Type');
            $format = $request->getFormat($contentType);
        }
        /** @var Post $post */
        $post = $serializer->deserialize($body, Post::class, $format);
        $validator->validate($post);
        /** @var UploadedFile $file */
        $file = $request->files->get('file');
        $thumbnail = $request->files->get('thumbnail');

        $fileService->getInformationFromUpload($post, $file);

        $duplicate = $postRepository->findOneByChecksum($post->checksum);
        if (null !== $duplicate) {
            throw new HttpException(400, sprintf('File has already been uploaded: %s', $iriConverter->getIriFromItem($duplicate)));
        }

        try {
            $fileService
                ->moveUpload($post, $file)
                ->createThumbnail($post, $thumbnail);
        } catch (Throwable $e) {
            $fileService->deleteFiles($post);
            throw $e;
        }
        return $post;
    }
}