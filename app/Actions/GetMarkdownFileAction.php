<?php

namespace App\Actions;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class GetMarkdownFileAction extends BaseAction
{
    private ?string $fileContent = null;

    public function __construct(private string $fileName) {}

    public function execute(): self
    {
        $localName = $this->fileName.'_'.app()->getLocale();

        $path = Arr::first([
            resource_path('markdown/'.$localName.'.md'),
            resource_path('markdown/'.$this->fileName.'.md'),
        ], function ($path) {
            return file_exists($path);
        });

        if ($path == false) {
            return $this->setFailed(__('markdown.file-not-found'));
        }

        $this->fileContent = Str::markdown(file_get_contents($path));

        return $this->setSuccessful();
    }

    public function getFileContent(): ?string
    {
        return $this->fileContent;
    }
}
