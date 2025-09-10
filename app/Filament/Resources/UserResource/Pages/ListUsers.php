<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Mail\InviteUserMail;
use App\Models\Invitation;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;
    protected static ?string $title = "List des utilisateurs";

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('inviteUser')
                ->label('inviter')
                ->form([
                    TextInput::make('email')
                        ->email()
                        ->required()
                ])
                ->action(function (array $data) {
                    $invitation = Invitation::create(['email' => $data['email']]);

                    // TODO: Send email inviation.
                    Mail::to($invitation->email)->send(new InviteUserMail($invitation));

                    Notification::make('invitedSuccess')
                        ->body("L'invitation a été bien envoyé")
                        ->success();
                })
        ];
    }
}
