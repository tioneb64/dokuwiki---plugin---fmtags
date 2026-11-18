# Fmtags : hashtags

Plugin DokuWiki (`fmtags`).

Nuage de **hashtags** et listes de pages par tag, à partir des tags de l'en-tête des fiches (plugin `fmeta`).

## Utilisation

- `?do=fmtags` ou la balise `<tagcloud>` : nuage de tous les hashtags.
- `<taglist ia projet status=validee>` : liste des pages portant les tags donnés, avec filtre optionnel de statut.
- Les `#hashtags` écrits dans le corps d'une page deviennent des liens vers la liste des pages correspondantes.

## Dépendances

`fmeta`.

## Installation

Copier le contenu de ce dépôt dans `lib/plugins/fmtags/` (le fichier `plugin.info.txt` doit se trouver à la racine de ce dossier), puis vider le cache en rendant la date de `conf/local.php` plus récente (`touch conf/local.php`).

Avec le gestionnaire d'extensions de DokuWiki : *Administration → Gestionnaire d'extensions → Installer*, en indiquant l'adresse du dépôt (archive ZIP).

## Licence

Aucune licence n'a encore été choisie : à ajouter (fichier `LICENSE`) avant de publier le dépôt. DokuWiki est sous GPL-2.0.
