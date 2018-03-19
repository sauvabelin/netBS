# NetBS
Bienvenue sur le système de gestion d'organisation de la brigade de Sauvabelin.
Cette application a pour but de rendre plus facile la gestion des membres
dans tout type de structure organisée de manière hierarchique.

## Ce qui est cool
* Prêt à l'usage directement, avec un script d'installation facile et rapide
* Hautement customizable pour s'adapter au plus de besoins possibles tout
en restant performant et agréable à utiliser
* Exportation en Excel et PDF et génération d'étiquettes
* Génération de listes dynamiques de membres par utilisateur

### Concrètement
Le projet est codé en PHP, basé sur Symfony et utilisant Doctrine ORM pour
l'abstraction base de données. Nous avons séparé les fonctionnalités dans
4 bundles différents
* NetBSCoreBundle, qui fournis des fonctionnalités de base à l'application
* NetBSFichierBundle qui s'occupe de la gestion des membres, groupes etc. à
proprement parlé
* NetBSSecureBundle qui offre une couche de sécurité hautement customizable
pour accéder à l'application
* NetBSListBundle qui s'occupe de générer et afficher des listes en tout genre
un peu partout

### Qu'est-ce qui est en développement
* Les envois massifs d'email automatiques, avec la possibilité pour les
utilisateurs de s'abonner/désabonner de certaines listes de publication

## Comment ça marche
Initialement, le projet est développé pour gérer l'effectif d'un groupe scout,
mais peut facilement être adapté pour d'autres usages.

L'application est construite autour de la notion de groupe, entité qui réunit
des membres autour de quelque chose. Dans le cas des scouts, c'est toute la
structure de l'organisation qui est représentée sous forme de groupes, avec
les patrouilles, troupes, sizaines, branches etc. Chaque groupe possède des
propriétés qui lui donnent ses particularités.

Les membres font ensuite partie de ces groupes au travers d'attributions, qui
leur associent une date de début et de fin potentielle, ainsi qu'une fonction
qu'ils exercent dans ce groupe. Les fonctions leurs donnent ensuite les
autorisations d'exécuter certaines actions ou d'accéder à certaines parties
de l'application par exemple.

## installation
1. faites un `git clone` de ce repository
2. Ouvrez un terminal à l'intérieur du dossier ainsi cloné, et utilisez
[composer](https://getcomposer.org/) pour faire un `composer install`
3. Une fois l'installation terminée, assurez-vous que les paramètres de
base de donnée soient corrects.
4. Importez les routes de l'application en ajoutant à votre fichier `routing.yml`
```yaml
netbs:
    resource: '@NetBSCoreBundle/Resources/config/routing.yml'
    prefix: /netBS
```
5. Lancez le script d'installation en faisant
`php bin/console netbs:install --purge=1 --dummy=1`, où `--purge` indique
de nettoyer la base de données et construire le schéma, et `--dummy` d'y
charger quelques fausses données pour essayer.
6. Connectez-vous en accédant à `/netBS/secure/login` avec `admin` et `password`