# Anti Spam

Antispam protection for Thelia forms : contact, customer registration, newsletter.

## Installation

### Manually

* Copy the module into ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is AntiSpam.
* Activate it in your thelia administration panel

## Usage

In the module configuration you can chose which elements of protection you want to activate or deactivate.
These are honeypot field, form filling duration field, and question/calculation field.

## Hook

The module introduces a front hook "pxlpluri.antispam" which adds html template for honeypot and question fields.
