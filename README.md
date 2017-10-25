# Islandora XML Forms

## Introduction

XML Forms is a collection of Drupal modules that allow for the manipulation of
XML documents though Drupal forms. The Islandora Form Builder
(XML_Forms modules) makes it possible for users to create, copy, and edit ingest
forms, and to affiliate them with Content Models in the repository.

The following Modules are installed as part of the XML Forms package. See inline
documentation for additional details.

* Objective Forms
* Islandora XML Form API
* Islandora XML Schema API
* Islandora XML Form Elements
* Islandora XML Form Builder
* Islandora XML Forms

### Objective Forms

This module allows for the creation of Object Based Drupal Forms. It provides a
number of functions and class for processing/populating forms.

Some important notes.

* Each form element is assigned a unique #hash Form Property to identify it.
* Each form element is stored in a registry, and will persist through out the
  lifetime of the form even if it's removed from the form. Ancestry of Form
  Elements is stored, so if a Form Element is cloned we will be able to
  determine the Form Element that is was cloned from.
* Form Properties can be objects. To define new Form Properties, implement the
  hook `objectify_properties`.
* Forms will be auto-populated from `$form_states['values']`.
* There is a FormStorage class that can be used to store any persistent data.

### Islandora XML Form API

The core of the library this module provides functions for processing XML files
through forms.

In essence this module models

* The form to be processed.
* The Form Properties needed to manipulate XML
* The XML document to be manipulated
* The actions required to generate repeating Form Elements (tabs,tags) from the
  XML document
* The schema needed to determine the insert locations of elements and the
  validation requirements. (included via the Islandora XML Schema API)

### Islandora XML Schema API

This module provides functions for processing Schema files. It's used to
determine where to insert XML Nodes, and how to validate them.

### Islandora XML Form Elements

This module defines custom Drupal Form Elements along with AHAH callbacks.

## Requirements

This module requires the following modules/libraries:

* [Islandora](https://github.com/islandora/islandora)
* [Tuque](https://github.com/islandora/tuque)
* [phplib](https://github.com/islandora/php_lib)
* [Objective forms](https://github.com/islandora/objective_forms)
* [libxml2](http://xmlsoft.org/) version 2.7+

## Installation

Install as usual, see
this](https://drupal.org/documentation/install/modules-themes/modules-7)
for further information.

## Configuration

Create and import forms at Administration » Form Builder (admin/islandora/xmlform).

![image](https://cloud.githubusercontent.com/assets/2371345/9818577/b47086fe-587f-11e5-919c-fd98c5d637b6.png)

You can also set whether a default DC XSLT will be enforced.

![image](https://cloud.githubusercontent.com/assets/2371345/9818551/98ef94ce-587f-11e5-9d26-81bb18e45901.png)

## Documentation

Further documentation for this module is available at the
[wiki](https://wiki.duraspace.org/display/ISLANDORA/XML+Forms).

## FAQ

Q. Can I convert an existing field to any form element type listed in the "Type"
  options under the "Common Form Controls" tab (or create new form elements
  using any form element type)?

A. No. The following element types are not supported for full CRUD
  (create/read/update/delete) operations:

  * checkbox
  * checkboxes
  * date
  * file 
  * managed_file
  * password_confirm
  * radio
  * radios
  * tableselect
  * vertical_tabs
  * weight
  * button
  * image_button
  * submit

## Troubleshooting/Issues

Having problems or solved one? Create an issue, check out the Islandora Google
groups.

* [Users](https://groups.google.com/forum/?hl=en&fromgroups#!forum/islandora)
* [Devs](https://groups.google.com/forum/?hl=en&fromgroups#!forum/islandora-dev)

or contact [discoverygarden](http://support.discoverygarden.ca).
Note if you get error "XML form definition is not valid." during ingest you need
to update your libxml2 version to 2.7+

## Maintainers/Sponsors

Current maintainers:

* [discoverygarden](http://www.discoverygarden.ca)

## Development

If you would like to contribute to this module, please check out the helpful
[Documentation](https://github.com/Islandora/islandora/wiki#wiki-documentation-for-developers),
[Developers](http://islandora.ca/developers) section on Islandora.ca and create
an issue, pull request and or contact
[discoverygarden](http://support.discoverygarden.ca).

## License

[GPLv3](http://www.gnu.org/licenses/gpl-3.0.txt)
