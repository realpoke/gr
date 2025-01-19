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
            if (! Str::endsWith($fileName, '.rep') || Str::startsWith($fileName, 'good')) {
                continue;
            }

            $lastModified = Carbon::createFromTimestamp(Storage::disk('replays')->lastModified($fileName));

            if ($lastModified->diffInHours(Carbon::now()) >= 24) {
                Storage::disk('replays')->delete($fileName);
            }
        }

        return $this->setSuccessful();
    }
}
