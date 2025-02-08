<?php

namespace App\Livewire\Partials;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ZhIconComponent extends Component
{
    public string $unit;

    public string $tooltip;

    #[Computed()]
    public function src(): Collection
    {
        $type = '.png';

        $exploded = collect(explode('_', $this->unit));
        $name = $exploded->pop();
        $subFolder = $exploded->pop();

        $basePath = 'storage/images/zh/';
        $iconPath = $basePath.$subFolder.'/'.$subFolder.'_'.$name.$type;

        if (! is_null($subFolder) && file_exists(public_path($iconPath))) {
            $iconAsset = asset($iconPath);
        } elseif (file_exists(public_path($basePath.'/base/'.$name.$type))) {
            $iconAsset = asset($basePath.'/base/'.$name.$type);
        } else {
            $iconAsset = asset('storage/images/zh/unknown.png');
        }

        if (! is_null($subFolder) && file_exists(public_path($basePath.'/teams/'.$subFolder.$type))) {
            $teamAsset = asset($basePath.'/teams/'.$subFolder.$type);
        } elseif (str::contains($name, 'GLA')) {
            $teamAsset = asset($basePath.'/teams/GLA'.$type);
        } elseif (str::contains($name, 'CHINA')) {
            $teamAsset = asset($basePath.'/teams/China'.$type);
        } elseif (str::contains($name, 'America')) {
            $teamAsset = asset($basePath.'/teams/America'.$type);
        }

        return collect([
            'icon' => $iconAsset,
            'team' => $teamAsset ?? null,
        ]);
    }
}
