# API de facturation (Laravel 12)

API REST et back-office de gestion de clients et de factures, construits avec Laravel, Sanctum, Filament et MySQL.
Chaque utilisateur ne peut accéder qu'à ses propres données.

## Points techniques

- Authentification par jetons (Laravel Sanctum)
- Isolation des données entre utilisateurs (un client ou une facture d'un autre utilisateur renvoie 404)
- Montants stockés en **centimes** (entiers) pour éviter les erreurs d'arrondi
- Création de facture et de ses lignes dans une **transaction**
- Validation des entrées et réponses JSON via des API Resources
- Pagination des listes
- 15 tests automatisés (PHPUnit), dont les tests de sécurité entre utilisateurs
- Back-office Filament avec lignes de facture dynamiques

## Back-office d'administration (Filament)

Interface web disponible sur `/admin` pour gérer clients et factures :

- Lignes de facture dynamiques, prix saisis et affichés en euros (stockés en centimes)
- Numérotation automatique des factures
- Chaque utilisateur ne voit que ses propres clients et factures

## Installation

Prérequis : PHP 8.4+, Composer, MySQL.

```bash
git clone https://github.com/sofianetlm/facturation-api.git
cd facturation-api
composer install
cp .env.example .env
php artisan key:generate
```

Créez une base `facturation`, configurez `.env`, puis :

```bash
php artisan migrate --seed --seeder=DemoSeeder
php artisan serve
```

- API : `http://127.0.0.1:8000/api`
- Back-office : `http://127.0.0.1:8000/admin`

Compte de démonstration : `demo@example.com` / `password`

## Tests

```bash
php artisan test
```

## Endpoints

Toutes les routes, sauf `register` et `login`, exigent l'en-tête `Authorization: Bearer <token>`.
Ajoutez toujours `Accept: application/json`.

| Méthode | Route | Description |
|---|---|---|
| POST | `/api/register` | Inscription |
| POST | `/api/login` | Connexion, renvoie un jeton |
| POST | `/api/logout` | Déconnexion |
| GET | `/api/me` | Utilisateur connecté |
| GET, POST | `/api/clients` | Lister, créer des clients |
| GET, PUT, DELETE | `/api/clients/{id}` | Voir, modifier, supprimer un client |
| GET, POST | `/api/invoices` | Lister (filtre `?status=`), créer des factures |
| GET, PATCH, DELETE | `/api/invoices/{id}` | Voir, modifier le statut, supprimer |

### Exemple : créer une facture

```bash
curl -X POST http://127.0.0.1:8000/api/invoices \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{
    "client_id": 1,
    "issued_at": "2026-10-01",
    "items": [
      {"description": "Audit du code", "quantity": 2, "unit_price": 30000}
    ]
  }'
```

Réponse (extrait) : `"status": "draft"`, `"total": 60000` (soit 600,00 €).

## Pistes d'amélioration

- Numérotation des factures par séquence et par utilisateur
- Export PDF des factures
- Documentation OpenAPI
- Gestion des rôles pour restreindre l'accès au back-office

## Licence

Ce projet est distribué sous licence MIT (voir le fichier [LICENSE](LICENSE)).

## Auteur

Soufiane, développeur Laravel, disponible pour des missions en sous-traitance (français, arabe).
Contact : [sofianebouzina7@gmail.com]