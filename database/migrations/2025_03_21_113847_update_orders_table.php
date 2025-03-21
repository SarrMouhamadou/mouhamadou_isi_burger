<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateOrdersTable extends Migration
{
    public function up()
    {
        // Étape 1 : Ajouter la colonne user_id si elle n'existe pas
        if (!Schema::hasColumn('orders', 'user_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->foreignId('user_id')->constrained()->onDelete('cascade')->after('id');
            });
        }

        // Étape 2 : Rendre customer_name et customer_email nullable (si elles existent)
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'customer_name')) {
                $table->string('customer_name')->nullable()->change();
            }
            if (Schema::hasColumn('orders', 'customer_email')) {
                $table->string('customer_email')->nullable()->change();
            }
        });

        // Étape 3 : Renommer la colonne total en total_amount
        if (Schema::hasColumn('orders', 'total')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->renameColumn('total', 'total_amount');
            });
        }

        // Étape 4 : Mettre à jour la colonne status (enum)
        // 4.1 : Créer un nouveau type enum avec les nouvelles valeurs
        DB::statement("CREATE TYPE order_status_new AS ENUM ('En attente', 'En préparation', 'Prête', 'Payée', 'Annulée')");

        // 4.2 : Supprimer la valeur par défaut actuelle de la colonne status
        DB::statement("ALTER TABLE orders ALTER COLUMN status DROP DEFAULT");

        // 4.3 : Appliquer le nouveau type enum directement (puisque les valeurs sont déjà correctes)
        DB::statement("
            ALTER TABLE orders
            ALTER COLUMN status TYPE order_status_new
            USING (status::text::order_status_new)
        ");

        // 4.4 : Redéfinir la valeur par défaut pour le nouveau type
        DB::statement("ALTER TABLE orders ALTER COLUMN status SET DEFAULT 'En attente'");

        // 4.5 : Supprimer l'ancien type enum (le type original s'appelle probablement "orders_status")
        DB::statement("DROP TYPE IF EXISTS orders_status");

        // 4.6 : Renommer le nouveau type enum pour remplacer l'ancien
        DB::statement("ALTER TYPE order_status_new RENAME TO orders_status");
    }

    public function down()
    {
        // Étape 1 : Revenir sur la mise à jour de la colonne status
        // 1.1 : Créer un type enum avec les anciennes valeurs
        DB::statement("CREATE TYPE order_status_old AS ENUM ('en_attente', 'en_preparation', 'prete', 'payee')");

        // 1.2 : Supprimer la valeur par défaut actuelle
        DB::statement("ALTER TABLE orders ALTER COLUMN status DROP DEFAULT");

        // 1.3 : Convertir les nouvelles valeurs de status vers les anciennes
        DB::statement("
            ALTER TABLE orders
            ALTER COLUMN status TYPE order_status_old
            USING (
                CASE
                    WHEN status = 'En attente' THEN 'en_attente'::order_status_old
                    WHEN status = 'En préparation' THEN 'en_preparation'::order_status_old
                    WHEN status = 'Prête' THEN 'prete'::order_status_old
                    WHEN status = 'Payée' THEN 'payee'::order_status_old
                    ELSE 'en_attente'::order_status_old
                END
            )
        ");

        // 1.4 : Redéfinir la valeur par défaut pour l'ancien type
        DB::statement("ALTER TABLE orders ALTER COLUMN status SET DEFAULT 'en_attente'");

        // 1.5 : Supprimer le type enum actuel
        DB::statement("DROP TYPE IF EXISTS orders_status");

        // 1.6 : Renommer l'ancien type pour le restaurer
        DB::statement("ALTER TYPE order_status_old RENAME TO orders_status");

        // Étape 2 : Revenir sur le renommage de total_amount en total
        if (Schema::hasColumn('orders', 'total_amount')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->renameColumn('total_amount', 'total');
            });
        }

        // Étape 3 : Revenir sur customer_name et customer_email (si elles existent)
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'customer_name')) {
                $table->string('customer_name')->nullable(false)->change();
            }
            if (Schema::hasColumn('orders', 'customer_email')) {
                $table->string('customer_email')->nullable(false)->change();
            }
        });

        // Étape 4 : Supprimer la colonne user_id si elle existe
        if (Schema::hasColumn('orders', 'user_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }
    }
}
