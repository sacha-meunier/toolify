# Toolify

Application web de veille technologique digitale.

## Sommaire

- [Contexte](#contexte)
- [Problématique](#problématique)
- [Objectifs](#objectifs)
- [Personas](#personas)
- [Fonctionnalités](#fonctionnalités)
- [Stack technique](#stack-technique)
- [Installation](#installation)

## Contexte

La quasi-totalité de la population utilise aujourd'hui des périphériques digitaux au quotidien (smartphones, tablettes, ordinateurs) et dépend d'outils numériques pour accomplir ses tâches : navigateurs, calendriers, boîtes mail, applications de messagerie, outils de prise de notes... Que l'on soit professionnel, étudiant ou simple utilisateur, chacun peut gagner en productivité en utilisant les bons outils pour les bonnes tâches. Encore faut-il savoir qu'ils existent.

## Problématique

La veille technologique est aujourd'hui un exercice difficile, pour plusieurs raisons.

**Surcharge informationnelle.** L'écosystème des outils digitaux est vaste et fragmenté. Des milliers d'applications, services et plateformes se répartissent sur des dizaines de catégories (productivité, design, développement, IA, collaboration...). Personne ne peut connaître l'ensemble des solutions disponibles sur le marché.

**Rythme d'innovation soutenu.** De nouveaux outils sortent chaque jour, ce qui rend la veille continue chronophage. Peu d'utilisateurs ont le temps de découvrir, tester et évaluer chaque nouvel outil susceptible d'améliorer leur workflow.

**Suivi des évolutions complexe.** Les outils existants changent en permanence : mises à jour majeures, nouvelles fonctionnalités, changements de modèle économique. Difficile de savoir si l'outil qu'on utilise depuis un an est toujours le bon choix.

**Absence de plateforme centralisée.** Il n'existe pas de support dédié qui permette de centraliser l'information sur les outils digitaux, de les comparer selon des critères précis, de recevoir des recommandations personnalisées, et de gérer une stack technologique à jour, seul ou en équipe.

Résultat : du temps perdu, des choix d'outils sous-optimaux, et des équipes qui n'exploitent jamais pleinement les innovations disponibles.

## Objectifs

Toolify vise à devenir la plateforme de référence pour découvrir, gérer et suivre les outils digitaux, seul ou en équipe.

- **Catalogue.** Recenser un maximum d'outils digitaux au même endroit, y compris ceux qui ne sont plus maintenus, pour ne jamais perdre la trace de ce qui a existé.
- **Recherche.** Une recherche pertinente avec des filtres (catégories, prix, périphériques...) pour permettre des requêtes aussi bien larges que précises.
- **Stack.** Enregistrer les outils qui ont fait, font ou feront partie de sa stack, en personnel comme en équipe.
- **Veille.** Automatiser et faciliter les recherches récurrentes grâce à des recherches persistantes enrichies de statistiques et de notifications.
- **Architecture.** Organiser les utilisateurs via des espaces de travail et des équipes, pour coller à la structure de vraies organisations.
- **Partage.** Permettre aux éditeurs de mettre en avant les outils qu'ils ont développés.

## Personas

### Marie, freelance en veille personnelle

Marie est designeuse indépendante. Elle utilise Toolify pour trouver une alternative à un outil qu'elle paie trop cher, en comparant plusieurs solutions à partir de filtres de prix et de catégorie. Une fois l'outil retenu, elle l'ajoute à sa stack personnelle.

Elle sauvegarde aussi sa recherche sous forme de veille pour continuer à suivre cette catégorie dans le temps, sans devoir la relancer manuellement. Elle consulte ses notifications régulièrement pour être informée des nouveautés dans les catégories qu'elle suit.

### Karim, administrateur d'un espace de travail

Karim gère l'espace de travail Toolify de son agence. Il crée des équipes selon la structure de l'organisation, invite des membres et centralise les outils validés collectivement dans la stack de l'agence ou de ses équipes.

Il a besoin d'une vue d'ensemble des outils utilisés par ses équipes, sans devoir interroger chaque membre individuellement.

### Léa, membre d'une équipe

Léa fait partie de l'équipe design de son entreprise. Elle consulte les veilles partagées par son équipe, reçoit des notifications sur les nouveautés pertinentes, et ajoute occasionnellement un outil intéressant à la stack de l'équipe. Son usage est tourné vers le suivi et la contribution ponctuelle, pas la configuration.

### Thomas, éditeur d'un outil référencé

Thomas a développé un outil qu'il veut faire connaître. Il configure un listing pour renseigner les informations de son outil et de son entreprise, et les rendre publiques. Sa préoccupation principale : garder la main sur ce qui est visible à son sujet.

## Fonctionnalités

- Recherche d'outils avec filtres (catégorie, prix, plateforme) et historique de recherche
- Fiches outils détaillées, avec ajout direct à la stack
- Stacks personnelles et d'équipe (outils utilisés, envisagés ou abandonnés)
- Veilles : recherches sauvegardées avec statistiques et notifications sur les nouveautés
- Espaces de travail et équipes, avec invitation par mail ou code d'accès
- Listings pour les éditeurs d'outils

## Stack technique

- [Laravel 13](https://laravel.com/) (PHP 8.5)
- [Livewire 4](https://livewire.laravel.com/)
- [Blaze](https://github.com/livewire/blaze), compilateur Blade
- [Laravel Fortify](https://laravel.com/docs/fortify) pour l'authentification
- [Tailwind CSS 4](https://tailwindcss.com/)
- [Pest](https://pestphp.com/) pour les tests
- SQLite en local, MySQL en production

## Installation

Prérequis : PHP 8.5, Composer, Node.js et npm.

```bash
git clone https://github.com/sacha-meunier/toolify.git
cd toolify

composer install
npm install

cp .env.example .env
php artisan key:generate

touch database/database.sqlite
php artisan migrate:fresh --seed

npm run build
```

Pour lancer l'environnement de développement (serveur, queue, logs et Vite en parallèle) :

```bash
composer run dev
```

L'application est ensuite accessible sur `http://localhost` (ou via [Laravel Herd](https://herd.laravel.com/) si vous l'utilisez, à l'adresse `http://toolify.test`).
