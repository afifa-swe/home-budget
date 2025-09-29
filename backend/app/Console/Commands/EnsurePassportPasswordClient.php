<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Passport\ClientRepository;

class EnsurePassportPasswordClient extends Command
{
    protected $signature = 'passport:ensure-password-client';
    protected $description = 'Ensure a Passport password grant client exists; prints id and secret';

    public function handle()
    {
        $repo = new ClientRepository();

        // Try to find an existing password client
        $client = $repo->personalAccessClient();

        // personalAccessClient is not password client; instead, look into clients for password type
        $clients = \DB::table('oauth_clients')->where('password_client', true)->get();
        if ($clients->isEmpty()) {
            $this->info('No password grant client found. Creating one...');
            $clientModel = $repo->createPasswordGrantClient(null, 'Password Grant Client', 'http://localhost');
            $this->info('Created password client:');
            $this->line('ID: '.$clientModel->id);
            $this->line('Secret: '.$clientModel->secret);
        } else {
            $clientModel = $clients->first();
            $this->info('Found existing password client:');
            $this->line('ID: '.$clientModel->id);
            $this->line('Secret: '.$clientModel->secret);
        }

        return 0;
    }
}
