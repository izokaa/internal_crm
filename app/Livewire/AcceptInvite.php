<?php

namespace App\Livewire;

use App\Models\Invitation;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Pages\Dashboard;
use Filament\Pages\SimplePage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;

class AcceptInvite extends SimplePage
{
    use InteractsWithForms;

    protected static string $view = 'livewire.accept-invite';

    public int $invitationId;
    private Invitation $invitation;
    public array $data = [];

    public function mount()
    {
        $this->invitation = Invitation::findOrFail($this->invitationId);
        $this->form->fill([
            'email' => $this->invitation->email
        ]);
    }

    public function getHeading(): string
    {
        return 'Accpeter l\'invitation ';
    }

    public function hasLogo(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
            ->label('Nom complet')
                ->required(),
            TextInput::make('email')
                ->label('Adresse email')
                ->disabled()
            ,
            TextInput::make('password')
                ->label('Mot de passe')
                ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                ->rule(Password::default())
                ->password()
                ->same('passwordConfirmation')
                ->required(),
            TextInput::make('passwordConfirmation')
                ->label('Confirmation de mot de passe')
                ->password()
                ->dehydrated(false)
                ->required(),
        ])->statePath('data');
    }

    public function create()
    {
        $this->invitation = Invitation::findOrFail($this->invitationId);

        $user = User::create([
            'name' => $this->form->getState()['name'],
            'email' => $this->invitation->email,
            'password' => $this->form->getState()['password']
        ]);

        auth()->login($user);

        $this->invitation->delete();

        $this->redirect(Dashboard::getUrl());

    }


}
