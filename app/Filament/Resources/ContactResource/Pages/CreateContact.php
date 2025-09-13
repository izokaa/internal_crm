<?php

namespace App\Filament\Resources\ContactResource\Pages;

use App\Filament\Resources\ContactResource;
use Bunny\Storage\Client;
use Bunny\Storage\Region;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CreateContact extends CreateRecord
{
    protected static string $resource = ContactResource::class;


    protected function handleRecordCreation(array $data): Model
    {

        if (isset($data['profile_picture'])) {
            $filePath = basename($data['profile_picture']);

            $client = new Client(env('FTP_PASSWORD'), env('FTP_USERNAME'), Region::FALKENSTEIN);

            $client->upload(Storage::disk('public')->path($data['profile_picture']), $filePath);

            $data['profile_picture'] = $filePath;

            return static::getModel()::create($data);
        }
    }
}
