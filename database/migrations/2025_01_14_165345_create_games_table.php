<?php

use App\Enums\Game\GameStatusEnum;
use App\Enums\Game\GameTypeEnum;
use App\Models\Map;
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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('hash');
            $table->enum('status', GameStatusEnum::values())->default(GameStatusEnum::AWAITING);
            $table->enum('type', GameTypeEnum::values())->default(GameTypeEnum::UNSUPPORTED);
            $table->foreignIdFor(Map::class);
            $table->json('data');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
