<?php

use App\Enums\Game\GameModeEnum;
use App\Enums\Rank\RankTimeFrameEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('periods', function (Blueprint $table) {
            $table->id();

            $table->enum('game_mode', GameModeEnum::ccases(GameModeEnum::validGameModes()));
            $table->enum('rank_time_frame', RankTimeFrameEnum::values());

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periods');
    }
};
