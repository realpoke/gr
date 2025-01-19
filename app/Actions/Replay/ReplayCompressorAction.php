<?php

namespace App\Actions\Replay;

use App\Actions\BaseAction;

class ReplayCompressorAction extends BaseAction
{
    private $contentCompressed;

    private $contentOriginal;

    public function __construct(private string $fileContent) {}

    public function execute(): self
    {
        try {
            $uncompressed = gzuncompress($this->fileContent);

            $this->contentOriginal = $uncompressed;
            $this->contentCompressed = $this->fileContent;
        } catch (\Throwable $th1) {
            try {
                $compressed = gzcompress($this->fileContent);

                $this->contentOriginal = $this->fileContent;
                $this->contentCompressed = $compressed;
            } catch (\Throwable $th2) {
                return $this->setFailed('Failed to compress or uncompress replay error: '.$th1->getMessage().' and '.$th2->getMessage());
            }
        }

        return $this->setSuccessful();
    }

    public function getCompressedContent(): string
    {
        return $this->contentCompressed;
    }

    public function getUncompressedContent(): string
    {
        return $this->contentOriginal;
    }
}
