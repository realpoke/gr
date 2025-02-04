<?php

namespace App\Enums\Rank;

use App\Traits\EnumArray;

enum RankBracketEnum: string
{
    use EnumArray;

    case COMMANDER_IN_CHIEF = 'commander-in-chief';
    case GENERAL = 'general';
    case BRIGADIER_GENERAL = 'brigadier-general';
    case COLONEL = 'colonel';
    case MAJOR = 'major';
    case CAPTAIN = 'captain';
    case LIEUTENANT = 'lieutenant';
    case SERGEANT = 'sergeant';
    case CORPORAL = 'corporal';
    case PRIVATE = 'private';

    case UNRANKED = 'unranked';

    public static function fromElo(int $elo): self
    {
        return match (true) {
            $elo > self::COMMANDER_IN_CHIEF->eloRange()[0] => self::COMMANDER_IN_CHIEF,
            $elo > self::GENERAL->eloRange()[0] => self::GENERAL,
            $elo > self::BRIGADIER_GENERAL->eloRange()[0] => self::BRIGADIER_GENERAL,
            $elo > self::COLONEL->eloRange()[0] => self::COLONEL,
            $elo > self::MAJOR->eloRange()[0] => self::MAJOR,
            $elo > self::CAPTAIN->eloRange()[0] => self::CAPTAIN,
            $elo > self::LIEUTENANT->eloRange()[0] => self::LIEUTENANT,
            $elo > self::SERGEANT->eloRange()[0] => self::SERGEANT,
            $elo > self::CORPORAL->eloRange()[0] => self::CORPORAL,
            default => self::PRIVATE,
        };
    }

    public function eloRange(): array
    {
        return match ($this) {
            self::COMMANDER_IN_CHIEF => [2600, 9999],
            self::GENERAL => [2450, 2599],
            self::BRIGADIER_GENERAL => [2300, 2449],
            self::COLONEL => [2150, 2299],
            self::MAJOR => [2000, 2149],
            self::CAPTAIN => [1850, 1999],
            self::LIEUTENANT => [1700, 1849],
            self::SERGEANT => [1500, 1699],
            self::CORPORAL => [1300, 1499],
            self::PRIVATE => [0, 1299],
            default => [0, 9999],
        };
    }

    public function prettyName(bool $withRange = true): string
    {
        return match ($this) {
            self::COMMANDER_IN_CHIEF => $withRange ? __('enum.bracket.commander-in-chief').' ('.self::COMMANDER_IN_CHIEF->eloRange()[0].'+)' : __('enum.bracket.commander-in-chief'),
            self::GENERAL => $withRange ? __('enum.bracket.general').' ('.self::GENERAL->eloRange()[0].'-'.self::GENERAL->eloRange()[1].')' : __('enum.bracket.general'),
            self::BRIGADIER_GENERAL => $withRange ? __('enum.bracket.brigadier-general').' ('.self::BRIGADIER_GENERAL->eloRange()[0].'-'.self::BRIGADIER_GENERAL->eloRange()[1].')' : __('enum.bracket.brigadier-general'),
            self::COLONEL => $withRange ? __('enum.bracket.colonel').' ('.self::COLONEL->eloRange()[0].'-'.self::COLONEL->eloRange()[1].')' : __('enum.bracket.colonel'),
            self::MAJOR => $withRange ? __('enum.bracket.major').' ('.self::MAJOR->eloRange()[0].'-'.self::MAJOR->eloRange()[1].')' : __('enum.bracket.major'),
            self::CAPTAIN => $withRange ? __('enum.bracket.captain').' ('.self::CAPTAIN->eloRange()[0].'-'.self::CAPTAIN->eloRange()[1].')' : __('enum.bracket.captain'),
            self::LIEUTENANT => $withRange ? __('enum.bracket.lieutenant').' ('.self::LIEUTENANT->eloRange()[0].'-'.self::LIEUTENANT->eloRange()[1].')' : __('enum.bracket.lieutenant'),
            self::SERGEANT => $withRange ? __('enum.bracket.sergeant').' ('.self::SERGEANT->eloRange()[0].'-'.self::SERGEANT->eloRange()[1].')' : __('enum.bracket.sergeant'),
            self::CORPORAL => $withRange ? __('enum.bracket.corporal').' ('.self::CORPORAL->eloRange()[0].'-'.self::CORPORAL->eloRange()[1].')' : __('enum.bracket.corporal'),
            self::PRIVATE => $withRange ? __('enum.bracket.private').' ('.self::PRIVATE->eloRange()[0].'-'.self::PRIVATE->eloRange()[1].')' : __('enum.bracket.private'),
            default => ucfirst(str_replace('-', ' ', $this->value)),
        };
    }
}
