<?php

namespace App\Actions\GenTool;

use App\Actions\BaseAction;
use App\Actions\Replay\ReplayCompressorAction;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DownloadGenToolReplayAction extends BaseAction
{
    private ?string $file;

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function __construct(
        private string $replayUrlPath,
        private int|string|null $uniqueId = null
    ) {}

    public function execute(): self
    {
        $url = 'https://www.gentool.net/'.$this->replayUrlPath;

        if (is_null($this->uniqueId)) {
            $this->uniqueId = Str::random(8);
        }

        $fileName = time().'_'.$this->uniqueId.'_replay.rep';

        try {
            $fileContent = file_get_contents($url);
            if ($fileContent === false) {
                return $this->setFailed('Failed to get content from url: '.$url);
            }

            $compressor = new ReplayCompressorAction($fileContent);
            $compressor->handle();

            if ($compressor->failed()) {
                return $this->setFailed('Failed to compress replay: '.$compressor->getErrorMessage());
            }

            $uploadedReplay = Storage::disk('replays')->put($fileName, $compressor->getCompressedContent());

            if ($uploadedReplay === false) {
                return $this->setFailed('Failed to save replay to disk.');
            }
        } catch (\Throwable $th) {
            Storage::disk('replays')->delete($fileName);

            return $this->setFailed('Failed to download replay error: '.$th->getMessage());
        }

        $this->file = $fileName;

        return $this->setSuccessful();
    }
}
