## Travis CI

[![Build Status](https://travis-ci.org/Easy-Lab/ITCommunity-Front.svg?branch=develop)](https://travis-ci.org/Easy-Lab/ITCommunity-Front)

## Installation

Cloner le projet.

Ensuite, naviguer jusqu'au dossier du projet cloné avec la commande `cd`.

Puis : 

`composer install` (si erreurs, ajouter l'option `--ignore-platform-reqs`)

`npm install`

Si le fichier `.env` n'existe pas : `cp .env.dist .env`

Modifier le fichier `.env`

Puis : 

`sh bin/run`

Maintenir le script `sh bin/watch` en cours dans un onglet du terminal en cas de modifications du scss (local ou pull).
