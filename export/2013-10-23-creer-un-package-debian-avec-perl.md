---
title: "Créer un package debian avec Perl"
date: "2013-10-23"
categories: 
  - "languages"
  - "perl"
tags: 
  - "debian"
  - "packaging"
  - "perl"
---

## Créer un module "CPAN-compatible"

1. Utiliser l'utilitaire module-starter pour créer la structure de base : `module-starter --module=MyModule::Name \ --author="Your Name" --email=theemail@domain.net -mb` le flag _\-mb_ spécifie qu'on utilisera Module::Builder. Il est possible de spécifier _\-mi_ à la place, ce qui générera un Makefile.PL utilisé par Module::Install.
2. Copier les .pm dans lib. Si des scripts sont présents (à installer dans bin plutôt que perl), il faut créer un dossier `scripts` et y déposer les scripts .pl en omettant l'extension.
3. Editer le fichier Build.PL (on part du principe qu'on utilise Module::Builder). Pour un module possédant des scripts et des modules, nous pourrions avoir, par exemple, la configuration suivante:

    ```perl
    use 5.006;
    use strict;
    use warnings FATAL => 'all';
    use Module::Build;

    my $builder = Module::Build->new(
        module_name         => 'CybeSync',
        license             => 'perl',
        dist_author         => q{lucy linder <lucy.derlin@gmail.com>},
        dist_version_from   => 'lib/CybeSync.pm',
        dist_abstract       => 'Cyberlearn Sync Commandline Tool',
        version_from        => 'lib/CybeSync.pm',
        abstract_from       => 'script/cybe',
        release_status      => 'stable',
        configure_requires => {
            'Module::Build' => 0,
        },
        build_requires => {
            'Test::More' => 0
        },
        requires => {
            'Term::ReadLine' => 0,
            'Term::ReadKey' => 0,
            'Data::Dumper' => 0,
            'Carp' => 0,
            'JSON' => 0,
            'Pod::Usage' => 0,
            'LWP::UserAgent' => 0,
            'LWP::Protocol::https' => 0,
            'HTTP::Request' => 0,
            'HTTP::Cookies' => 0,
            'URI::Escape' => 0,
            'File::Spec' => 0,
            'HTML::TokeParser::Simple' => 0,
            'Cwd' => 0
        },
        script_files       => ['scripts/cybe'], 
        add_to_cleanup     => [ 'CybeSync-*' ],
        create_makefile_pl => 'traditional',
        create_readme      => 1
    );

    $builder->create_build_script();</lucy.derlin@gmail.com>
    ```
    À noter les choses suivantes:
    - `script_files` : permet de spécifier les scripts additionnels, à ne metter que si on en a.
    - `create_makefile_pl`: permet de créer le Makefile.PL (normalement obligatoire pour un module CPAN) grâce à la commande `./Build distmeta`
    - `version_from`: pour créer ensuite des paquets debian, il est plus judicieux d'utiliser `version` et de le mettre à jour à la main
4. Pour générer le script de Build, il suffit de taper `perl Build.PL` dans la console. Cela génère un script exécutable nommé Build, qui possède de nombreuses options intéressantes, telles que distmeta (crée entre autres le Makefile.PL), fakeinstall (simule une installation), dist (créer le tarball)...

### Créer le paquet debian

1. Créer le tarball original : `./Build dist`
2. Copier le tarball dans un autre répertoire, en le renommant: `_nomdumodule___version_.orig.tar.gz`. Le underscore est très très important ! Dans notre exemple, nous aurons `libcybesync-perl_1.0.orig.tar.gz` (le lib est la manière standard de nommer les paquets perl - si vous avez un doute, tentez la commande debuild, il vous dira ce qu'il attend comme nom de tarball original)
3. Extraire le contenu du tarball + cd new\_folder. Ce nouveau dossier (qu'on peut renommer comme bon nous semble) sera celui utilisé pour mettre à jour le package. Il ne faudra donc pas l'effacer après la création du premier paquet !
4. Ajouter le fichier .gitignore à la racine du nouveau dossier, avec le contenu suivant:
    
   ```text
   META*
   MYMETA*
   _build/*
   blib/*
   ```
    
5. Exécuter la commande `dh-make-perl`, qui va créer tous les fichiers debian dont nous aurons besoin.
6. Editer le fichier `debian/control`, en s'assurant que l'architecture, la version et la description sont correctes.
7. Faire un commit des changements: `git commit -a`.
8. Créer le paquet debian via la commande `debuild -us -uc`. L'option -us permet d'éviter la signature du package (qui peut poser des problèmes si on ne possède pas de clé pgp).
9. Vérifier que tout est correct avec la commande `dpkg -I ../.deb`

### Mettre à jour le paquet debian

Attention : toutes les modifications sur les sources doivent se faire dans le répertoire original, tandis que les modifications relatives au package (description, version, etc) se font dans le répertoire utilisé pour le packaging. Les deux sont totalement différents, voire presque indépendants!

1. Les sources ayant été modifiées, il s'agit de recréer un tarball. Ce tarball représente la nouvelle _upstream_ version.
2. Mettre à jour le dossier packaging via la commande `git-update-orig` .
3. Mettre à jour le changelog via `git-dch`: il faudra soit-même s'assurer que la version est correcte, voire la modifier à la main.
4. Faire un git commit des changements
5. Créer le nouveau package via `debuild -us -uc`
6. Normalement, il n'est pas nécessaire de garder tous les packages debian précédemment créés. Tant que nous avons la dernière release et un tarball original !

## Other utilities

### readme.md file

The plugin `Pod::Markdown` includes a nice util, `pod2markdown`, which convert your POD to md file format.

```perl
# convert POD to md file
> pod2markdown MyModule.pm > README.md
# convert POD to text file
> pod2text MyModule.pm > README
# convert POD to html
> pod2html MyModule.pm > README.html

# display POD
> perldoc MyModule.pm
```
