---
title: "Perl encoding on windows"
date: "2014-01-04"
categories: 
  - "perl"
tags: 
  - "perl"
  - "windows"
---

## Enable the console to support UTF-8

By default, windows console does only support ASCII characters. But we can easily change this default behavior, in three steps:

1. open a console
2. change the default font from Raster Fonts to Lucida or Consolas by clicking on the upper left icon >  defaults > fonts. Indeed, raster fonts does not include extended character sets
3. change the code point used by typing `chcp 65001` in the console

To set your console to unicode once and for all, type the following in your console and restart your computer. But be aware that some old applications might behave incorrectly:
```bash
# set the console to use latin1 character set by default
reg add hklm\system\currentcontrolset\control\nls\codepage -v oemcp -d 65001
# come back to default
reg add hklm\system\currentcontrolset\control\nls\codepage -v oemcp -d 437
```
For the little story, the term **code page** is another term for _character encoding_. IBM was the first to use it and created a table of code values describing the encoding for different languages. At first, Windows collaborated with IBM and used the same codes, but after their break-up in the nineties, the two tables evolved separately.

Windows code pages are unfortunately not well documented and it seems that the Console and the GUI often use different code pages (thanks to the DOS legacy):

- **Windows Code Page**: used by the system and the applications. These are ANSI code pages (Windows-1252), which support accented letters and such.
- **OEM Code Page**: stands for _Original Equipment Manufacturer_. These are the character sets used and supported by MS-DOS and they are actually still the default for the Windows console...
    
    > Back in the days of MS-DOS, there was only one code page, namely, the code page that was provided by the original equipment manufacturer in the form of glyphs embedded in the character generator on the video card. When Windows came along, the so-called ANSI code page was introduced and the name "OEM" was used to refer to the MS-DOS code page.
    > 
    > Over the years, Windows has relied less and less on the character generator embedded in the video card, to the point where the term "OEM character set" no longer has anything to do with the original equipment manufacturer. It is just a convenient term to refer to "the character set used by MS-DOS and console programs."
    

The more useful are **65001** (for the console - utf8) and **cp1252** (for file names with accented letters and such - latin1). By default, the console use cp 855, which is Cyrillic (try to type `chcp` in the console to know the active OEM).

## Handle files with accented letters

After trying a lot of different solutions, here is what worked for me.

- always begin your perl script with the directive `use utf8`
- for the console, set the encoding stdout should use with `binmode STDOUT ":encoding(...)"`
- for strings or filenames, always ensure that you convert all strings from outside (internet, ...) to utf8. Then, texts used for filenames or written to a file should be converted to latin1 (cp1252). Note that for the console, utf8 should be ok, as long as you set the binmode of stdout.

The following script tries to resume everything:

```perl
#!/usr/bin/env perl

use warnings;
use utf8;	# always use utf8 internally

use URI::Escape; 
use Encode qw(encode decode); # import the functions encode/decode

binmode STDOUT, ":encoding(UTF-8)"; # stdout set to utf8

# handle existing files
sub list_dirs {
	my ($path) = @_;
	my @resources;
	opendir( D, $path ) or die "Could not open directory '$path'\n"; 
	map {  
		push @resources, 
		decode( "cp1252", $_)  # decode: latin1 => utf8
	} readdir D;
	closedir D;

	# accents will be correctly handled
	map { print $_, "\n" } @resources; 

	return \@resources;	
}

# download a pdf with accented letters in title + content
sub download_pdf {
	my ($url) = @_;
	my $resp = LWP::UserAgent->new()->get( $url );
	# ensure you have utf8
	$_ = decode("utf8", uri_unescape( $1 )) 
		if $url =~ m/([^\/]+pdf)/; 

	# filename converted to latin1 + for pdfs, 
	# be sure to write RAW BYTES to file
	open F, ">:raw", encode( "cp1252", $_ ) 
		or die("Could not write file '$_'\n"); 
	print F  $resp->content;
	close F;	
}
```