<?php

namespace App\Livewire;

use App\Models\Activity;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\TextEntry;
use Livewire\Component;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Log;

class ActivityActions extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    public function render()
    {
        return view('livewire.activity-actions');
    }


    public function createTaskAction(): Action
    {
        return Action::make('createTask')
            ->label('Nouvelle Tâche')
            ->form([
                TextInput::make('name')
                    ->label('Your Name'),
            ])
            ->modalSubmitActionLabel('Save')
            ->action(function (array $data): void {
                // Handle form submission if using a form
                Log::info($data);
            });
    }




}
