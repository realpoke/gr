<?php

namespace App\Actions\Clean;

use App\Actions\BaseAction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CleanOldReplaysAction extends BaseAction
{
    public function execute(): self
    {
        $fileNames = Storage::disk('replays')->files();

        foreach ($fileNames as $fileName) {
            if (! Str::endsWith($fileName, '.rep')) {
                continue;
            }

            $safeFor = Str::startsWith($fileName, 'good') ? 24 * 7 : 4;

            $lastModified = Carbon::createFromTimestamp(Storage::disk('replays')->lastModified($fileName));
            $diffInHours = $lastModified->diffInHours(Carbon::now());

            if ($diffInHours >= $safeFor || $diffInHours >= 24 * 30) {
                Storage::disk('replays')->delete($fileName);
            }
        }

        return $this->setSuccessful();
    }
}
