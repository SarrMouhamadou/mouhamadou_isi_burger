<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdatePaymentsTableStatus extends Migration
{
    public function up()
    {
        // Étape 1 : Créer un nouveau type enum pour payments
        DB::statement("CREATE TYPE payment_status_new AS ENUM ('En attente', 'Payée', 'Annulée')");

        // Étape 2 : Supprimer la valeur par défaut actuelle de la colonne status
        DB::statement("ALTER TABLE payments ALTER COLUMN status DROP DEFAULT");

        // Étape 3 : Convertir les anciennes valeurs de status
        DB::statement("
            ALTER TABLE payments
            ALTER COLUMN status TYPE payment_status_new
            USING (
                CASE
                    WHEN status = 'pending' THEN 'En attente'::payment_status_new
                    ELSE 'Annulée'::payment_status_new
                END
            )
        ");

        // Étape 4 : Redéfinir la valeur par défaut pour le nouveau type
        DB::statement("ALTER TABLE payments ALTER COLUMN status SET DEFAULT 'En attente'");

        // Étape 5 : Supprimer l'ancien type (si applicable)
        DB::statement("DROP TYPE IF EXISTS payment_status");

        // Étape 6 : Renommer le nouveau type
        DB::statement("ALTER TYPE payment_status_new RENAME TO payment_status");
    }

    public function down()
    {
        // Étape 1 : Créer un type varchar pour revenir à l'ancien type
        DB::statement("ALTER TABLE payments ALTER COLUMN status DROP DEFAULT");

        // Étape 2 : Convertir les valeurs actuelles en varchar
        DB::statement("
            ALTER TABLE payments
            ALTER COLUMN status TYPE varchar
            USING (
                CASE
                    WHEN status = 'En attente' THEN 'pending'
                    ELSE 'pending'
                END
            )
        ");

        // Étape 3 : Supprimer le type enum actuel
        DB::statement("DROP TYPE IF EXISTS payment_status");

        // Étape 4 : Redéfinir la valeur par défaut
        DB::statement("ALTER TABLE payments ALTER COLUMN status SET DEFAULT 'pending'");
    }
}
