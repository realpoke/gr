<?php

namespace App\Actions\Replay;

use App\Actions\BaseAction;
use App\Actions\Map\HashMapAction;
use App\Enums\FactionEnum;
use App\Enums\Game\GameTypeEnum;
use App\Enums\PlayerTypeEnum;
use App\Enums\SideEnum;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ReplayParserAction extends BaseAction
{
    private string $replayOwnerName;

    private Collection $players;

    private string $GameHash;

    private Collection $gameBuild;

    private Carbon $playedAt;

    private Collection $importantOrders;

    private Collection $metaData;

    public function __construct(private Collection $data) {}

    public function execute(): self
    {
        try {
            $this->setGameHash($this->data);
        } catch (\Throwable $exception) {
            return $this->setFailed('Failed to set game hash, error: '.$exception->getMessage());
        }

        try {
            $this->setPlayers($this->data);
        } catch (\Throwable $exception) {
            return $this->setFailed('Failed to set players, error: '.$exception->getMessage());
        }

        try {
            $this->setImportantOrders($this->data);
        } catch (\Throwable $exception) {
            return $this->setFailed('Failed to set important orders, error: '.$exception->getMessage());
        }

        try {
            $this->setGameBuild($this->data);
        } catch (\Throwable $exception) {
            return $this->setFailed('Failed to set game build, error: '.$exception->getMessage());
        }

        try {
            $this->setPlayedAt($this->data);
        } catch (\Throwable $exception) {
            return $this->setFailed('Failed to set played at, error: '.$exception->getMessage());
        }

        try {
            $this->setMetaData($this->data);
        } catch (\Throwable $exception) {
            return $this->setFailed('Failed to set meta data, error: '.$exception->getMessage());
        }

        return $this->setSuccessful();
    }

    public function all(): Collection
    {
        return collect([
            'gameData' => $this->getGameData(),
            'gameHash' => $this->getGameHash(),
            'metaData' => $this->getMetaData(),
            'gameBuild' => $this->getGameBuild(),
            'replayOwnerName' => $this->getReplayOwnerName(),
            'players' => $this->getPlayers(),
            'playedAt' => $this->getPlayedAt(),
            'importantOrders' => $this->getImportantOrders(),
        ]);
    }

    public function getGameData(): Collection
    {
        return collect([
            'metaData' => $this->getMetaData(),
            'gameBuild' => $this->getGameBuild(),
            'players' => $this->getPlayers(),
            'playedAt' => $this->getPlayedAt(),
            'importantOrders' => $this->getImportantOrders(),
        ]);
    }

    public function getPlayedAt(): Carbon
    {
        return $this->playedAt;
    }

    private function setPlayedAt(Collection $data): void
    {
        $this->playedAt = Carbon::create(
            $data['Header']['Year'],
            $data['Header']['Month'],
            $data['Header']['Day'],
            $data['Header']['Hour'],
            $data['Header']['Minute'],
            $data['Header']['Second']
        );
    }

    public function getGameHash(): string
    {
        return $this->GameHash;
    }

    private function setGameHash(Collection $data)
    {
        $this->GameHash = md5(
            $data['Body'][5]['TimeCode'].
            $data['Body'][10]['OrderCode'].
            $data['Header']['Hash'].
            $data['Header']['Metadata']['MapSize'].
            $data['Header']['Metadata']['Seed'].
            $data['Header']['GameType'].
            $data['Header']['VersionMinor'].
            $data['Header']['VersionMajor']
        );
    }

    public function getReplayOwnerName(): string
    {
        return $this->replayOwnerName;
    }

    public function getPlayers(): Collection
    {
        return $this->players;
    }

    private function setPlayers(Collection $data)
    {
        $summary = collect($data['Summary']);
        $replayOwnerSlot = $data['Header']['ReplayOwnerSlot'];

        $this->players = collect($data['Header']['Metadata']['Players'])
            ->map(function ($player, $index) use ($summary, $replayOwnerSlot) {
                $playerSummary = $summary->firstWhere('Name', $player['Name']);

                // Calculate the expected slot format for this player
                $playerSlotFormat = '3'.$index.'00';

                if ((string) $replayOwnerSlot == $playerSlotFormat) {
                    $this->replayOwnerName = $player['Name'];
                }

                $type = PlayerTypeEnum::tryFrom(strtolower(str_replace(' ', '-', $player['Type'])));
                if ($type === null) {
                    throw new \Exception('Player '.$player['Name'].' has an invalid type set: '.$player['Type']);
                }

                $faction = FactionEnum::tryFrom(strtolower(str_replace(' ', '-', $player['Faction'])));

                if ($faction === null) {
                    throw new \Exception('Player '.$player['Name'].' has an invalid faction set: '.$player['Faction']);
                }

                if (! $faction->isPlaying() && $type->isHuman()) {
                    $side = SideEnum::OBSERVER;
                } else {
                    $side = SideEnum::tryFrom(strtolower(str_replace(' ', '-', $playerSummary['Side'])));
                    if ($side === null) {
                        if ($faction == FactionEnum::RANDOM) { // TODO: Look into getting a btter binary replay parser, this could happen if the game is way to short.
                            throw new \Exception('Player '.$player['Name'].' is random faction but their side was not detected!');
                        }
                        throw new \Exception('Player '.$player['Name'].' has an invalid side set: '.$playerSummary['Side']);
                    }
                }

                // Determine if the player is playing
                $isPlaying = $type->isHuman() && $faction->isPlaying();

                return [
                    'name' => $player['Name'],
                    'type' => $type,
                    'faction' => $faction,
                    'team' => $player['Team'],
                    'startingPosition' => $player['StartingPosition'],
                    'isPlaying' => $isPlaying,
                    'side' => $side,
                    'win' => false, // NOTE: Always set win to false, we use our own win calculation.
                    'moneySpent' => $playerSummary['MoneySpent'] ?? null,
                    'unitsCreated' => $playerSummary['UnitsCreated'] ?? [],
                    'buildingsBuilt' => $playerSummary['BuildingsBuilt'] ?? [],
                    'upgradesBuilt' => $playerSummary['UpgradesBuilt'] ?? [],
                    'powersUsed' => $playerSummary['PowersUsed'] ?? [],
                ];
            });

        // After processing all players, verify that we found the replay owner
        if (empty($this->replayOwnerName)) {
            throw new \Exception('Could not find replay owner in any player slot');
        }
    }

    public function getMetaData(): Collection
    {
        return $this->metaData;
    }

    private function setMetaData(Collection $data)
    {
        $playersPlaying = $this->players->where('isPlaying', true)->count();
        $hasBots = $this->players->contains(function ($player) {
            return $player['type']->isBot();
        });

        $mapHasher = new HashMapAction(
            collect(explode('/', $data['Header']['Metadata']['MapFile']))->pop(),
            $data['Header']['Metadata']['MapCRC'],
            $data['Header']['Metadata']['MapSize'],
        );
        $mapHasher->handle();

        if ($mapHasher->failed()) {
            throw new \Exception('Failed to hash map: '.$mapHasher->getErrorMessage());
        }

        $this->metaData = collect($data['Header']['Metadata'])->only([
            'MapFile',
            'MapCRC',
            'MapSize',
            'Seed',
            'C',
            'SR',
            'StartingCredits',
            'O',
        ])->merge(collect($data['Header'])->only([
            'TimeStampBegin',
            'TimeStampEnd',
            'NumTimeStamps',
            'Hash',
        ]))->merge([
            'MapHash' => $mapHasher->getHash(),
            'playersPlaying' => $playersPlaying,
            'hasBots' => $hasBots,
            'gameType' => $this->determinGameType($this->players->toArray()),
            'gameInterval' => (int) ($data['Header']['NumTimeStamps'] / 15),
        ]);
    }

    public function getImportantOrders(): Collection
    {
        return $this->importantOrders;
    }

    private function setImportantOrders(Collection $data)
    {
        $this->importantOrders = collect($data['Body'])->filter(function ($command) {
            return ($command['OrderCode'] == 27 && $command['OrderName'] == 'EndReplay') ||
                   ($command['OrderCode'] == 1093 && $command['OrderName'] == 'Surrender');
        })->map(function ($command) {
            if ($command['PlayerName'] === '') {
                throw new \Exception('Important order has no PlayerName set');
            }

            return [
                'TimeCode' => $command['TimeCode'],
                'OrderCode' => $command['OrderCode'],
                'OrderName' => $command['OrderName'],
                'PlayerName' => $command['PlayerName'],
                'Arguments' => $command['Arguments'],
            ];
        });
    }

    public function getGameBuild(): Collection
    {
        return $this->gameBuild;
    }

    private function setGameBuild(Collection $data)
    {
        $this->gameBuild = collect([
            'gameType' => $data['Header']['GameType'],
            'buildDate' => $data['Header']['BuildDate'],
            'versionMinor' => $data['Header']['VersionMinor'],
            'versionMajor' => $data['Header']['VersionMajor'],
        ]);
    }

    private function determinGameType(array $allPlayers): GameTypeEnum
    {
        // Filter out players that are not playing
        $players = array_filter($allPlayers, function ($player) {
            return $player['isPlaying'] ?? false;
        });

        $playerCount = count($players);
        $teams = array_column($players, 'team');
        $uniqueTeams = array_unique($teams);
        $teamCount = count($uniqueTeams);

        // Solo
        if ($playerCount === 1) {
            return GameTypeEnum::SOLO;
        }

        // 1v1
        if ($playerCount === 2) {
            // Both on team -1 or on different teams
            if (($teamCount === 1 && $uniqueTeams[0] === '-1') || $teamCount === 2) {
                return GameTypeEnum::ONE_ON_ONE;
            }
        }

        // FFA
        if ($this->isFFA($players, $teams, $uniqueTeams)) {
            return match ($playerCount) {
                3 => GameTypeEnum::FREE_FOR_ALL_THREE,
                4 => GameTypeEnum::FREE_FOR_ALL_FOUR,
                5 => GameTypeEnum::FREE_FOR_ALL_FIVE,
                6 => GameTypeEnum::FREE_FOR_ALL_SIX,
                7 => GameTypeEnum::FREE_FOR_ALL_SEVEN,
                8 => GameTypeEnum::FREE_FOR_ALL_EIGHT,
                default => GameTypeEnum::UNSUPPORTED,
            };
        }

        // Team games
        if ($teamCount === 2 && ! in_array('-1', $teams)) {
            return match ($playerCount) {
                4 => GameTypeEnum::TWO_ON_TWO,
                6 => GameTypeEnum::THREE_ON_THREE,
                8 => GameTypeEnum::FOUR_ON_FOUR,
                default => GameTypeEnum::UNSUPPORTED,
            };
        }

        // Co-op (all on same team)
        if ($teamCount === 1 && ! in_array('-1', $teams)) {
            return match ($playerCount) {
                2 => GameTypeEnum::CO_OP_TWO,
                3 => GameTypeEnum::CO_OP_THREE,
                4 => GameTypeEnum::CO_OP_FOUR,
                5 => GameTypeEnum::CO_OP_FIVE,
                6 => GameTypeEnum::CO_OP_SIX,
                7 => GameTypeEnum::CO_OP_SEVEN,
                8 => GameTypeEnum::CO_OP_EIGHT,
                default => GameTypeEnum::UNSUPPORTED,
            };
        }

        // Multi-team
        if ($teamCount > 2) {
            return match ($teamCount) {
                3 => GameTypeEnum::MULTI_TEAM_THREE,
                4 => GameTypeEnum::MULTI_TEAM_FOUR,
                default => GameTypeEnum::UNSUPPORTED,
            };
        }

        return GameTypeEnum::UNSUPPORTED;
    }

    private function isFFA(array $players, array $teams, array $uniqueTeams): bool
    {
        if (count($uniqueTeams) === count($players) || (count($uniqueTeams) === 1 && $uniqueTeams[0] === '-1')) {
            return true;
        }

        $teamCounts = array_count_values($teams);

        foreach ($teamCounts as $team => $count) {
            if ($team !== '-1' && $count > 1) {
                return false;
            }
        }

        return true;
    }
}
