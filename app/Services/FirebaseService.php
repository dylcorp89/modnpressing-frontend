<?php

namespace App\Services;

use Exception;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Database;

class FirebaseService
{
    protected $database;

    public function __construct()
    {
         // Chemin des credentials Firebase
         $path = storage_path(env('FIREBASE_CREDENTIALS'));

         if (!file_exists($path)) {
             throw new Exception("Le fichier de configuration Firebase n'existe pas : {$path}");
         }

         // Initialisation de Realtime Database via Firebase Admin SDK
         $firebase = (new Factory)
             ->withServiceAccount($path)
             ->withDatabaseUri(env('FIREBASE_DATABASE_URI'));

             $this->database = $firebase->createDatabase();
    }

    public function listenToRealtimeDatabase($path, $callback)
    {
        $reference = $this->database->getReference($path);
        $listener = $reference->getSnapshot()->getValue();

        // Simulez l'écoute en boucle (à adapter selon vos besoins)
        while (true) {
            $currentData = $reference->getSnapshot()->getValue();
            if ($currentData !== $listener) {
                $callback($currentData); // Appelez le callback en cas de changement
                $listener = $currentData;
            }

            sleep(1); // Pause pour limiter les requêtes
        }
    }
}
