<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'customer_name', 'customer_email', 'status', 'total_amount'];

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array
     */
    protected $casts = [
        'total_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Les statuts possibles pour une commande.
     *
     * @var array
     */
    public const STATUSES = [
        'En attente',
        'En préparation',
        'Prête',
        'Payée',
        'Annulée',
    ];

    /**
     * Relation avec l'utilisateur (client).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec les burgers (plusieurs burgers par commande).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function burgers()
    {
        return $this->belongsToMany(Burger::class, 'order_burger')
                    ->withPivot('quantity', 'unit_price')
                    ->withTimestamps();
    }

    /**
     * Relation avec le paiement (une commande peut avoir un paiement).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Vérifie si le statut donné est valide.
     *
     * @param string $status
     * @return bool
     */
    public static function isValidStatus($status)
    {
        return in_array($status, self::STATUSES);
    }
}
