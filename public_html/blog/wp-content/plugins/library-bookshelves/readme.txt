=== Library Bookshelves ===
Contributors: PhotonicGnostic, GPL IT
Tags: books, bookshelf, library, catalog, ILS
Requires at least: 3.7
Tested up to: 5.7
Requires PHP: 5.3
Stable tag: 4.26
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Create bookshelves that link to your library catalog. Use shortcodes and widgets to display book covers in Slick carousels.

== Description ==

The Library Bookshelves plugin allows you to curate virtual bookshelves just like you would a shelf around a theme in your library. Bookshelves are displayed as customizable Slick carousels, using cover art from, and links to, your library catalog. The plugin creates a Bookshelves post type, shortcode, widget, and custom taxonomy.

**Many of you have asked for a way to have Bookshelves which link directly to cloudLibrary, Hoopla, or Overdrive, and also have Bookshelves which link to your main catalog. Well, here it is! Look for the ebook catalog options in the Bookshelves post editor.**

This plugin currently supports these catalog systems:
- BiblioCommons
- Bibliotheca cloudLibrary
- Calibre and COPS
- Civica Spydus
- DB/Textworks
- EBSCOHost Discovery Service
- Evergreen
- Ex Libris Primo
- Hoopla
- Innovative Encore, WebPAC PRO, and Polaris
- Koha
- Marmot Pika
- OpenLibrary.org
- Overdrive
- SirsiDynix Enterprise and Horizon
- TLC
- WorldCat

It supports retrieval of images from these third-party CDNs:
- [Amazon](https://images-na.ssl-images-amazon.com)
- [ChiliFresh](https://secure.chilifresh.com)
- [Baker & Taylor](https://contentcafe2.btol.com)
- [EBSCO](https://rps2images.ebscohost.com)
- [OpenLibrary.org](http://covers.openlibrary.org)
- [Syndetics](http://syndetics.com)
- [TLC](http://ls2content.tlcdelivers.com).

Bookshelves can be populated using:
- Calibre OPDS (and HTML) PHP Server API
- Evergreen SuperCat feeds
- JSON data from any web address
- Koha Reports Web Service
- Koha RSS feeds
- New York Times Books API
- OpenLibrary API
- Pika API
- Sierra API
- SirsiDynix Symphony Web Service
- TLC LS2 PAC API

When using an API you can set a Bookshelf to update items on a regular schedule using the Wordpress cron system. Items returned from an API query which have no associated cover art in your selected image CDN are automatically removed from the Bookshelf.

If you would like this plugin to support another catalog system, CDN, or web service [email me](mailto:lorangj@guilderlandlibrary.org).

Originally developed by and for staff at the [Guilderland Public Library](http://guilderlandlibrary.org).

Thanks to Gregory Testa of Chesapeake Public Library and Josh Stompro of Lake Agassiz Regional Library for feature suggestions and code contributions.

We want to know where our plugin is being used and how you're using it! Don't worry, we're not going to use any tracking code to find out. If you are one of the many libraries using this plugin [drop us a line](mailto:lorangj@guilderlandlibrary.org) and say "Hi!"

= Configuration =

1. Go to *Bookshelves>Settings* to configure the plugin.
1. On the Catalog tab enter the domain name of your catalog (default is OpenLibrary.org).
1. Select your catalog system and image CDN.
	- If you have Polaris 6.3 or higher you may need to choose the Polaris 6.3+ catalog option if your item links fail.
	- ChiliFresh users will need to add their website domain to "Covered hosts" in the ChiliFresh Admin Panel for images to display.
	- TLC users will need to enter a Customer ID which can be found in your catalog's item cover art URLs.
1. Enter your Overdrive or cloudLibrary catalog URL if you wish to have Bookshelves link to that catalog. You can then set individual Bookshelves to link to your ebook catalog instead of your main catalog.
1. On the Slider Settings tab you can customize Bookshelf behavior. Defaults have been set to get you started.
1. On the CSS Settings tab you can customize some Bookshelf element styles.

= Getting Started =

Create a new Bookshelf using ISBNs or UPCs from items in your catalog. You can input items manually, from an exported list, or from a web service API. Paste the Bookshelf shortcode into a post or page, or use the Bookshelf widget. Add location tags to your Bookshelves to display them in the widget or just to keep them organized. The widget can organize Bookshelves in tabs, and will sort Bookshelves by the Order attribute.

You can make a Bookshelf that links to an ebook catalog while having other Bookshelves link to your main catalog. To do this, enter your ebook catalog URL(s) in addition to your main catalog URL in the plugin settings. Create a new Bookshelf and choose an option in the eBook Catalog box.

**EBSCOHost users must enter Accession Numbers, and Calibre/COPS users must enter book ID numbers instead of ISBNs or UPCs.**

= REST API =
You can modify Bookshelf items and alt text using the [WP REST API](https://developer.wordpress.org/rest-api/reference/posts). The API endpoint for Bookshelf posts is https://{your.library.url}/wp-json/wp/v2/bookshelves/. This plugin only supports the REST API in Wordpress 5.3 and higher.

= Known Issues =

There is a known issue using the Bookshelves widget in tabbed mode and the Ultimate Addons for Visual Composer plugin.

== Frequently Asked Questions ==

= Q: This plugin doesn't support my library's catalog system. Will future versions support it? =

A: Yes! But only if you [contact me](mailto:lorangj@guilderlandlibrary.org) with a link to your catalog! As soon as you do I'll get working on an update.

= Q: I don't know which CDN my catalog uses for item images. How do I find out? =

A: Right click on an item image from your catalog and select *View Image* or *Inspect* to see the image URL. If you're still not sure, [email me](mailto:lorangj@guilderlandlibrary.org.com)

= Q: I've set my catalog settings correctly, so why don't item images appear? =

A: Try an alternate ISBN if possible. Amazon, for example, only supports 10-digit ISBNs. Not all CDNs will have images for every ISBN. In some cases you may get better results using UPCs for DVD items.

== Changelog ==

= 4.26 =

* Added support for OpenLibrary API.

= 4.25 =

* Fixed bug causing Sympony Web Services API calls to fail.

= 4.24 =

* Added a sortable author column to the Bookshelves post list. Use Transform option turned off by default to fix blurring in Chromium browsers when slides are in motion.

= 4.23 =

* Improvements to API data processing. Bug fixes in Slick settings. When using Koha RWS or RSS, the plugin will look for images on the Koha server if no ISBN is in the item record. Improved results when using Evergreen SuperCat feeds, particularly for DVD items. Added an option to randomize item order in Bookshelves.

= 4.22 =

* Added option to disable links on Bookshelve item images. Minor bug fixes and improved error-checking.

= 4.21 =

* Added DB/Textworks support. Added more user-customizable CSS options.

= 4.20 =

* Added support for cloudLibrary catalogs. Added the ability to reset the global Slick settings to plugin defaults. Added an option to import JSON data from the web into Bookshelves. Fixed a bug which caused the plugin to incorrectly interpret some site timezone settings.

= 4.19 = 

* Added support for Evergreen SuperCat feeds. Fixed issue with item titles containing line feeds.

= 4.18 =

* Fixed a bug which caused broken links when using Evergreen record number with a location code set. Added the ability create Bookshelves that link to Overdrive or Hoopla, while also having Bookshelves that link to your main catalog.

= 4.17 =

* Added support for Koha Reports Web Service and RSS feeds. Fixed a problem with links affecting some TLC LS2 PAC catalogs.

= 4.16 =

* Added support for Overdrive and Hoopla. UX improvements to the catalog setting page.

= 4.15 =

* Added support for location IDs in Evergreen links. Fixed a bug that broke OpenLibrary links. Added support for Ex Libris Primo.

= 4.14 =

* Added support for Calibre OPDS (and HTML) PHP Server (COPS).

= 4.13 =

* Added support for Civica Spydus catalogs.

= 4.12 =
* Added support for the TLC LS2 PAC API. Now automatically removes items with bad image URLs when building a Bookshelf with an API query.

= 4.11 =
* Now supports WorldCat.org and WorldCat discovery service catalogs, and the New York Times Books API. Added REST API endpoints for the Bookshelves post type.

= 4.10 = 
* Added a workaround for Encore users who have recently noticed ContentCafe images failing to load. Added support for the Marmot Pika List API.

= 4.9 =
* Added a second Polaris catalog option to fix redirect failures experienced with Polaris 6.3.2292.

= 4.8 =
* Fixed a bug which caused duplicate items to appear when using the Symphony Web Services API if an item record had more than one ISBN or UPC.
* Fixed a bug which prevented some cover images from displaying when using the Sierra API.

= 4.7 =
* Added support for Marmot Pika catalogs. Various code improvements and security enhancements.

= 4.6 =
* Added the ability to schedule periodic Bookshelf post updates when populating a shelf from an API request. Fixed issue with Syndetics image URLs containing UPC numbers.

= 4.5 =
* Added support for older TLC catalogs (Library System pre-5.0). Added support for Baker & Taylor CDN credentials.

= 4.4 =
* Fixed bugs related to Sierra API requests.

= 4.3 =
* Added support for Sierra API requests. Polaris and WebPAC PRO users can now use ISBNs or UPCs.

= 4.2 =
* Added support for WebPAC PRO catalogs.

= 4.1 =
* Added an option to specify ISBN or UPC item identifiers when using the Syndetics CDN. Minor code improvements.

= 4.0 =
* Added options for processing data from web services. Added support for SirsiDynix Symphony Web Service API. Fixed a DVD image display issue caused by changes in the Syndetics CDN.

= 3.2 =
* Added support for Calibre servers. Some text changes and bug fixes.

= 3.1 =
* Changed some user-editable CSS defaults. Upgraded Slick to 1.9.0. Added option to select HTTP or HTTPS protocol for catalog URLs.

= 3.0 =
* Added a style editor to the settings page. Added Support for TLC catalogs. Added support for optional image ALT attributes to support screen readers. The widget now sorts Bookshelves by post order. Bookshelves now support post revisions. Fixed some bugs.

= 2.5 = 
* Fixed URL issue for SyrsiDynix Enterprise catalogs not configured with SSL certificates.

= 2.4 =
* Added support for item record number and UPC in addition to ISBN for Evergreen ILS and CDN.

= 2.3 =
* Added support for EBSCOHost Discovery Service.

= 2.2 =
* Fixed PHP 5.3 incompatibility which affected multi-site installs.

= 2.0 =
* Changed the ISBN input method to allow copy-and-pasting of delimited lists. Added customizable Slick options for each Bookshelf. Added Evergreen support. Fixed a CSS conflict that may occur with themes or plugins that also include Slick. Fixed some other minor bugs.

= 1.10 =
* Fixed a bug preventing some Slick CSS from rendering. Upgraded to Slick 1.8.1.

= 1.9 =
* Fixed a bug which prevented plugin settings from being read on some non-standard Wordpress installations.

= 1.8 =
* Added profile support for Polaris catalogs.

= 1.7 =
* Added support for Koha.

= 1.6 =
* Fixed a bug which could occur if profile code field is left blank.

= 1.5 =
* Added a setting for profile code and added profile support for SirsiDynix Enterprise catalogs.

= 1.4 =
* Added support for ChiliFresh CDN. Fixed some Slick settings bugs.

= 1.3 =
* Compatible with PHP 5.3+. Links to Encore catalog are now HTTPS.

= 1.2 =
* Fixed bug with default settings. Added minimum PHP version.

= 1.1 =
* Added support for BiblioCommons, Polaris, and SirsiDynix catalogs.

= 1.0 =
* Initial version.
