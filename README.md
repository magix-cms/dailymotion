# Dailymotion

Plugin dailymotion for Magix CMS 3
Ajoute des vidéos dailymotion aux produits de votre site.

[![release](https://img.shields.io/github/release/magix-cms/dailymotion.svg)](https://github.com/magix-cms/geminiai/releases/latest)
![License](https://img.shields.io/github/license/magix-cms/dailymotion.svg)
![PHP Version](https://img.shields.io/badge/php-%3E%3D%207.4-blue.svg)

## Auteurs

* **Gerits Aurelien** (gtraxx) - [aurelien@magix-cms.com](mailto:aurelien@magix-cms.com)
* Communauté Magix CMS

## Installation
 * Décompresser l'archive dans le dossier "plugins" de magix cms
 * Connectez-vous dans l'administration de votre site internet
 * Cliquer sur l'onglet plugins du menu déroulant pour sélectionner thematic.
 * Une fois dans le plugin, laisser faire l'auto installation
 * Il ne reste que la configuration du plugin pour correspondre avec vos données.
 * Copier le contenu du dossier skin/public dans le dossier de votre skin.

## Afficher les vidéos dans le produit
Ajouter la ligne suivante dans le tpl du produit où vous souhaitez afficher les vidéos

```smarty
{include file="dailymotion/brick/videos.tpl" data=$product.dailymotion}
```

<img width="1173" height="351" alt="Image" src="https://github.com/user-attachments/assets/13636fc7-45de-4688-90b1-ce65b7230f82" />

---

## Licence

Ce projet est sous licence **GPLv3**. Voir le fichier [LICENSE](LICENSE) pour plus de détails.
Copyright (C) 2008 - 2026 Gerits Aurelien (Magix CMS)
Ce programme est un logiciel libre ; vous pouvez le redistribuer et/ou le modifier selon les termes de la Licence Publique Générale GNU telle que publiée par la Free Software Foundation ; soit la version 3 de la Licence, ou (à votre discrétion) toute version ultérieure.

---