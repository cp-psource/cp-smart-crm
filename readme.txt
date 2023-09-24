=== WP smart CRM & Invoices FREE ===

Contributors: softradeweb,andrew_fisher
Tags: crm, invoices, todo
Requires at least: 4.2
Tested up to: 4.7.5
Stable tag: 1.5.17
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
 
WP smart CRM & invoices FREE adds a powerful CRM with many functions to wp-admin. 
Manage Customers, Invoices, TODO, Appointments and Notifications.

== Description ==


WP smart CRM & invoices FREE covers lots of "office management features". 

CRM management: 

- Customers archive grid
- "CRM agent" custom role
- Todo and appointments scheduler
- Annotation timeline
- TODO / appointment Status update
- Notification system ( email and dashboard )
- Notification to single user(s)
- Notification to specific WP role(s)
- Custom notification rules
- Successive notification steps ( for mid/long term expiring services )
- Customers CSV import 


INVOICES /QUOTATION management

- Dynamic creation of invoices/quotation with multi-line products
- Invoices/quotation creation in .pdf format
- Download PDF and save on the server
- Custom LOGO
- Custom header elements alignment in pdf template
- Configurable payment expiring dates
- Notification at payment expiration
- Internal comments and annotation
- Custom canvas signature in quotation ( touch compatible )
- Invoices registration
- Custom numbering start value ( you can start to use it at any point of the year giving a starting number different from 1, conforming to your fiscal accounting )


All the records in grids are powered with filtering/grouping/sorting functions for a quick usage.

All the grids information are visually enhanced with icon/colors to always guarantee a quick overview

Official site and support : [WP Smart CRM free plugin](http://softrade.it/wordpress-crm-invoices-plugin/ "Plugin  Wordpress per CRM e fatturazione") 
The plugin has been designed conforming to the Italian fiscal rules but we're open to feedbacks to address specific country-based issues in EU, USA and ASIA.
NOTE: the italian fiscal law system is worldwide recognized as (probably) the most tricky and confused..so it will  an easy task to adapt it to any system, just ask !

If you want to send us feedback use the support forum, if you want to partecipate to its translation in more languages, drop us a note info [at] softrade.it
Important: if you change .mo/.po files in the plugin "languages" folder, you may lose your changes in next update. To prevent this, copy your .mo/.po files in "/wp-content/languages/plugins" folder.
== Installation ==



1. Upload `wp-smartcrm-invoices.php` to the `/wp-content/plugins/` directory

2. Activate the plugin through the 'Plugins' menu in WordPress

3. Some basic configuration are needed upon activation, follow the help to configure your business data


== Screenshots ==

1. The agenda view with appointments and todo having different styles based on status ( to do, done, canceled)
2. The Customer form with all the information and quick todo and summary of activities and related documents
3. The document list grouped by type ( invoices / quotation )
4. The pdf document that can be downloaded and saved on the server for future browsing
5. The "notification rules" screeen by which you can create a set of notifications step to apply to a rule
6. The "online help"  popup that contains information for every screen you're currently in

== Changelog ==

= 1.5.17 =
* Minor Bugfixes

= 1.5.16 =
* Bug fixed dbDelta at plugin activation + missing file
* Added discount type in invoices ( %  or value)

= 1.5.14 =
* Bug fixed php notices

= 1.5.13 =
* Bug fixed missing class upgrade from 1.5.09


= 1.5.11 =

* Added compatibility with  [thenewsletterplugin](https://www.thenewsletterplugin.com/ "Newsletter plugin for wordpress") with record bulk import
* Improved timeline log view for customer

= 1.5.09 =

* Bug fixed db upgrade from 1.5.08

= 1.5.08 =

* Added support for BRL, CNY, JPY, INR currencies and date format
* Bug fixed document custom numbering
* Added  categories , interests and origins in CSV import + all missing fields


= 1.5.07 =

* Bug fixed saving customer with some general options selected

= 1.5.06 =

* Bug fixed customer form php open tags 

= 1.5.05 =

* Bug fixed error on activation for PHP < 5.3


= 1.5.04 =

* CSS improvement in document printable version with some options added
* Usability improved in customer edit/insert
* Bug fixed in TODO status update


= 1.5.03 =

* Bug fixed in category update at setup 


= 1.5.02 =

* Bug fixed product description in invoices and quotes not saving correctly 

= 1.5.01 =

* Minor bug fix unespected output in send mail via cron



= 1.5.0 =

* UI improvement in grids: create TODO, appointments and activities directly from customers grid
* Implemented better taxonomies and terms management for customers
* Implemented split Invoices / Quotes in separate grids


= 1.4.9 =

* Implemented: automatic reset for progressive numbering of invoices and quotes at new year change



= 1.4.8 =

* Bug fixed utf8 encoding in customer address field
* Featured added: Greek culture support for js widgets ( calendar, datepicker)



= 1.4.7 =

* Bug fixed in todo and appointments creations for GERMAN language
* Featured added: send instant notification at todo / appointments creation


= 1.4.6 =

* Minor Bug fixed in todo and appointments notification scheduled time


= 1.4.5 =

* Feature added: Quick toggle invoices payment status (paid/unpaid) from document grid 
* Bug fixed at setup for some mysql configuration
* Bug fixed: PDF documents  export not working



= 1.4.4 =

* Feature added: Date range selection improved in todo and document grids 


= 1.4.3 =

* Fixed: Customer selection in appointment/todo creation


= 1.4.2 =

* Fixed: import csv not working properly
* Fixed: missing csv example file


= 1.4.1 =

* Fixed: CRM-Agents can only manage their customers and their customers' documents
* Optionally possible to choose whether to print totals in quotes or not
* Minor bug fixes


= 1.4 =

* Feature added: csv customers import 
* Added link to CRM important sections in the main WP admin menu 


= 1.2 =

* bug fixed for currencies and date format


= 1.1 =

* ENGLISH TRANSLATION


= 1.0 =

* First version


== Upgrade Notices ==
