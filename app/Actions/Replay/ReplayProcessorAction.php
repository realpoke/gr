<?php

namespace App\Actions\Replay;

use App\Actions\BaseAction;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class ReplayProcessorAction extends BaseAction
{
    private ?string $processOutput;

    public function getProcessOutput(): ?string
    {
        return $this->processOutput;
    }

    public function __construct(private string $replayFileName) {}

    public function execute(): self
    {
        try {
            $replayFileContent = Storage::disk('replays')->get($this->replayFileName);
        } catch (\throwable $e) {
            Storage::disk('replays')->delete($this->replayFileName);

            return $this->setFailed('Could not get replay file on disk: '.$this->replayFileName.' with error: '.$e->getMessage());
        }

        $compressor = new ReplayCompressorAction($replayFileContent);
        $compressor->handle();

        if ($compressor->failed()) {
            return $this->setFailed('Failed to uncompress replay: '.$compressor->getErrorMessage());
        }

        $binary = $this->getReplayParserBinary();
        if (is_null($binary)) {
            return $this->setFailed('Failed to find replay parser binary');
        }

        $tempFilePath = tempnam(sys_get_temp_dir(), 'temp_replay_'.random_int(1000, 9999));
        if ($tempFilePath === false) {
            return $this->setFailed('Failed to create temporary file.');
        }

        file_put_contents($tempFilePath, $compressor->getUncompressedContent());

        $processResult = Process::env([
            'PATH' => '/usr/local/bin:/usr/bin:/bin',
        ])->run([$binary, $tempFilePath]);

        unlink($tempFilePath);

        if ($processResult->failed()) {
            return $this->setFailed('Process failed with the following output: '.$processResult->errorOutput());
        }

        if (empty($processResult->output())) {
            return $this->setFailed('Process output was empty');
        }

        $this->processOutput = $processResult->output();

        // The binary leaves a bunch of temporary files, remove them
        $process = Process::run('rm -f /tmp/example_*');
        if ($process->failed()) {
            return $this->setFailed('Failed to remove temporary files: '.$process->errorOutput());
        }

        return $this->setSuccessful();
    }

    private function getReplayParserBinary(): ?string
    {
        if (Storage::disk('binaries')->exists('replay_parser_live')) {
            return Storage::disk('binaries')->path('replay_parser_live');
        } elseif (Storage::disk('binaries')->exists('replay_parser')) {
            return Storage::disk('binaries')->path('replay_parser');
        }

        return null;
    }
}
