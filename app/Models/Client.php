<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $primaryKey = 'client_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'client_id',
        'client_name',
        'ic_number',
        'installation_address',
        'phone_number',
        'email_address',
        'payment_method',
        'contract_type',
    ];
    
    /**
     * Get the projects for this client
     */
    public function projects()
    {
        return $this->hasMany(Project::class, 'client_id', 'client_id');
    }
    
    /**
     * Generate a unique client ID
     */
    public static function generateClientId()
    {
        // Get all clients with numeric IDs (pattern: CLI-#####)
        $clients = self::where('client_id', 'LIKE', 'CLI-%')->get();
        
        if ($clients->isEmpty()) {
            return 'CLI-00001';
        }
        
        // Extract numeric parts and find the maximum
        $maxNumber = 0;
        foreach ($clients as $client) {
            // Extract the numeric part after 'CLI-'
            $idPart = substr($client->client_id, 4);
            // Check if it's a valid number
            if (is_numeric($idPart)) {
                $number = (int) $idPart;
                if ($number > $maxNumber) {
                    $maxNumber = $number;
                }
            }
        }
        
        $newNumber = $maxNumber + 1;
        return 'CLI-' . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }
}

