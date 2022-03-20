# Template Companion
##### A Joomla 3 Plugin

## Installation
1. Download the plugin from the [release section](https://github.com/Gileba/plg_system_templatecompanion/releases)
2. Install with the Joomla Installer

---

## Minimum Requirements (v1.0.1)
+ Joomla 3 (minor versions 4 and up) _(This plugin uses the JLess-class in Joomla, which was added in version 3.4 and has been removed from Joomla 4)_
+ A compatible template

---

## Minimum Requirements (v2.0.0)
+ Joomla 3.10 or Joomla 4
+ A compatible template

---

## Usage
This plugin compiles .less-files into a site specific .css-file on three occasions
1. On saving a compatible Joomla Template, a new .css-file will always be created saving all specific parameters as less-variables
2. Any time a .less-file was changed on the server, a new .css-file will be created taking those changes into account. The template parameters are implemented as well.
3. You can force a compile by removing the destination .css-file.

---

## Options
#### Force compilation
When turned on, every page load will trigger a new compilation. It is recommended to only activate this option when developing a new template.
#### Preserve comments (removed in version 2)
When turned on, comments made in the source files (Less) will be transferred to the destination file (CSS).
#### Format
  + Compressed: Compresses the .css output for minimal load time and file size
  + Joomla: The standard Joomla formatter (removed in version 2)
  + Less JS: Same style used in LESS for JavaScript
  + Classic: Lessphp’s original formatter

---

## Template Developer Instructions
#### Marking your template compatible with the plugin
The plugin only processes the parameters if it gets a sign that the template is compatible. You do that by adding the hidden parameter "useLESS" to the template manifest file.

```XML
<field name="useLESS" type="hidden" default="true" />
```

#### Passing parameters
The plugin only passes specific parameters into the Less-parser. If you want a template parameter to be parsed, you have to prefix it with 'tc\_'. This prefix will be stripped from the name before adding it to the Less-parser.

_Example_
```XML
<field name="tc_color-main" type="color" default="#145CAE" label="TPL_XYZ_COLOR_MAIN" description="TPL_XYZ_COLOR_MAIN_DESC" />
```
will become
```LESS
@color-main: #145CAE;
```

#### File locations
* The less file should be in template/less/template.less (include all imports in this file)
* The final css file will be stored in template/css/template.css (add this folder to your template)
