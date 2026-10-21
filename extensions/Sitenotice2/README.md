<div align="center">
<h3>
Sitenotice2 - MediaWiki
</h3>
<p>An extension that improves the appearance of Sitenotice.</p>
</div>

<div align="center">
    <a href="https://central.staging.polandball.wiki/wiki/">
        Preview
    </a>
    <span>&nbsp;✦&nbsp;</span>
    <a href="#-installation">
        Installation
    </a>
    <span>&nbsp;✦&nbsp;</span>
    <a href="#-notes">
        Notes
    </a>
    <span>&nbsp;✦&nbsp;</span>
    <a href="#-configurations">
        Configurations
    </a>
    <span>&nbsp;✦&nbsp;</span>
    <a href="#%EF%B8%8F-usage">
        Usage
    </a>
    <span>&nbsp;✦&nbsp;</span>
    <a href="#%EF%B8%8F-screenshots">
        Screenshots
    </a>
</div>

<p></p>

<div align="left">
Sitenotice2 is an extension that improves the visual appearance of the MediaWiki Sitenotice notices, this extension places them at the bottom in a floating way. It also adds a header along with a wiki logo. ✨
</div>

## 🚀 Installation
1. [Download](https://github.com/DarkGames26/mediawiki-extensions-Sitenotice2/archive/refs/heads/main.zip) place the file(s) in a directory called `Sitenotice2` in your `extensions/` folder.
2. Add the following code at the bottom of your LocalSettings.php:
```php
wfLoadExtension( 'Sitenotice2' );
```
3. **✔️Done** - Navigate to Special:Version on your wiki to verify that the extension is successfully installed.

### 👀 Notes
⚠️ This extension should not be used with DismissableSiteNotice. It was created based on that extension, so it has all the functionality that DismissableSiteNotice already had.

## 🧞 Configurations
**Available configurations**

| ⚙️ | Name | Description | Values | Default
:--- | :--- | :--- | :--- | :---
| ⚙️ | `$wgDismissableSiteNoticeForAnons` | This allows to set whether or not it should be possible for anonymous visitors of the wiki to dismiss the sitenotice shown. Available for MW 1.25 + | `true` or `false` | `true`
| ⚙️ | `$wgMajorSiteNoticeID` | Integer. The value is stored inside a cookie. When a user decides to close the sitenotice, the current value of `$wgMajorSiteNoticeID` is saved as well and the closed notice is not shown again. `$wgMajorSiteNoticeID` now can be incremented when a new text is set up as sitenotice. If `$wgMajorSiteNoticeID` has inbetween been incremented, then the sitenotice is shown again, even if the user closed an older sitenotice before. | `true` or `false` | `0`

## 🕹️ Usage
When you add a new sitenotice and want everyone to see it, change the number on the created page "MediaWiki:Sitenotice id" by one (e.g. if it would be 5, you'd replace the page with the number 6 and so on).

The extension behavior is that it will remember the dismissal by the user and only reset after the number in MediaWiki:Sitenotice_id has been raised. This allows a wiki to make minor updates to a notice without causing it to show again for everybody.

## 🎨 Configuring Colors
Sitenotice2 was thought to be highly modifiable easily with CSS, so the extension has different variables that are used for the colors displayed in the notice, which allows you to easily modify its appearance.
### CSS Variables
| ⚙️ | Name | Description
:--- | :--- | :---
| ⚙️ | `--filter-glass` | Filter applied to the entire notice.
| ⚙️ | `--sitenotice2-border-radius` | border-radius applied to different parts of the notice, both in the main container and on the left border.
| ⚙️ | `--sitenotice2-border` | Border-color
| ⚙️ | `--sitenotice2-background` | Background of the notice.
| ⚙️ | `--sitenotice2-background--header` | Background for the header. In case you don't want background, just set it to `-transparent`.
| ⚙️ | `--sitenotice2-background--secondary` | Background for the left border of the notice.

### Example configuration:
```
/*  🎨 Extension:Sitenotice2 
    -------------------------  */
:root {
	--sitenotice2-border-radius: 6px;
	--sitenotice2-border: #a51919;

	--sitenotice2-background: rgba(232,189,189,0.73);
	--sitenotice2-background--header: rgba(251,225,225,0.6);
	--sitenotice2-background--secondary: #a51919;
}
```

## 🎞️ Screenshots
<img src="img/Sitenotice2_Screenshot.png" height="auto" width="auto" /> 
<img src="img/Sitenotice2_Screenshot_2.png" height="auto" width="auto" /> 
<img src="img/Sitenotice2_Screenshot_3.gif" height="auto" width="auto" /> 


## ✅ Por hacer...

- [ ] Implementar compatibilidad con  <a href="https://www.mediawiki.org/wiki/Extension:DismissableSiteNotice">DismissableSiteNotice</a>.
- [ ] Corregir border-radius en Citizen, Minerva Y Default.
- [ ] Alinear de manera más apropiada los Logos del encabezado.
- [ ] Implementar compatibilidad para idiomas que se lee de derecha a izquierda.
- [ ] Mejorar la animación al cerrar el aviso.
- [ ] Añadir compatibilidad con <a href="https://www.mediawiki.org/wiki/Extension:GlobalNotice">GlobalNotice</a>.