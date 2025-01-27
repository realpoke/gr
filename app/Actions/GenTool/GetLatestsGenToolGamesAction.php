<?php

namespace App\Actions\GenTool;

use App\Actions\BaseAction;
use App\Enums\Game\GameTypeEnum;
use App\Traits\Rules\GameRules;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class GetLatestsGenToolGamesAction extends BaseAction
{
    use GameRules;

    protected Collection $replays;

    public function getReplays(): Collection
    {
        return $this->replays;
    }

    public function getReplayPaths(): Collection
    {
        return $this->replays->flatMap(function ($item) {
            return collect($item['files'])->filter(function ($file) {
                return Str::endsWith($file, '.rep');
            });
        });
    }

    public function getTextPaths(): Collection
    {
        return $this->replays->flatMap(function ($item) {
            return collect($item['files'])->filter(function ($file) {
                return Str::endsWith($file, '.txt');
            });
        });
    }

    public function execute(): self
    {
        $yamlResponse = Http::get($this->latestGenToolYaml());

        if ($yamlResponse->successful()) {
            $this->replays = $this->parseYamlResponse($yamlResponse);

            return $this->setSuccessful();
        }

        return $this->setFailed('Failed to get latest GenToolUrl.');
    }

    private function latestGenToolYaml(): string
    {
        $now = Carbon::now()->subMinutes(10)->second(0);
        $now->minute = intval($now->minute / 10) * 10;

        return sprintf(
            'https://www.gentool.net/data/zh/logs/%s_%s/%s/uploads_%s%s%s_%s%s%s.yaml.txt',
            $now->year,
            str_pad($now->month, 2, '0', STR_PAD_LEFT),
            str_pad($now->day, 2, '0', STR_PAD_LEFT),
            $now->year,
            str_pad($now->month, 2, '0', STR_PAD_LEFT),
            str_pad($now->day, 2, '0', STR_PAD_LEFT),
            str_pad($now->hour, 2, '0', STR_PAD_LEFT),
            str_pad($now->minute, 2, '0', STR_PAD_LEFT),
            '00'
        );
    }

    private function parseYamlResponse(Response $response): Collection
    {
        $bodyArray = explode('---', $response->body());
        array_shift($bodyArray);
        $yamlCollection = new Collection;

        foreach ($bodyArray as $yaml) {
            try {
                $cleanYaml = preg_replace('/^username:.*\n/m', '', $yaml);
                $parsedYaml = Yaml::parse($cleanYaml);

                // Skip if not an array or missing required structure
                if (! is_array($parsedYaml) || ! isset($parsedYaml['files']) || ! is_array($parsedYaml['files'])) {
                    continue;
                }

                if ($parsedYaml['version'] < $this->minimumGenToolVersion()) {
                    continue;
                }

                $parsedYaml['username'] = $this->extractUsernameFromYaml($yaml);

                if (! empty($parsedYaml['files'])) {
                    $firstFile = $parsedYaml['files'][0];
                    if (! is_string($firstFile)) {
                        continue;
                    }

                    $parts = explode('/', $firstFile);
                    if (isset($parts[5])) {
                        $gameType = explode('_', $parts[5])[1] ?? null;
                        if ($gameType && in_array($gameType, GameTypeEnum::validGameTypeStrings())) {
                            $yamlCollection->add($parsedYaml);
                        }
                    }
                }
            } catch (ParseException $e) {
                continue;
            }
        }

        return $yamlCollection;
    }

    private function extractUsernameFromYaml($yaml): ?string
    {
        if (preg_match('/^username:\s*(.+)$/m', $yaml, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }
}
