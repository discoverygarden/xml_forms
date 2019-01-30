CONTENTS OF THIS FILE
---------------------
* Introduction
* Troubleshooting

INTRODUCTION
------------

Holds Drupal form elements for re-use.

TROUBLESHOOTING
---------------

The creative commons element must not have spaces in it's name and must be in
the root of a form to work.
There seems to be a bug in the Creative Commons API triggered by certain
combinations of _License Jurisdiction_ and other parameters. Querying, e.g.,
"Yes, as long as others share alike" for _Allow modifications of your work?_
and "Finland" for _License Jurisdiction_ results in a CC-BY-SA, resp.
CC-BY-NC-SA, _4.0 International_ license, instead of the corresponding _1.0
Finland_ version (which exists and is not deprecated). The only way to obtain
the correct license in this case is to enter
`http://creativecommons.org/licenses/by-sa/1.0/fi/`, resp.
`http://creativecommons.org/licenses/by-nc-sa/1.0/fi/`, _manually_ (choose
"Manually select a license URI" for _Select a license type_). Also, this kind
of licenses cannot be set by administrators in Form Builder as _Default Value_
for the Creative Commons Form Element.
