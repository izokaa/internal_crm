# Projet CRM en Filament v^3.3

Ce projet est un petit CRM (Customer Relationship Management) pour la gestion commerciale de la partie **front-office** d'une entreprise.

## Fonctionnalités clés

1. Gestion de contacts et de contrats.
3. Gestion et Suivi des opportunities.
4. Un calendrie modern pour visulaiser et programmer des activités(Appels, Tâches, Evènements).
5. Un affichage kanban pour les opportunities.
6. Un système d'historique des évènements et de commentaires.
7. Paramètres.
8. Tableau de board.

## Installation

**Note**:

*Il faut Respecter l'ordre de l'installation !!*

1. `git clone git@github.com:izokaa/internal_crm.git`
2. `npm install`
3. `composer install`
4. `npm run build && npm run dev`
5. `cp .env.example .env`
6. `php artisan migrate`
7. `php artisan db:seed`
8. `php artisan server`
9. `php shield:super-admin`
10. `php shield:setup`
11. `php artisan migrate`
12. `php artisan shield:generate --all`

* login :

**Email**: `admin@gmail.com`
**Password**: `password`

## Tech stack

Framework Filament v^3.3.

**I am realy bad at documenting this**
