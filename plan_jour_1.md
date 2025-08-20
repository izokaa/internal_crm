# Plan du Jour 1 : Fondations du CRM

Voici le plan d'action pour aujourd'hui. L'objectif est de mettre en place les fonctionnalités de base et d'avoir un Kanban fonctionnel à la fin de la journée.

Suivez les étapes dans l'ordre et exécutez les commandes fournies.

---

### Étape 1 : Configuration de l'application en Français

La première chose à faire est de s'assurer que toute l'interface générée par Filament sera en français.

1.  **Ouvrez le fichier `config/app.php`.**
2.  **Modifiez les valeurs suivantes :**
    *   `'locale' => 'fr',`
    *   `'faker_locale' => 'fr_FR',`

---

### Étape 2 : Création des Modèles et Migrations

Nous allons créer la structure de notre base de données. Exécutez les commandes suivantes dans votre terminal.

1.  **Créer le modèle `Stage` (pour les étapes du Kanban) et sa migration :**
    ```bash
    php artisan make:model Stage -m
    ```

2.  **Créer le modèle `Contact` et sa migration :**
    ```bash
    php artisan make:model Contact -m
    ```

3.  **Créer le modèle `Opportunity` (Opportunité) et sa migration :**
    ```bash
    php artisan make:model Opportunity -m
    ```

4.  **Lancer la migration pour créer les tables dans la base de données :**
    *Avant de lancer cette commande, assurez-vous que votre fichier `.env` est bien configuré pour votre base de données (le fichier `database/database.sqlite` est déjà une bonne option pour démarrer vite).*
    ```bash
    php artisan migrate
    ```

---

### Étape 3 : Définir la structure des tables (migrations)

Maintenant que les fichiers de migration sont créés, nous devons définir les colonnes de chaque table.

1.  **Ouvrez le fichier de migration des `stages`** (il se trouve dans `database/migrations` et se termine par `_create_stages_table.php`).
    *   Ajoutez une colonne pour le nom de l'étape et un ordre d'affichage :
        ```php
        Schema::create('stages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('order')->default(0);
            $table->timestamps();
        });
        ```

2.  **Ouvrez le fichier de migration des `contacts`** (`..._create_contacts_table.php`).
    *   Ajoutez les colonnes pour les informations du contact :
        ```php
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->timestamps();
        });
        ```

3.  **Ouvrez le fichier de migration des `opportunities`** (`..._create_opportunities_table.php`).
    *   Ajoutez les colonnes pour l'opportunité et les liens vers les autres tables :
        ```php
        Schema::create('opportunities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->foreignId('stage_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
        ```

4.  **Relancez la migration** pour appliquer ces nouveaux changements. L'option `--step` permet de revenir en arrière d'une seule étape (la dernière migration) avant de la relancer.
    ```bash
    php artisan migrate:refresh --step=1
    ```

---

### Étape 4 : Génération des interfaces d'administration (Filament)

C'est ici que la magie opère. Nous allons créer les interfaces pour gérer nos données.

1.  **Créez la ressource Filament pour les `Stages` :**
    ```bash
    php artisan make:filament-resource Stage --generate
    ```

2.  **Créez la ressource Filament pour les `Contacts` :**
    ```bash
    php artisan make:filament-resource Contact --generate
    ```

---

### Étape 5 : Création de la page Kanban

Maintenant, la fonctionnalité clé.

1.  **Créez la page Kanban pour les `Opportunités` :**
    ```bash
    php artisan make:filament-page Kanban --resource=Opportunity --type=custom
    ```
    *   Quand on vous le demande (`Which view would you like to create?`), choisissez `[kanban-board]`.

---

### Étape 6 : Peupler la base de données (Seeders)

Pour ne pas avoir une application vide, nous allons créer des données de test.

1.  **Créez les fichiers de Seeder :**
    ```bash
    php artisan make:seeder StageSeeder
    php artisan make:seeder ContactSeeder
    php artisan make:seeder OpportunitySeeder
    ```

2.  **Modifiez le fichier `database/seeders/DatabaseSeeder.php`** pour appeler ces nouveaux seeders :
    ```php
    public function run(): void
    {
        $this->call([
            StageSeeder::class,
            ContactSeeder::class,
            OpportunitySeeder::class,
        ]);
    }
    ```

3.  **Lancez les seeders** pour remplir la base de données :
    ```bash
    php artisan db:seed
    ```

---

### Objectif de fin de journée

À la fin de ces étapes, vous devriez pouvoir :
1.  Vous connecter à votre panneau d'administration Filament.
2.  Créer, voir, modifier et supprimer des "Stages" (Étapes).
3.  Créer, voir, modifier et supprimer des "Contacts".
4.  Voir une page "Kanban" (encore vide et non configurée) dans la section des opportunités.

Demain, nous nous concentrerons sur la configuration fine du Kanban, la logique métier et l'amélioration des formulaires.

**Bon courage ! N'hésitez pas si vous êtes bloqué sur une étape.**
