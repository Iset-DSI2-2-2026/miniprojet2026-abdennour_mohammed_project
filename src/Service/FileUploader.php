<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    public function __construct(
        private string $targetDirectory,
        private SluggerInterface $slugger,
    ) {
    }

    public function upload(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), \PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $extension = $file->guessExtension() ?: $file->getClientOriginalExtension() ?: 'bin';
        $fileName = $safeFilename.'-'.uniqid().'.'.$extension;

        $file->move($this->getTargetDirectory(), (string) $fileName);

        return (string) $fileName;
    }

    public function remove(string $fileName): void
    {
        $filePath = $this->getTargetDirectory().'/'.$fileName;
        if (is_file($filePath)) {
            unlink($filePath);
        }
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}
