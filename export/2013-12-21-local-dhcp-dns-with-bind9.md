---
title: "Local DHCP - DNS with bind9"
date: "2013-12-21"
categories: 
  - "configuration-tricks"
  - "programs"
---

## DNS

### Configurer bind9

Avant toute chose, installer bind9 via `install bind9 isc-dhcp-server`.

###### fichier named.conf.options

Mettre le dnssec-validation à "no" et ajouter un bloc _forwarders_ qui contient les adresses ip des serveurs DNS à qui s'adresser. Peuvent être soit les DNS de notre provider, soit les DNS de OpenDNS ou Google :

```text
forwarders {
    62.2.17.60;
    62.2.24.162;
};
dnssec-validation no;
```

Le block forwarders est **crucial**: il permet à notre serveur local de répondre à toutes les requêtes. Si le host n'est pas dans le domaine/le lan qu'il gère, il envoie la requête plus haut.

###### fichier named.conf.local

définit les zones dont le serveur à la charge, i.e. les zones locales.  
Exemple :  
On définit d'abord la zone "nommée":

*   type: master ou slave. Le type master spécifie que le serveur ne va chercher nulle par ailleurs pour les informations, tout est là. Slave : le serveur n'a pas autorité sur cette zone, mais doit s'adresser à un autre serveur (souvent quand on a deux serveurs DNS, un primaire et un secondaire. Seul un master par zone)
*   file: le chemin vers le fichier servant de base de données, soit où sont écrites les entrées RR ( resource records)
*   allow-update: si à faux, aucun serveur DHCP n'a le droit de demander une update du fichier db. Vu que nous voulons un DHCP dans notre domaine, et qu'il tourne sur le pi, on spécifie que les requêtes venant de localhost sont acceptées
```
zone "pipi" {
    type master;         // ce serveur est le master de cette zone
                         // vs slave si on a plusieurs dns server
    file "/opt/dhcp-dns/db.pipi"; // ou aller chercher les infos
    allow-update {                // permettre les update de dhcp
        127.0.0.1;
    }; 
}; 
```

On définit ensuite la zone "IP", ce qui est crucial pour les requêtes inverses. Le principe est le même.

*   notify: si on a plusieurs DNS, spécifie si le master doit notifier ses slaves lors de changement. Sinon, polling à intervalle régulier (donc si master+slaves, mieux de mettre notify à yes)

Il est aussi possible de définir notify au niveau global, donc pour toutes leszones, en ajoutant en début de fichier.
```
options {
  notify yes;
  // ...
};

zone "0.168.192.in-addr.arpa" {  // reverse address
    type master;
    file "/opt/dhcp-dns/db_reverse.pipi";
    notify no;              // wtf ??
    allow-update {        
        127.0.0.1;
    }; 
};
```

###### fichier zones.rfc1918

Si la zone définie existe dans la liste, la commenter car elle est déjà définie dans named.conf.local :

```java
// zone "168.192.in-addr.arpa" { type master; file "/etc/bind/db.empty"; }; 
```

##### Créer les fichiers db comme défini dans named.conf.local ( attribut "file" )

###### fichier /opt/dhcp-dns/db.pipi

*   $ORIGIN: permet de gagner du temps en évitant d'écrire à chaque fois server.pipi., virusnest.pipi. en gros, toutes les entrées seront suivies de l'origine, qui est le nom de notre zone. Le @ se réfère justement à l'origine
*   entrées: chaque entrée est précédée d'un ttl et est de la forme :  
    ```
    name    DNS class     information
    ```
*   SOA = state of authority record. Il spécifie l'authorité responsable de la zone et à une forme plus compliquée :  
    ```
    name SOA   server.name  email.address(@ -> .)  (
        ...
    )
    ```
            
    

les informations entre parenthèse sont pour les slaves principalement. Elles informent du minimum refresh rate, etc. On pourrait ne pas les mettre (format valide) mais cela pourrait poser problème lors de dnssec. Donc on les met quand-même, ne coute rien.

le serial est incrémenté lors de changements du document.

Pour le NS et le A qui suit, pas besoin de respécifier le TTL, car si manquant c'est le dernier qu'on a spécifié qui s'applique

*   NSnameserver : le nom du serveur pour la zone
*   A adresse ip de la zone
*   toutes les autres entrées sont ajoutées automatiquement par le serveur DHCP. Si on n'en a pas, il faut ajouter à la main un RR par machine
*   Attention ! il ne faut pas oublier d'ajouter à la main une entrée A pour le serveur, sinon il sera introuvable !!

```
$ORIGIN pipi.
 
$TTL 604800 ; 1 week
@       IN SOA  server fakeemailaddress (
   9          ; serial
   604800     ; refresh (1 week)
   86400      ; retry (1 day)
   2419200    ; expire (4 weeks)
   604800     ; minimum (1 week)
 )  
NS  server
A   192.168.0.16
 
$TTL 604800 ; 1 week
server       A   192.168.0.16
```

###### fichier /opt/dhcp-dns/db_reverse.pipi

exactement la même chose, mais à "l'envers". La zone est exprimée par son adresse IPv4 et le entrées sont de type PTR, soit "nom d'hôte".  
Encore une fois, ne pas oublier l'entrée statique "server". Le reste sera ajouté par DHCP

```
$ORIGIN 0.168.192.in-addr.arpa.
$TTL 604800 ; 1 week
@   IN SOA  server.pipi. fakeemailaddress.pipi. (
                9          ; serial
                604800     ; refresh (1 week)
                86400      ; retry (1 day)
                2419200    ; expire (4 weeks)
                604800     ; minimum (1 week)
                )  
NS  server.pipi.
```

Pour tester le reverse, utiliser la commande `dig -x 192.168.0.23`

### fichier /etc/resolv.conf

Spécifie les adresses DNS pour une machine. Sur le pi/le serveur, il faut spécifier que le name-server DNS se trouve sur localhost. Le "search pipi" permet de spécifier que si la demande DNS est sans nom de domaine, i.e. virusnest versus virusnest.pipi., il faut faire une recherche dans la zone pipi.

```
nameserver 127.0.0.1
search pipi
```

### Adress fixe pour le serveur

Il ne faut pas oublier de donner une adresse fixe au serveur et de désactiver la fonction DHCP de notre routeur.

Configuration de l'adresse fixe : `/etc/network/interfaces`

```bash
iface lo inet loopback
 
iface eth0 inet static
address 192.168.0.16
netmask 255.255.255.0
gateway 192.168.0.1    #router
 
allow-hotplug wlan0
iface wlan0 inet manual
wpa-roam /etc/wpa_supplicant/wpa_supplicant.conf
iface default inet dhcp  
```

## DHCP


###### fichier /etc/dhcp/dhcpd.conf

```bash
#spécifie comment mettre à jour le fichier des zones. Ici TXT
ddns-update-style interim;
 update-static-leases on;  #test ?? semble marcher aussi sans...
 
#on définit nos zones. Primary = adresse du serveur ayant authorité, ici localhost
zone pipi. {
    primary 127.0.0.1;
}
 
zone 0.168.192.in-addr.arpa. {
    primary 127.0.0.1;
}
 
option domain-name "pipi";   #spécifier le nom de domaine
option domain-name-servers 192.168.0.16, 62.2.17.60, 62.2.24.162; # ajouter le DNS cablecom au cas ou il y a une merde...
 
default-lease-time 3600;
max-lease-time 720000;  # vaut la peine de l'augmenter un peu
 
authoritative;    #mon serveur a autorité sur cette zone
 
log-facility local7;  #on ne touche pas
 
# options DHCP : sous-réseau dont il a la charge {}
# range = pool d'adresses à dispo. Ne pas commencer à 1 car routeur !!
# option routers = adresse(s) du routeur par défaut à transmettre aux clients
# interface : lui dire sur quelle interface il doit écouter pour les demandes broadcast destinées au port 67
subnet 192.168.0.0 netmask 255.255.255.0 {
  range 192.168.0.100 192.168.0.200;
  option routers 192.168.0.1;
  interface eth0; # rappel : broadast !! vaut mieux lui dire ou écouter...
}
 
# si on désire attribuer des ip fixes à certaines machines
host virusnest {
  hardware ethernet 50:46:5d:b3:6c:1e;
  fixed-address 192.168.0.23;
  ddns-hostname "virusnest";
  option host-name "virusnest";
}
 
host pi {
  hardware ethernet b8:27:eb:58:39:12;
  fixed-address 192.168.0.16;
  ddns-hostname "pi";
  option host-name "pi";
}
```

Pour toutes les adresses statiques (définies en statique dans /etc/dhcp/dhcpd.conf), il faut penser à ajouter à la main les entrées correspondantes dans db.pipi et db_reverse.pipi

###### fichier /etc/default/isc-dhcp.server

Penser à mettre la bonne interface :
```bash
# On what interfaces should the DHCP server (dhcpd) serve DHCP requests?
# Separate multiple interfaces with spaces, e.g. "eth0 eth1".
INTERFACES="eth0"
```

## Erreurs, problèmes, solutions

### How to fix BIND's journal out of sync error

If you are running a BIND name server with an dynamic zone updating from from DHCP or similar, you'll find that if the zone is manually updated the zone will no longer load correctly, giving the following error:

```
zone example.com/IN: journal rollforward failed: journal out of sync with zone
zone example.com/IN: not loaded due to errors.
```

The error can be clearing seen by running BIND from command line as follows:  
`named -g`

To resolve this stop BIND, then remove the journal file for problem zone, these exist in the same directory as the zone files but end in ".jnl". Once the file has been deleted BIND can be restarted and all will be back to normal.

### How to reload zone files after manual edit

Faire un simple reload ne suffit pas, il faut absolument passer par un restart du serveur bind9 pour que les modifications soient prises en compte !

### rndc - name server control utility

Petit programme en ligne de commande qui permet de faire des reload, config, etc d'un serveur DNS quel qu'il soit.

## SPF

A l'origine, le **Sender Policy Framework (SPF)** a été conçu pour éviter l'usurpation d'adresse de l'expéditeur.

Le protocole SMTP ne permet pas de vérifier l'adresse qui est fournie par l'expéditeur, ce qui permet à quiconque de forger facilement cette adresse. Cela représente un énorme problème car il n'y a aucun moyen de base pour identifier clairement la véritable origine d'un message. Les spammeurs et les phishers utilisent massivement cette faiblesse pour contourner les filtres les plus élémentaires et de tromper les utilisateurs finaux.

L'idée derrière SPF est de fournir un mécanisme qui permet aux administrateurs d'un domaine de spécifier quels serveurs sont autorisés à envoyer du courrier à partir de leur domaine. Avec cette information publique, n'importe quel serveur de destination peut vérifier si les messages ont été émis par un serveur valide ou non et refuser alors une adresse d'expéditeur falsifiée.

[source](http://www.mailcleaner.net/informations/spf.html)

Puisque les adresses des serveurs agréés doivent être publiques et facilement accessible, il est possible de rajouter des entrées dans les DNS Records, sous la forme suivante :
```
example.com.  IN TXT "v=spf1 mx -all"
; OR
example.com.  IN SPF "v=spf1 mx -all"
```

Ces entrées texte ou SPF, disent en gros la chose suivante : seules les adresses contenues dans les records MX sont autorisés à envoyer des emails pour le domaine. Le –all signifie la fin des tests. Bien sûr, les vérifications peuvent être beaucoup plus complexes et comprendre plusieurs adresses, des includes de domaines différents, etc.

Entrée SPF du serveur webmail de l'école (via dig) :
```
hefr.ch.        3600    IN    TXT    "v=spf1 ip4:160.98.8.116 ip4:160.98.8.117 ip4:160.98.2.243 ip4:160.98.2.244 ip4:160.98.240.40 include:spf.agenceweb.net -all"
```
seuls les serveurs dont les adresses IP sont présentes ainsi que ceux listés dans l'entrée spf du domaine agenceweb.net sont autorisés à envoyer des mails pour le domaine hefr.ch. Le serveur 160.98.31.32 que nous avons utilisé traduit donc notre usurpation.
