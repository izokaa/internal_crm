<?php

namespace App\Filament\Resources\OpportunityResource\Pages;

use App\Filament\Resources\OpportunityResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Illuminate\Contracts\View\View;
use Filament\Forms\Form;
use Filament\Forms\Components;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Concerns\HasForms;

class ViewOpportunityDetails extends ViewRecord
{
    use InteractsWithForms;

    protected static string $resource = OpportunityResource::class;

    protected static string $view = 'filament.resources.opportunity-resource.pages.view-opportunity-details';

    public function infolist(Infolist $infolist): Infolist
    {
        return OpportunityResource::infolist($infolist);
    }

    public function getHeader(): ?View
    {
        return null;
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    public $commentContent;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Components\MarkdownEditor::make('commentContent')
                    ->label('Commentaire')
                    ->placeholder('Écrivez votre commentaire ici...')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public function createComment()
    {
        $data = $this->form->getState();

        // Here you would save the comment to the database
        // For now, just a placeholder
        session()->flash('message', 'Commentaire envoyé : ' . $data['commentContent']);

        $this->form->fill(); // Clear the form after submission
    }

    public function clearComment()
    {
        $this->form->fill(); // Clear the form
    }

    public function getFormStatePath(): ?string
    {
        return null;
    }
}
