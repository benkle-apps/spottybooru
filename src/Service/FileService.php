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

namespace App\Service;


use App\Entity\Post;
use Imagick;
use ImagickException;
use Jenssegers\ImageHash\ImageHash;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use Mimey\MimeTypes;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\HttpException;

class FileService
{
    private FilesystemInterface $storageFiles;

    private FilesystemInterface $storageThumbnails;

    private MimeTypes $mimeTypes;

    private ImageHash $imageHash;

    private int $thumbnailSize;

    /**
     * FileService constructor.
     *
     * @param FilesystemInterface $storageFiles
     * @param FilesystemInterface $storageThumbnails
     * @param MimeTypes           $mimeTypes
     * @param ImageHash           $imageHash
     * @param int                 $thumbnailSize
     */
    public function __construct(FilesystemInterface $storageFiles, FilesystemInterface $storageThumbnails, MimeTypes $mimeTypes, ImageHash $imageHash, int $thumbnailSize)
    {
        $this->storageFiles = $storageFiles;
        $this->storageThumbnails = $storageThumbnails;
        $this->mimeTypes = $mimeTypes;
        $this->imageHash = $imageHash;
        $this->thumbnailSize = $thumbnailSize;
    }

    /**
     * Gather information from an uploaded file and store it in a post.
     *
     * @param Post         $post
     * @param UploadedFile $file
     *
     * @return $this
     * @throws ImagickException
     */
    public function getInformationFromUpload(Post $post, UploadedFile $file): self
    {
        $post->mime = $file->getMimeType() ?? 'application/octet-stream';
        $post->size = $file->getSize();

        $realPath = $this->getRealPath($file);

        if (0 === strpos($post->mime, 'image/')) {
            //$post->checksum = $this->imageHash->hash($realPath)->toHex();
            $imagick = new Imagick();
            $imagick->readImage($realPath);
            $post->width = $imagick->getImageWidth();
            $post->height = $imagick->getImageHeight();
            $post->checksum = $imagick->getImageSignature();
            unset($imagick);
        } else if ('application/x-shockwave-flash' === $post->mime) {
                $post->checksum = sha1_file($realPath);
                [$width, $height, $type, $attr] = getimagesize($realPath);
                $post->width = $width;
                $post->height = $height;
        } else {
            // TODO What now?
        }

        $path = array_slice(str_split($post->checksum, 3), 0, 2);
        $post->file = implode('/', array_merge($path, [$post->checksum . '.' . $this->mimeTypes->getExtension($post->mime)]));

        return $this;
    }

    /**
     * @param UploadedFile $file
     *
     * @return string
     */
    private function getRealPath(UploadedFile $file): string
    {
        $realPath = $file->getRealPath();
        if (!$realPath || !file_exists($realPath)) {
            throw new HttpException(413, sprintf('Upload is missing, maybe it was bigger than %d', UploadedFile::getMaxFilesize()));
        }
        return $realPath;
    }

    /**
     * Actually move an uploaded file.
     *
     * @param Post         $post
     * @param UploadedFile $file
     *
     * @return $this
     */
    public function moveUpload(Post $post, UploadedFile $file): self
    {
        $realPath = $this->getRealPath($file);

        $stream = fopen($realPath, 'rb');
        $this->storageFiles->putStream($post->file, $stream);
        fclose($stream);

        return $this;
    }

    /**
     * Delete all files attached to a post.
     *
     * @param Post $post
     *
     * @return $this
     * @throws FileNotFoundException
     */
    public function deleteFiles(Post $post): self
    {
        if ($this->storageFiles->has($post->file)) {
            $this->storageFiles->delete($post->file);
        }
        if ($this->storageThumbnails->has($post->thumbnail)) {
            $this->storageThumbnails->delete($post->thumbnail);
        }
        return $this;
    }

    /**
     * Create a thumbnail for a post.
     *
     * @param Post              $post
     * @param UploadedFile|null $file
     *
     * @return $this
     * @throws FileNotFoundException
     * @throws ImagickException
     * @throws FileExistsException
     */
    public function createThumbnail(Post $post, ?UploadedFile $file): self
    {
        $post->thumbnail = $post->file;
        $imagick = new Imagick();

        if (null !== $file) {
            $realPath = $this->getRealPath($file);
            $imagick->readImage($realPath);
        } else if (0 === strpos($post->mime, 'image/')) {
            $imagick->readImageFile($this->storageFiles->readStream($post->file));
        } else {
            $post->thumbnail = str_replace('/', '-', $post->mime) . '.png';
        }

        $imagick->thumbnailImage($this->thumbnailSize, $this->thumbnailSize, true);
        $thumbnail = fopen('php://temp', 'wb+');
        $imagick->writeImageFile($thumbnail);
        $this->storageThumbnails->writeStream($post->thumbnail, $thumbnail);

        return $this;
    }
}