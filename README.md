# Less Template Companion
##### A Joomla 3 Plugin
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/6ec183dc0cc24de5bbadf081863e4a60)](https://www.codacy.com/app/Gileba/plg_system_lesstemplatecompanion?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=Gileba/plg_system_lesstemplatecompanion&amp;utm_campaign=Badge_Grade)
[![Build Status](https://travis-ci.org/Gileba/plg_system_lesstemplatecompanion.svg?branch=master)](https://travis-ci.org/Gileba/plg_system_lesstemplatecompanion)
[![CLA assistant](https://cla-assistant.io/readme/badge/Gileba/plg_system_lesstemplatecompanion)](https://cla-assistant.io/Gileba/plg_system_lesstemplatecompanion)
[![SemVer](http://img.shields.io/SemVer/2.0.0.png)](http://semver.org/spec/v2.0.0.html)

---

## Installation
1. Download the plugin from the [release section](https://github.com/Gileba/ttactua_mobile/releases)
2. Install with the Joomla Installer
3. Activate the plugin in the Joomla backend.

---

## Minimum Requirements
+ Joomla 3 (minor versions 4 and up) _(This plugin uses the JLess-class in Joomla, which was added in version 3.4 and is marked to be removed from Joomla 4)_
+ A compatible template

---

## Usage
This plugin compiles .less-files into a site specific .css-file on two occasions
1. On saving a compatible Joomla Template, a new .css-file will always be created saving all specific parameters as less-variables
2. Any time a .less-file was changed on the server, a new .css-file will be created taking those changes into account. The template parameters are implemented as well.

---

## Options
#### Force compilation
When turned on, every page load will trigger a new compilation. It is recommended to only activate this option when developping a new template.
#### Preserve comments
When turned on, comments made in the source files (Less) will be transferred to the destination file (CSS).
#### Format
  + Compressed: Compresses the .css output for minimal load time and file size
  + Joomla: The standard Joomla formatter
  + Less JS: Same style used in LESS for JavaScript
  + Classic: Lessphp’s original formatter"

---

## Template Developer Instructions
The plugin only passes specific parameters into the Less-parser. If you want a template parameter to be parsed, you have to prefix it with 'ltc\_'. This prefix will be stripped from the name before adding it to the Less-parser.

_Example_
```XML
<field name="ltc_color-main" type="color" default="#145CAE" label="TPL_GILEBA_COLOR_MAIN" description="TPL_GILEBA_COLOR_MAIN_DESC" />
```
will become
```LESS
@color-main: #145CAE;
```
