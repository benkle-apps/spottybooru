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


use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Post;
use Imagick;
use Jenssegers\ImageHash\ImageHash;
use League\Flysystem\FilesystemInterface;
use Mimey\MimeTypes;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

final class CreatePostAction
{
    /**
     * @param Request $request
     *
     * @return Post
     */
    public function __invoke(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        FilesystemInterface $defaultStorage,
        MimeTypes $mimeTypes,
        ImageHash $imageHash
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
        //$post->checksum = sha1($body);
        $post->checksum = $imageHash->hash($file->getRealPath())->toHex();
        $basename = $post->checksum . '.' . $mimeTypes->getExtension($file->getMimeType());
        $path = array_slice(str_split($basename, 3), 0, 2);
        $path[] = $basename;
        $path = implode('/', $path);
        $stream = fopen($file->getRealPath(), 'r');
        $defaultStorage->putStream($path, $stream);
        fclose($stream);
        $post->file = $path;
        $post->mime = $file->getMimeType();
        $post->size = $file->getSize();
        $imagick = new Imagick();
        $imagick->readImageFile($defaultStorage->readStream($post->file));
        $post->width = $imagick->getImageWidth();
        $post->height = $imagick->getImageHeight();
        unset($imagick);
        return $post;
    }
}