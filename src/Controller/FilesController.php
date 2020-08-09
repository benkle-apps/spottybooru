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

use League\Flysystem\FilesystemInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FilesController
 *
 * @package App\Controller
 */
class FilesController
{
    private FilesystemInterface $storageFiles;

    private FilesystemInterface $storageThumbnails;

    private FilesystemInterface $storageMime;

    private int $thumbnailSize;

    /**
     * FilesController constructor.
     *
     * @param FilesystemInterface $storageFiles
     * @param FilesystemInterface $storageThumbnails
     * @param FilesystemInterface $storageMime
     * @param int                 $thumbnailSize
     */
    public function __construct(FilesystemInterface $storageFiles, FilesystemInterface $storageThumbnails, FilesystemInterface $storageMime, int $thumbnailSize)
    {
        $this->storageFiles = $storageFiles;
        $this->storageThumbnails = $storageThumbnails;
        $this->storageMime = $storageMime;
        $this->thumbnailSize = $thumbnailSize;
    }

    /**
     * @Route("/files/{path}", name="get_file", requirements={"path"=".+"})
     * @param string $path
     *
     * @return Response
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function getFile(string $path): Response
    {
        if ($this->storageFiles->has($path)) {
            $resource = $this->storageFiles->readStream($path);
            return new StreamedResponse(
                static function () use ($resource) {
                    stream_copy_to_stream($resource, fopen('php://output', 'wb'));
                },
                Response::HTTP_OK,
                [
                    'Content-Type' => $this->storageFiles->getMimetype($path),
                ]
            );
        }
        throw new FileNotFoundException($path);
    }

    /**
     * @Route("/thumbnails/{path}", name="thumbnail", requirements={"path"=".+"})
     * @param string $path
     *
     * @return Response
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function getThumbnail(string $path): Response
    {
        $resource = null;

        $mimePathA = sprintf('/%1$dx%1$d/mimetypes/%2$s', $this->thumbnailSize, $path);
        $mimePathB = sprintf('/base/%1$dx%1$d/mimetypes/%2$s', $this->thumbnailSize, $path);
        $mimePathC = sprintf('/base/%1$dx%1$d/mimetypes/application-octet-stream.png', $this->thumbnailSize);

        if ($this->storageThumbnails->has($path)) {
            $resource = $this->storageThumbnails->readStream($path);
        } else if ($this->storageMime->has($mimePathA)) {
            $resource = $this->storageThumbnails->readStream($mimePathA);
        } else if ($this->storageMime->has($mimePathB)) {
            $resource = $this->storageMime->readStream($mimePathB);
        } else if ($this->storageMime->has($mimePathC)) {
            $resource = $this->storageMime->readStream($mimePathC);
        }

        if (null !== $resource) {
            return new StreamedResponse(
                static function () use ($resource) {
                    stream_copy_to_stream($resource, fopen('php://output', 'wb'));
                },
                Response::HTTP_OK,
                [
                    'Content-Type' => 'image/png',
                ]
            );
        }
        throw new FileNotFoundException($path);
    }
}