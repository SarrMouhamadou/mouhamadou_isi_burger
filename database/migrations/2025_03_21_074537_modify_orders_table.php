<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Étape 1 : Supprimer la contrainte existante sur la colonne status
        DB::statement("ALTER TABLE orders DROP CONSTRAINT orders_status_check");

        // Étape 2 : Supprimer la valeur par défaut de la colonne status
        DB::statement("ALTER TABLE orders ALTER COLUMN status DROP DEFAULT");

        // Étape 3 : Mettre à jour les valeurs existantes de la colonne status
        DB::statement("UPDATE orders SET status = 'En attente' WHERE status = 'en_attente'");
        DB::statement("UPDATE orders SET status = 'En préparation' WHERE status = 'en_preparation'");
        DB::statement("UPDATE orders SET status = 'Prête' WHERE status = 'prete'");
        DB::statement("UPDATE orders SET status = 'Payée' WHERE status = 'payee'");

        // Étape 4 : Créer un type enum personnalisé pour le status
        DB::statement("CREATE TYPE order_status AS ENUM ('En attente', 'En préparation', 'Prête', 'Payée', 'Annulée')");

        // Étape 5 : Modifier la colonne status pour utiliser le nouveau type enum
        DB::statement("ALTER TABLE orders ALTER COLUMN status TYPE order_status USING (status::order_status)");

        // Étape 6 : Redéfinir la valeur par défaut et la contrainte NOT NULL
        DB::statement("ALTER TABLE orders ALTER COLUMN status SET DEFAULT 'En attente'");
        DB::statement("ALTER TABLE orders ALTER COLUMN status SET NOT NULL");

        // Étape 7 : Gérer les lignes avec user_id à null
        // Option 1 : Supprimer les lignes avec user_id à null
        DB::statement("DELETE FROM orders WHERE user_id IS NULL");

        // Option 2 : (Alternative) Si tu veux conserver ces lignes, attribue un user_id par défaut
        // DB::statement("UPDATE orders SET user_id = 1 WHERE user_id IS NULL"); // Remplace 1 par un user_id valide

        // Étape 8 : Rendre user_id non nullable
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
        });

        // Étape 9 : Supprimer les colonnes customer_name et customer_email
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['customer_name', 'customer_email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Étape 1 : Supprimer la valeur par défaut de la colonne status
        DB::statement("ALTER TABLE orders ALTER COLUMN status DROP DEFAULT");

        // Étape 2 : Restaurer la colonne status comme varchar
        DB::statement("ALTER TABLE orders ALTER COLUMN status TYPE varchar(255) USING (status::varchar)");

        // Étape 3 : Restaurer les anciennes valeurs de status
        DB::statement("UPDATE orders SET status = 'en_attente' WHERE status = 'En attente'");
        DB::statement("UPDATE orders SET status = 'en_preparation' WHERE status = 'En préparation'");
        DB::statement("UPDATE orders SET status = 'prete' WHERE status = 'Prête'");
        DB::statement("UPDATE orders SET status = 'payee' WHERE status = 'Payée'");

        // Étape 4 : Redéfinir la valeur par défaut et ajouter l'ancienne contrainte CHECK
        DB::statement("ALTER TABLE orders ALTER COLUMN status SET DEFAULT 'en_attente'");
        DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_status_check CHECK (status IN ('en_attente', 'en_preparation', 'prete', 'payee'))");

        // Étape 5 : Supprimer le type enum personnalisé
        DB::statement("DROP TYPE IF EXISTS order_status");

        // Étape 6 : Rendre user_id nullable
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
        });

        // Étape 7 : Restaurer les colonnes customer_name et customer_email
        Schema::table('orders', function (Blueprint $table) {
            $table->string('customer_name');
            $table->string('customer_email');
        });
    }
};
