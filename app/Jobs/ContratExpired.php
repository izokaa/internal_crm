<?php

namespace App\Jobs;

use App\Models\Contrat;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Enums\ContratStatus;

class ContratExpired implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $expiredContrats = Contrat::where('date_fin', '<', now()->toDateString())->get();
        $expiredContrats->each(fn ($contrat) => $contrat->update(['status' => ContratStatus::EXPIRED->value]));
    }
}
