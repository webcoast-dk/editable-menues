<?php

declare(strict_types=1);

(function() {
    $menuConfiguration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\WEBcoast\EditableMenus\Configuration\MenuConfiguration::class);

    if (!empty($menuConfiguration->getActiveMenus())) {
        $typoScriptSetup = '';

        $menuIndex = 100;
        foreach ($menuConfiguration->getActiveMenus() as $menu => $configuration) {
            $typoScriptSetup .= sprintf(
<<<EOTS
page.20.dataProcessing.{$menuIndex} = %s
page.20.dataProcessing.{$menuIndex} {
    as = menu%s
    levels = %s
    special = list
    special.value.cObject = CONTENT
    special.value.cObject {
        table = pages
        select {
            pidInList.data = leveluid:0
            recursive = 10
            join = tx_editablemenus_menu_pages_mm m ON m.uid_foreign=pages.uid
            where {
                data = leveluid:0
                wrap = {#m.field_name} = "%s_menu" AND {#m.uid_local}=|
            }
            orderBy = m.sorting asc
        }
        renderObj = TEXT
        renderObj {
            field = uid
            wrap = |,
        }
        stdWrap.substring = 0,-1
    }
}

EOTS, \TYPO3\CMS\Frontend\DataProcessing\MenuProcessor::class, \TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($menu), $configuration['levels'] ?? 1, $menu
        );

            ++$menuIndex;
        }

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup($typoScriptSetup);
    }
})();
