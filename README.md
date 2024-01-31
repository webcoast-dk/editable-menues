# Editable menus

TYPO3 CMS extension to create menus independent of the page tree/main menu where
editors can change the menu items by selecting the pages, they want in the menu.

## Installation
```shell
composer req webcoast/editable-menus
```

## Configuration
The extension checks all active packages for a `Configuration/Menus.php` file.
For this extension to correctly determine the extension key, a `composer.json` file
must exist and contain the `extra.typo3/cms.extension-key` property.

The file must return an array with a valid menu configuration:
```php
<?php

return [
    'the_menu_identifier' => [
        // The menu configuration
    ],
    // ... more menu configurations
];
```

**Attention:** It is recommended to use snake case (lowercase with underscore) for
the menu identifier to make the conversion work in several places throughout the
menu processing.

### Minimal configuration
The minimal menu configuration is just an empty array:

```php
<?php

return [
    'header' => [], // This creates a field called `header_menu` in the root page properties
];
```

### Full configuration
```php
<?php

return [
    'header' => [
        'label' => 'My menu label', // Explicitly define a label, can also be a LLL reference
        'description' => 'A description explaining the usage of this menu', // Explicitly define a description, can also be a LLL reference
        'exclude' => false, // Do not make the menu field an exclude field, defaults to true if not defined (null)
        'displayCond' => 'FIELD:is_siteroot:=:1', // Define the display condition of this field. Supports all valid displayCond values. Defaults to `FIELD:is_siteroot:=:1`
        'levels' => 2, // Number of levels for the menu rendering in TypoScript. Defaults to `1`
        'disabled' => true, // Defaults to false, used to disable previously defined menus
    ],
];
```

## Override existing menus
It is possible to override existing menus to change their configuration or disabled them.

To do this, just use the same menu identifier. The configuration is merged using
`array_replace_recursive`.

```php
<?php

return [
    'header' => [
        // Add custom label and description for field `header_menu`
        'label' => 'LLL:EXT:sitepackage/Resources/Private/Language/Menus.xlf:header.label',
        'description' => 'LLL:EXT:sitepackage/Resources/Private/Language/Menus.xlf:header.description',
    ],
    'footer' => [
        // Disable the footer menu, meaning the field is not shown in the backend and the SQL for the database column would not be generated
        'disabled' => true,
    ],
];
```

## Usage

### Backend interface
For each configured menu a field is added to the site root properties under the
`Menu settings` tab.

By default, the fields are added as exclude fields, where editors need explicit permission
to see it. This can be disabled for each menu individually.

The field name for the menu is generated by appending `_menu` to the menu identifier:
* `header` becomes `header_menu`
* `footer_lower` becomes `footer_lower_menu`

The label and description are - if not set explicitly - determined automatically by
taking by building a LLL references 
* `LLL:EXT:{ext-key}/Resources/Private/Language/Menus.xlf:{menu-identifier}.label` and
* `LLL:EXT:{ext-key}/Resources/Private/Language/Menus.xlf:{menu-identifier}.description`
where `{ext-key}` is the extension key from the extension originally defining the menu,
and `{menu-identifier}` is the menu identifier like `header` or `footer_lower`.

### Fluid page template - Data processing
TypoScript data processing configuration is automatically added for each configured menu.
The variable that holds the menu data in the Fluid template is generated from the identifier
by converting the snake_case to UpperCamelCase and prepending `menu` to it:

* Identifier `header` becomes `menuHeader` in the Fluid template
* Identifier `footer_lower` becomes `menuFooterLower`

The default TypoScript should be sufficient for most use-cases. However, you can
override it with your own TypoScript to tweak it, if necessary, e.g. to add sub data processors.

### SQL
All necessary SQL to update the database is generated for all configured menus
and will be shown/executed with the next database compare/schema update.
