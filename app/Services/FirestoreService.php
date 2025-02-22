<?php

namespace App\Services;

use Exception;
use Kreait\Firebase\Factory;
use InvalidArgumentException;
use Kreait\Firebase\Database;

use Illuminate\Support\Facades\Log;

class FirestoreService
{
    protected $database;
    protected $storage;
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
        $this->storage = $firebase->createStorage();
    }

    /**
     * Ajouter des données à une référence.
     *
     * @param string $reference
     * @param array $data
     * @return string L'ID généré.
     */
    public function addData(string $reference, array $data): string
    {
        try {
            $newReference = $this->database->getReference($reference)->push($data);
            return $newReference->getKey(); // Retourne l'ID généré automatiquement
        } catch (Exception $e) {
            throw new Exception("Erreur lors de l'ajout de données à Firebase Realtime Database : " . $e->getMessage());
        }
    }

    /**
     * Récupérer toutes les données à une référence.
     *
     * @param string $reference
     * @return array|null
     */
    public function getData(string $reference): ?array
    {
        try {
            $snapshot = $this->database->getReference($reference)->getSnapshot();
            return $snapshot->exists() ? $snapshot->getValue() : null;
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la récupération des données de Firebase Realtime Database : " . $e->getMessage());
        }
    }



/**
 * Récupérer tous les champs d'une entrée en connaissant son ID.
 *
 * @param string $reference La référence de la base de données (par exemple, 'api').
 * @param string $id L'ID spécifique de la donnée.
 * @return array|null
 */
public function getAllFieldsById(string $reference, string $id): ?array
{
    try {
        // Accéder à la référence de la base de données avec l'ID spécifique
        $snapshot = $this->database->getReference($reference . '/' . $id)->getSnapshot();

        // Vérifier si la donnée existe sous cet ID
        if ($snapshot->exists()) {
            // Retourner tous les champs sous cet ID
            return $snapshot->getValue(); // Cela retournera un tableau avec toutes les données sous cet ID
        }

        return null;
    } catch (Exception $e) {
        throw new Exception("Erreur lors de la récupération des données de Firebase Realtime Database : " . $e->getMessage());
    }
}


/**
 * Récupérer une entrée par un champ spécifique (comme "identifiant").
 *
 * @param string $reference La référence de la base de données (par exemple, 'utilisateurs').
 * @param string $field Le champ sur lequel effectuer la recherche (par exemple, 'email').
 * @param string $value La valeur recherchée dans le champ spécifié.
 * @return array|null Les données associées ou null si aucune correspondance n'est trouvée.
 */
public function getAllFieldsByField(string $reference, string $field, string $value): ?array
{
    // Valider les paramètres d'entrée
    if (empty($reference) || empty($field) || empty($value)) {
        throw new InvalidArgumentException("Les paramètres 'reference', 'field', et 'value' doivent être renseignés.");
    }

    try {
        // Accéder à la référence principale
        $snapshot = $this->database->getReference($reference)->getSnapshot();

        if (!$snapshot->exists()) {
            throw new Exception("La référence '{$reference}' est vide ou inexistante.");
        }

        // Parcourir chaque élément sous la référence principale
        foreach ($snapshot->getValue() ?? [] as $key => $data) {
            // Naviguer dans la hiérarchie des champs
            $fieldPath = explode('/', $field);
            $currentData = $data;

            foreach ($fieldPath as $segment) {
                if (isset($currentData[$segment])) {
                    $currentData = $currentData[$segment];
                } else {
                    $currentData = null;
                    break;
                }
            }

            // Vérifier si la valeur correspond
            if ((string) $currentData === (string) $value) {
                return $data; // Retourner toutes les données correspondant à la valeur
            }
        }

        return null; // Aucune correspondance trouvée
    } catch (Exception $e) {
        throw new Exception("Erreur lors de la recherche dans Firebase Realtime Database : " . $e->getMessage());
    }
}



    /**
     * Mettre à jour des données à une référence.
     *
     * @param string $reference
     * @param array $data
     * @return void
     */
    public function updateData(string $reference, array $data, string $id): void
    {
        try {
            // Accéder à la référence Firebase et mettre à jour les données
            $this->database->getReference($reference . '/' . $id)->update($data);
        } catch (Exception $e) {
            // Lancer une exception avec un message détaillé en cas d'erreur
            throw new Exception("Erreur lors de la mise à jour des données dans Firebase Realtime Database : " . $e->getMessage());
        }
    }


    /**
 * Supprimer une référence spécifique.
 *
 * @param string $reference
 * @param string $id
 * @return void
 */
public function deleteData(string $reference, string $id): void
{
    try {
        // Delete the child node using the reference and id
        $this->database->getReference($reference . '/' . $id)->remove();
    } catch (Exception $e) {
        throw new Exception("Erreur lors de la suppression de données dans Firebase Realtime Database : " . $e->getMessage());
    }
}

 /**
     * Récupère le nombre de clients dans la base Firebase Realtime Database.
     */
    public function getClientsCount(): int
    {
        try {
            $path = storage_path(env('FIREBASE_CREDENTIALS'));

            if (!file_exists($path)) {
                throw new Exception("Le fichier de configuration Firebase n'existe pas : {$path}");
            }

            $firebase = (new Factory)
                ->withServiceAccount($path)
                ->withDatabaseUri(env('FIREBASE_DATABASE_URI'));

            $auth = $firebase->createAuth();
            $users = $auth->listUsers() ?? [];
          //  dd($auth);
            $usersArray = iterator_to_array($users);
            $totalItems = count($usersArray);


            return $totalItems;
        } catch (\Exception $e) {
            Log::error('Erreur Firebase (Clients): ' . $e->getMessage());
            return 0;
        }
    }



    /**
     * Récupère le nombre de commandes dans la base Firebase Realtime Database.
     */
    public function getCommandesCount(): int
    {
        try {
            $snapshot = $this->database->getReference('commandes')->getSnapshot();
            return count($snapshot->getValue() ?? []);
        } catch (\Exception $e) {
            Log::error('Erreur Firebase (Commandes): ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Récupère le nombre de réclamations dans la base Firebase Realtime Database.
     */
    public function getReclamationsCount(): int
    {
        try {
            $snapshot = $this->database->getReference('reclamations')->getSnapshot();
            return count($snapshot->getValue() ?? []);
        } catch (\Exception $e) {
            Log::error('Erreur Firebase (Réclamations): ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Récupère toutes les statistiques.
     */
    public function getAllStats(): array
    {
        return [
            'clients' => $this->getClientsCount(),
            'commandes' => $this->getCommandesCount(),
            'reclamations' => $this->getReclamationsCount(),
        ];
    }



    public function getUserByEmail($email)
    {
        $users = $this->database->getReference('utilisateurs')->getValue();

        foreach ($users as $id => $user) {
            if (isset($user['email']) && $user['email'] === $email) {
                return array_merge(['id' => $id], $user);
            }
        }

        return null;
    }



   /**
     * Ajouter des promotions à Firebase Realtime Database.
     *
     * @param array $promotionData
     * @return string L'ID généré de la promotion.
     */
    public function addPromotion(array $promotionData): string
    {
        try {
            // Ajouter les informations de la promotion dans la base de données
            $newReference = $this->database->getReference('promotions')->push($promotionData);
            return $newReference->getKey(); // Retourner l'ID généré
        } catch (Exception $e) {
            throw new Exception("Erreur lors de l'ajout de la promotion : " . $e->getMessage());
        }
    }

    /**
     * Télécharger une image dans Firebase Storage et retourner l'URL de l'image.
     *
     * @param string $imagePath Chemin du fichier local.
     * @param string $imageName Nom du fichier dans Firebase Storage.
     * @return string URL de l'image téléchargée.
     */
    public function uploadImageToStorage(string $imagePath, string $imageName): string
    {
        try {
            $storageBucket = $this->storage->getBucket();
            $file = fopen($imagePath, 'r');

            // Télécharger l'image dans Firebase Storage
            $object = $storageBucket->upload($file, [
                'name' => 'promotions/' . $imageName,
            ]);

            // Retourner l'URL publique de l'image
            return 'promotions/' . $imageName;
        } catch (Exception $e) {
            throw new Exception("Erreur lors de l'upload de l'image : " . $e->getMessage());
        }
    }

    /**
     * Récupérer toutes les promotions depuis Firebase Realtime Database.
     *
     * @return array|null
     */
    public function getPromotions(): ?array
    {
        try {
            $snapshot = $this->database->getReference('promotions')->getSnapshot();
            return $snapshot->exists() ? $snapshot->getValue() : null;
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la récupération des promotions : " . $e->getMessage());
        }
    }

    /**
     * Récupérer l'image d'une promotion depuis Firebase Storage.
     *
     * @param string $imageName Nom du fichier d'image dans Firebase Storage.
     * @return string URL de l'image.
     */
    public function getImageFromStorage(string $imageName): string
    {
        try {
            $storageBucket = $this->storage->getBucket();
            $object = $storageBucket->object('promotions/' . $imageName);

            // Vérifier si l'image existe
            if ($object->exists()) {
                return $object->info()['mediaLink'];
            }

            return ''; // Image not found
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la récupération de l'image : " . $e->getMessage());
        }
    }


    // In FirestoreService class
public function updateUserPoints(string $userId, int $points): void
{
    try {
        // Reference to the user's data in the Firebase Realtime Database
        $reference = 'users/' . $userId;

        // Update the user's points
        $this->updateData($reference, ['point' => $points], $userId);
    } catch (Exception $e) {
        throw new Exception("Erreur lors de la mise à jour des points de l'utilisateur : " . $e->getMessage());
    }
}


}
