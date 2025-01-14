<?php

namespace App\Livewire;

use App\Actions\GetMarkdownFileAction;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('livewire.layouts.auth')]
class MarkdownPage extends Component
{
    public string $markdown;

    public function mount(string $document)
    {
        $markdown = new GetMarkdownFileAction($document);
        $markdown->handle();

        if ($markdown->failed()) {
            Flux::toast(__('toast.markdown-file-not-found'));

            return $this->redirect(route('landing.page'), true);
        }

        $this->markdown = $markdown->getFileContent();
    }
}
