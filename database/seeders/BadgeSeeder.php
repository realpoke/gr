<?php

namespace Database\Seeders;

use App\Enums\BadgeTypeEnum;
use App\Enums\Game\GameModeEnum;
use App\Enums\Rank\RankBracketEnum;
use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $badges = [
            // Additional badges
            'badge.name.mvp' => [
                'description' => 'badge.description.mvp',
                'classes' => 'text-amber-500 dark:text-amber-300',
                'type' => BadgeTypeEnum::ADDITIONAL,
                'icon' => 'medal',
            ],

            // Permission badges
            'badge.name.developer' => [
                'description' => 'badge.description.developer',
                'classes' => 'text-cyan-500 dark:text-cyan-300',
                'type' => BadgeTypeEnum::PERMISSION,
                'icon' => 'shield-check',
                'data' => [
                    'permissions' => [
                        '*',
                    ],
                ],
            ],
            'badge.name.admin' => [
                'description' => 'badge.description.admin',
                'classes' => 'text-red-500 dark:text-red-300',
                'type' => BadgeTypeEnum::PERMISSION,
                'icon' => 'shield-check',
                'data' => [
                    'permissions' => [
                        '*',
                    ],
                ],
            ],
            'badge.name.moderator' => [
                'description' => 'badge.description.moderator',
                'classes' => 'text-amber-500 dark:text-amber-300',
                'type' => BadgeTypeEnum::PERMISSION,
                'icon' => 'shield-check',
                'data' => [
                    'permissions' => [
                        'some',
                    ],
                ],
            ],
            'badge.name.helper' => [
                'description' => 'badge.description.helper',
                'classes' => 'text-lime-500 dark:text-lime-300',
                'type' => BadgeTypeEnum::PERMISSION,
                'icon' => 'shield-check',
                'data' => [
                    'permissions' => [
                        'some',
                    ],
                ],
            ],

            // Unique badges
            'badge.name.donator' => [
                'description' => 'badge.description.donator',
                'classes' => 'text-amber-500 dark:text-amber-300',
                'type' => BadgeTypeEnum::UNIQUE,
                'icon' => 'gift',
            ],
            'badge.name.content-creator' => [
                'description' => 'badge.description.content-creator',
                'classes' => 'text-sky-500 dark:text-sky-300',
                'type' => BadgeTypeEnum::UNIQUE,
                'icon' => 'video-camera',
            ],
            'badge.name.verified' => [
                'description' => 'badge.description.verified',
                'classes' => 'text-green-500 dark:text-green-300',
                'type' => BadgeTypeEnum::UNIQUE,
                'icon' => 'check-circle',
            ],
            'badge.name.translator' => [
                'description' => 'badge.description.translator',
                'classes' => 'text-indigo-500 dark:text-indigo-300',
                'type' => BadgeTypeEnum::UNIQUE,
                'icon' => 'language',
            ],
            'badge.name.feedbacker' => [
                'description' => 'badge.description.feedbacker',
                'classes' => 'text-amber-500 dark:text-amber-300',
                'type' => BadgeTypeEnum::UNIQUE,
                'icon' => 'chat-bubble-left-ellipsis-line',
            ],

            // Since badges
            'badge.name.supporter' => [
                'description' => 'badge.description.supporter',
                'classes' => 'text-indigo-500 dark:text-indigo-300',
                'type' => BadgeTypeEnum::SINCE,
                'icon' => 'lifebuoy',
            ],
            'badge.name.joined' => [
                'description' => 'badge.description.joined',
                'classes' => 'text-indigo-500 dark:text-indigo-300',
                'type' => BadgeTypeEnum::SINCE,
                'icon' => 'calendar',
            ],

            // Timestamp badges
            'badge.name.alpha-tester' => [
                'description' => 'badge.description.alpha-tester',
                'classes' => 'text-amber-500 dark:text-amber-300',
                'type' => BadgeTypeEnum::TIMESTAMP,
                'icon' => 'light-bulb',
            ],
            'badge.name.beta-tester' => [
                'description' => 'badge.description.beta-tester',
                'classes' => 'text-purple-500 dark:text-purple-300',
                'type' => BadgeTypeEnum::TIMESTAMP,
                'icon' => 'light-bulb',
            ],
            'badge.name.early-bird' => [
                'description' => 'badge.description.early-bird',
                'classes' => 'text-pink-500 dark:text-pink-300',
                'type' => BadgeTypeEnum::TIMESTAMP,
                'icon' => 'light-bulb',
            ],
        ];

        foreach (RankBracketEnum::cases() as $bracket) {
            if ($bracket == RankBracketEnum::UNRANKED) {
                continue;
            }

            foreach (GameModeEnum::validGameModes() as $mode) {
                $badges['badge.name.ranked-monthly-'.$mode->value.'-'.$bracket->value] = [
                    'description' => 'badge.description.ranked-monthly-'.$mode->value.'-'.$bracket->value,
                    'classes' => 'text-rose-500 dark:text-rose-300',
                    'type' => BadgeTypeEnum::ADDITIONAL,
                    'icon' => 'trophy',
                ];

                $badges['badge.name.ranked-yearly-'.$mode->value.'-'.$bracket->value] = [
                    'description' => 'badge.description.ranked-yearly-'.$mode->value.'-'.$bracket->value,
                    'classes' => 'text-rose-500 dark:text-rose-300',
                    'type' => BadgeTypeEnum::ADDITIONAL,
                    'icon' => 'trophy',
                ];
            }
        }

        foreach ($badges as $name => $badge) {

            if (Badge::where('name', $name)->exists()) {
                continue;
            }

            dump('Creating badge: '.$name);

            Badge::create([
                'name' => $name,
                'description' => $badge['description'],
                'classes' => $badge['classes'],
                'type' => $badge['type'],
                'icon' => $badge['icon'],
                'data' => $badge['type'] == BadgeTypeEnum::PERMISSION ? $badge['data'] : null,
            ]);
        }
    }
}
