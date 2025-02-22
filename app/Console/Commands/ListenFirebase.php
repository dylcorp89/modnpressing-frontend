<?php

namespace App\Console\Commands;

use Notification;

use Kreait\Firebase\Factory;
use App\Events\NewOrderAdded;
use Illuminate\Console\Command;
use App\Notifications\NewOrderNotification;



class ListenFirebase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:listen-firebase';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $firebase = (new Factory)
            ->withServiceAccount(config('firebase.credentials'))
            ->createDatabase();

        $database = $firebase->getReference('commandes'); // Chemin des commandes dans Firebase

        // $database->getSnapshot()
        //     ->getChildAdded(function ($snapshot) {
        //         $orderData = $snapshot->getValue();

        //         // Déclenchez un événement Laravel ou traitez la commande ici
        //         event(new NewOrderAdded($orderData));

        //         // // Ou envoyez une notification directement
        //         \Notification::route('mail', 'admin@example.com')
        //             ->notify(new NewOrderNotification($orderData));

        //         $this->info('Nouvelle commande détectée : ' . json_encode($orderData));
        //     });
    }
}
