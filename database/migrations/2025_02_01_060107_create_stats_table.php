<?php

use App\Enums\FactionEnum;
use App\Enums\Rank\RankBracketEnum;
use App\Models\Period;
use App\Models\User;
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
        Schema::create('stats', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Period::class);
            $table->foreignIdFor(User::class);

            $table->json('data')->nullable();

            $table->tinyInteger('favorite_faction')->default(FactionEnum::RANDOM->value);
            $table->enum('bracket', RankBracketEnum::values())->default(RankBracketEnum::UNRANKED);
            $table->smallInteger('elo')->unsigned()->default(1500);
            $table->mediumInteger('rank')->unsigned()->nullable();
            $table->decimal('win_percentage', 5, 2)->unsigned()->default(0);
            $table->smallInteger('wins')->unsigned()->default(0);
            $table->smallInteger('losses')->unsigned()->default(0);
            $table->mediumInteger('games')->unsigned()->default(0);
            $table->mediumInteger('streak')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stats');
    }
};
