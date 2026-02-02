# Procédure de Déploiement

Décrivez ci-dessous votre procédure de déploiement en détaillant chacune des étapes. De la préparation du VPS à la méthodologie de déploiement continu.

## Préparation du VPS

### Installation de aaPanel et du site :

```
URL=https://www.aapanel.com/script/install_7.0_en.sh && if [ -f /usr/bin/curl ];then curl -ksSO "$URL" ;else wget --no-check-certificate -O install_7.0_en.sh "$URL";fi;bash install_7.0_en.sh ipssl
```

- Choix du pack Single Web site sur aaPanel : Nginx, MySQL, php, phpMyAdmin
- Dans l'onglet "Website" cliquer sur "Add site"
- Entrer le nom de domaine OU l'adresse ip dans le champ "domain name"
- Confirmer

**/!\ j'ai été obligé d'aller sur aaPanel > App Store > php 8.3 > Disabled functions : et de "Del" donc réactiver putenv et proc_open qui était désactivé pour faire le composer install**

/!\ Il faudra peut être aller dans C:\Windows\System32\drivers\etc\hosts de votre ordinateur et ajouter la ligne :

```
192.168.23.134  bretbeil.dfs.lan
```

pour que cela fonctionne, c'est ce que j'ai du faire sur ma machine de développement.

### URL Rewriting

Dans la configuration du site aller dans l'onglet "URL Rewrite" et sélectionner le modèle MVC et faire "Save"

### Mise en place du certificat SSL

Pour installer un certificat SSL, du fait que le nom de domaine était un nom de domaine local, la méthode avec let's encrypt ou autre ne fonctionnait pas. J'ai donc lancé cette commande dans le cmd du VPS :

```
openssl req -x509 -nodes -days 3650 -newkey rsa:2048 -keyout /root/bretbeil.key -out /root/bretbeil.crt -subj "/C=US/ST=State/L=City/O=Local/CN=bretbeil.dfs.lan"
```

puis j'ai copié la clé et le certificat généré par cette commande dans l'onglet SSL et Current Certs de aaPanel.

### Création de la base de donnée

- Aller dans l'onglet "Database"
- Aller dans l'onglet "phpMyAdmin"
- Cliquer sur "public access"
- Un onglet avec phpMyAdmin va s'ouvrir, entrer le nom d'utilisateur root et le mot de passe présent dans l'onglet "root password"
- Dans phpMyAdmin, faites "Importer"
- Glisser le fichier database.sql pour créer la base de données
- Cliquer sur le bouton "Importer" en bas de la page

### Mise en place des backups

Aller dans l'onglet "Cron" de aaPanel. Cliquer que le bouton "Add Task".

- Dans Task Type: Backup Database
- Task name: Laisser celui généré
- Execute cycle: daily at any night hours
- Backup reminder: non-receipt

ne pas toucher le reste

Ajouter une nouvelle task:

- Dans Task Type: Backup Site

ne pas toucher le reste

### Création du repo distant Github

- Ouvrir une connexion ssh sur le VPS et entrer ces commandes :

```
mkdir /var/depot-git
cd /var/depot-git
git init --bare
```

- Sur la machine de développement, dans le dossier de développement :

```
git remote add vps root@192.168.23.134:/var/depot-git
```

A chaque mis à jour de l'application faire un git push sur le vps
Après le premier push sur l'application, ne pas oublier de changer la config du site sur aaPanel et changer le "_Running directory_" par public dans l'onglet _Site directory_.
Il faut aussi enlever le _Anti-XSS attack_ au même endroit.

## Méthode de déploiement

Tout d'abord commencer par commit son code avec une version et un tag. (Ne pas oublier de mettre à jour le fichier CHANGELOG à l'aide de git-cliff: git cliff --bump -o .\CHANGELOG.md)

Faire la commande :

```
git push -u vps {tag / branch}
```

Sur le serveur, faire la commande :

```
git --work-tree=/www/wwwroot/192.168.23.134 --git-dir=/var/depot-git checkout -f {tag / branch}
```

et dans le dossier ou se trouve le projet faire :

```
composer install
```

**_Ne pas oublier de modifier / créer le fichier .env_**
