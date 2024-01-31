<?php

declare(strict_types=1);

(function () {
    $menuConfiguration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\WEBcoast\EditableMenus\Configuration\MenuConfiguration::class);

    if (!empty($menuConfiguration->getActiveMenus())) {
        $fieldConfigurations = [];
        foreach ($menuConfiguration->getActiveMenus() as $menu => $configuration) {
            $fieldConfigurations[$menu . '_menu'] = [
                'label' => $configuration['label'] ?? sprintf('LLL:EXT:%s/Resources/Private/Language/Menus.xlf:%s.label', $configuration['extension'], $menu),
                'description' => $configuration['description'] ?? sprintf('LLL:EXT:%s/Resources/Private/Language/Menus.xlf:%s.description', $configuration['extension'], $menu),
                'exclude' => $configuration['exclude'] ?? true,
                'config' => [
                    'type' => 'group',
                    'internal_type' => 'db',
                    'allowed' => 'pages',
                    'MM' => 'tx_editablemenus_menu_pages_mm',
                    'MM_match_fields' => [
                        'field_name' => $menu . '_menu'
                    ],
                    'minitems' => 0,
                    'autoSizeMax' => 20,
                ],
                'displayCond' => $configuration['displayCond'] ?? 'FIELD:is_siteroot:=:1',
            ];
        }

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages', $fieldConfigurations);
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('pages', '--div--;LLL:EXT:editable_menus/Resources/Private/Language/Menus.xlf:tabs.menus.title, ' . implode(', ', array_keys($fieldConfigurations)));
    }
})();
