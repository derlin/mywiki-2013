---
title: "Perl tips and tricks"
date: "2013-10-13"
categories: 
  - "languages"
  - "perl"
---

## Import a module from a custom path

When you write custom modules, it is normally in order to use them in other files. To import them properly, i.e. in a way that they are always found, you should proceed like this :

```perl
use Cwd;
push @INC, ( Cwd::abs_path($0) =~ /(.*\/)[^\/]*/ and $1 );
require MyModule;
```

The idea is to push the path of the running file into the @INC variable (containing the paths perl will use to locate modules) before importing your module.
The **require** is the oldest way to load code. Its advantage is that it is evaluated at runtime and works with any piec of code (library, module, text file) while the `use` keyword is evaluated at compile time, so before you modified the @INC array, and works only with modules (you cannot write something like `use aFile.pl`.
Another difference between use and import is the Export handling. Here are some examples :

```perl
use Foo();
# equivalent to:
require Foo;
# ie don't import anything, not even the default things

use Foo;
# equivalent to:
BEGIN{
   require Foo; Foo->import();
}
use Foo qw (foo bar);
# equivalent to:
BEGIN{
   require Foo; Foo->import(qw(foo bar));
}
```

## Readline, terminal interactions, ...

### Get password from commandline

For that to work, you should first have a proper environment:

1. Check that you have proper readline support. The packages libreadline\* and libterm-readline-gnu-perl should installed on your system
2. In your ~.bashrc, add the following line : `export "PERL_RL= o=0"`

Then, you can use this code :
```perl
# prompts for a password and returns it, without term object
use Term::ReadKey;

print "Enter your password: ";
ReadMode 'noecho';
$password = ReadLine 0;
chomp $password;
ReadMode 'normal';
print "\n";
```
This is the easy and straight-forward way. But if we use a term object and we also want to avoid the password to be kept in history, we would rather use a more complex routine, like this one:

```perl
use Term::ReadKey;
use Term::ReadLine;

my $term = Term::ReadLine->new("a name"); # give it a name

sub get_pass{ # $pass (void)
    # parse args
    my $msg = shift;
    $msg = "Type your password : " unless defined $msg;

    # get password
    print $msg; # (??) it does not work with $term->...
    ReadMode('noecho'); # don't echo
    my $password;
	chomp( $password = ReadLine 0 ); 
    ReadMode( 0 );  # back to normal

    # remove pass from history
    eval{ $term->remove_history( $term->where_history() ) }; 

    print "\n";
    return $password;
}
```

### Autocompletion

First, check those links:

- [http://search.cpan.org/~hayashi/Term-ReadLine-Gnu-1.20/Gnu.pm#Custom\_Completion](http://search.cpan.org/~hayashi/Term-ReadLine-Gnu-1.20/Gnu.pm#Custom_Completion)
- [http://search.cpan.org/~hayashi/Term-ReadLine-Gnu-1.20/Gnu.pm#Term::ReadLine::Gnu\_Functions](http://search.cpan.org/~hayashi/Term-ReadLine-Gnu-1.20/Gnu.pm#Term::ReadLine::Gnu_Functions)

Sample example, autocompletion:
```perl
my $term = Term::ReadLine->new("a name");
$term->Attribs->{completion_function} = sub{
    my ($text, $line, $start) = @_;

    if( $line =~ /^s*$/ ){ # first word
        @_ = grep{ /^$text/ } @COMMANDS;
        return @_ if( scalar(@_) );
    }

    if( $line =~ /^s*(copy|find)s*w*$/ ){
        return grep { /^$text/ } @HEADERS_FOR_COMPLETION if( $text );
        return @HEADERS_FOR_COMPLETION;
    }

    if( $line =~ /^(w+)$/ ){
        @_ = grep{ /^$1/ } @COMMANDS;
        return @_ if( scalar(@_) );
    }

    return undef;
};
```

### Coloring

A simple and useful package is available on CPAN to use the built-in colors of the terminal : [Term::ANSIColor](https://www.google.ch/url?sa=t&rct=j&q=&esrc=s&source=web&cd=1&cad=rja&ved=0CDEQFjAA&url=http%3A%2F%2Fperldoc.perl.org%2FTerm%2FANSIColor.html&ei=ucJaUpK1G4nOtAaOsIHYDQ&usg=AFQjCNH6owAQsw7x6Xg6PUAvXEteRH_f6Q&sig2=iTXqlxeQW6AcyQYWv_fuVQ&bvm=bv.53899372,d.Yms).

```perl
# prints an error message (in red) to stdout
# I<params>: the message to print
sub print_error{ # void ( $message )
    my $msg = shift;
    print "  --- ", color( 'red' ), $msg, color( "reset" ), " ---" , "n"
        unless not defined $msg;
}

# prints an info message to stdout
# I<params>: the message to print
sub print_info{ # void ( $message )
    my $msg = shift;
    print "  --- ", color( 'magenta' ), $msg, color( "reset" ), " ---" , "n"
        unless not defined $msg;
}

```

## Utilities

### Remove duplicates from arrays

```perl
# removes the duplicates from the given array
# I: the array
sub distinct{ # \@ ( \@ )
    # the idea is to convert the array into a hash, since hash keys 
    # must be unique, and then get the keys back
    my %h;
    return grep { !$h{$_}++ } @_
}
```

### trim strings

```perl
# simple trim function
# I< params>: the string to trim 
sub trim { # $ ($)
   return $_[0] =~ s/^\s+|\s+$//rg;
}
```

## Catch signals

Nothing easier in Perl, although I didn't test it with multiple threads... The variable $SIG is a hash containing pointer to the routines used for each signal.

```perl
#!/usr/bin/perl

use strict;

our $SIG;

SIG{"INT"} = "catch_signal";

while (1){
    print "waiting for signal ... press ctl+c to catch the interrupt signal \n";
    sleep(10);
}

sub catch_signal {
    print "\n Kool, I am able to handle interrupt signal \n";
    exit();
}
```

## Get rid of the warning "SmartMatch is experimental"

This warning comes from the use of the ~~ operator, which is Two ways:

1. A smart guy developped a package, [`experimental`](https://metacpan.org/module/experimental), which provide macros to enable/disable experimental features. After installing it, we can simply write:
    ```perl
    use experimental 'smartmatch';
    ```
    
    and we are done
2. A second way is to disable smartmatch warning by an ugly macro:
    ```perl
    no if $] >= 5.017011, warnings => 'experimental::smartmatch';
    ```
    Notice that we must take the perl version into account, since the experimental::smartmatch is not defined in earlier versions, which will result in an explicit error...

_Note:_It would be interesting to use the construct [`given ... when`](http://perldoc.perl.org/perlsyn.html#Experimental-Details-on-given-and-when) as an alternative..

## Common troubles with perl-related tools

If the `perldoc` command outputs something like:

```text
ESC[1mNAMEESC[0m
    ESC[4mcybeESC[0m - Cyberlearn Sync Utility.

    Simple utility to keep in sync with a course from Cyberlearn.

ESC[1mDESCRIPTIONESC[0m
...
```

the problem here is that the "pager" (probably 'less') is "catching" (ie, printing "ESC" instead the actual escape character) the escape sequences, preventing your terminal from displaying the text correctly \[[sic](https://groups.google.com/forum/#!topic/comp.lang.perl.misc/9fv3yDB_ipM)\]. The cleanest solution is to set perldoc to use 'less' with the '-r' (="raw" output) option.

You can add this line to your .bashrc or .zshrc:
```bash
export PERLDOC_PAGER='less -r'
```
