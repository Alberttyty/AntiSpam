# Anti Spam

Antispam protection for Thelia 2 forms : contact, customer registration, newsletter.

## Installation

### Manually

* Copy the module into ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is AntiSpam.
* [Optional] Choose your usage, see below in the Usage section.
* Activate it in your thelia administration panel

## Hook

The module introduces a front hook "pxlpluri.antispam" which adds html template for honeypot and question fields.

## Usage

In the module configuration you can chose which elements of protection you want to activate or deactivate.  
These are honeypot field, form filling duration field, and question/calculation field.

### Manually

**For developpers only**, you can use the new pxlpluri.antispam hook to add antispam fields where you want. Add then your CSS and your JS. You can be inspired by assets included in the module.

### Automatically

For an automatic integration in the default thelia contact form, you must activate the commented lines in AntiSpam\Config\config.xml  
Then, the module will use theses Thelia hooks :
* contact.form-top (display alert errors)
* contact.form-bottom (display antispam fields)
* contact.stylesheet (add some CSS to hide honeypot field)
* contact.javascript-initialization (add some JS to add ajax question refresh)