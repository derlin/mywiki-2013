---
title: "Perl makefile"
date: "2014-09-13"
categories: 
  - "languages"
  - "perl"
---

#### Create the skeleton

Run the command : `h2xs -AX -skip-exporter -n MyModule::Name` This will create the usual folders, structure and also the most important file, Makefile.pl.

#### Edit the Makefile.PL

Here, we use `ExtUtils::MakeMaker`, which is really simple to use.

The file is composed of a simple function with named arguments. The most useful ones are:

| NAME | The name of your module/script |
|---|---|
| VERSION/VERSION_FROM | The module’s version, or the file containing the version informations. |
| PREREQ_PM | The dependencies, with their version number. Use 0 when in doubt. |
| PM | By default, the script will search for .pm file under the lib directory. In case you have some other scripts (.pl) or use a custom structure, it can be useful to manually list the files you want to include in your module, as well as where and under which name it must be installed. So, PM is a hash in the form source location / destination location.  For .pm files, use the variable `$(INST_LIB)`; for scripts, use `$(INST_BIN)`… |
| MAN1PODS/MAN3PODS | In case the documentation is in separate(s) file(s), you can list them here. The keys are the files, the values the destionations in the blib directory. So, your entries will be of the form `somedoc.pod => 'blib/man1/someprogram.1` |


#### Make and install

```bash
perl Makefile.PL # or ./Makefile.PL
make 
# check the content of the blib directory to be sure
# the structure is as expected
make install
```

## A full example

```perl
use 5.018000;
use ExtUtils::MakeMaker;
# See lib/ExtUtils/MakeMaker.pm for details of how to influence
# the contents of the Makefile that is written.

WriteMakefile(
    NAME              => 'EasyCmd',

    VERSION_FROM      => 'easypass.pl', # finds $VERSION

    PREREQ_PM         => {  # e.g., Module::Name => 1.1
        "Term::ReadLine"        => 0,
        "Term::ANSIColor"       => 0,
        "Data::Dumper"          => 0,
        "Term::ReadLine"        => 0,
        "Term::ReadKey"         => 0,
        "Term::ANSIColor"       => 0,
        "Cwd"                   => 0,
        "Getopt::Long"          => 0,
        "File::Spec::Functions" => 0,
        "Clipboard"             => 0,
        "Text::ParseWords"      => 0,
        "JSON"                  => 0,
        "Data::Dumper"          => 0,
        "Term::ANSIColor"       => 0,
        "Term::ReadLine"        => 0,
        "Term::ReadKey"         => 0,
        "utf8"                  => 0,
    },


    PM              => {
        'lib/DataContainer.pm' => '$(INST_LIB)/DataContainer.pm',
         'easypass.pl'         => '$(INST_BIN)/easycmd'
    },

    # PL_FILES => { 'easypass.pl' => 'bin/easypass'},

      MAN1PODS     => {
        'easypass_doc.pod'    => 'blib/man1/easycmd.1'
      },



    ($] >= 5.005 ?     ## Add these new keywords supported since 5.005

      (ABSTRACT_FROM  => 'easypass_doc.pod', # retrieve abstract from module

       AUTHOR         => 'Lucy Linder <lucy.derline@gmail.com>') : ()),
);
```

