<?php

namespace App\Actions\GenTool;

use App\Actions\BaseAction;
use App\Traits\Rules\GameRules;
use Carbon\CarbonInterval;
use Illuminate\Support\Str;

class TextFileValidatorAction extends BaseAction
{
    use GameRules;

    public function __construct(private string $textPath) {}

    public function execute(): self
    {
        $txtValidator = $this->checkTxtFile($this->textPath);
        if ($txtValidator !== null) {
            return $this->setFailed($txtValidator);
        }

        return $this->setSuccessful();
    }

    private function checkTxtFile(string $txtPath): ?string
    {
        if (! Str::endsWith($txtPath, '.txt')) {
            return 'Not a txt file.';
        }

        try {
            $content = file_get_contents('https://www.gentool.net/'.$txtPath);
        } catch (\Throwable $th) {
            return 'Failed to get txt file: '.$th->getMessage();
        }

        preg_match('/GenTool Version:\s+(\d+\.\d+)/', $content, $genToolVersion);

        if (empty($genToolVersion[1])) {
            return 'GenTool version not found.';
        }

        $genToolVersionNumber = (int) str_replace('.', '', $genToolVersion[1]);

        if ($genToolVersionNumber < $this->minimumGenToolVersion()) {
            return 'GenTool version is too low. Got: '.$genToolVersionNumber;
        }

        $validGameVersions = [
            'Zero Hour 1.04 The Ultimate Collection (x87, Steam)',
            'Zero Hour 1.04 The Ultimate Collection (x87, EA Origin)',
            'Zero Hour 1.04 The Ultimate Collection (x87, EA app)',
            'Zero Hour 1.04 (x87, CD)',
            'Zero Hour 1.04 (x87)',
            'Generals: Zero Hour',
        ];

        preg_match('/Game Version:\s+(.+)/', $content, $gameVersion);

        if (empty($gameVersion[1])) {
            return 'Could not find game version.';
        }

        if (! in_array($gameVersion[1], $validGameVersions)) {
            return 'Game version is not valid. Got: '.$gameVersion[1];
        }

        preg_match('/Match Length:\s+(\d{2}:\d{2}:\d{2})/', $content, $matchLength);

        if (empty($matchLength[1])) {
            return 'Match length not found.';
        }

        [$hours, $minutes, $seconds] = explode(':', $matchLength[1]);

        $interval = CarbonInterval::hours((int) $hours)
            ->minutes((int) $minutes)
            ->seconds((int) $seconds);

        if ($interval->totalSeconds < $this->minimumGameInterval()->totalSeconds) {
            return 'Match length is too short. Found: '.$interval->format('%H:%I:%S');
        }

        preg_match('/Match Mode:\s+.+\n\n(.*?)\n\nAssociated files:/s', $content, $matches);

        if (empty($matches[1])) {
            return 'Players section not found.';
        }

        $playersSection = trim($matches[1]);

        $lines = explode("\n", $playersSection);

        $aiDetected = false;

        foreach ($lines as $line) {
            // Skip lines that do not match the player format (IP USERNAME (FACTION))
            if (preg_match('/^\s*([\w\s]+)\s+(\S+)\s+\((.+)\)$/', $line, $playerMatches)) {
                $ip = trim($playerMatches[1]);
                $username = trim($playerMatches[2]);

                if (in_array($ip, ['Easy', 'Medi', 'Hard'], true) && $username === 'AI') {
                    $aiDetected = true;
                    break;
                }
            }
        }

        if ($aiDetected) {
            return 'Game has AI.';
        }

        return null;
    }
}
