<?php

declare(strict_types=1);

(function() {
    $menuConfiguration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\WEBcoast\EditableMenus\Configuration\MenuConfiguration::class);

    if (!empty($menuConfiguration->getActiveMenus())) {
        $typoScriptSetup = '';

        $menuIndex = 100;
        foreach ($menuConfiguration->getActiveMenus() as $menu => $configuration) {
            $typoScriptSetup .= sprintf('
page.20.dataProcessing.%1$d = %2$s
page.20.dataProcessing.%1$d {
    as = menu%3$s
    levels = %4$s
    if.isTrue.cObject = CONTENT
    if.isTrue.cObject {
        table = pages
        select {
            pidInList.data = leveluid:0
            recursive = 10
            join = tx_editablemenus_menu_pages_mm m ON m.uid_foreign=pages.uid
            where {
                data = leveluid:0
                wrap = {#m.field_name} = "%5$s_menu" AND {#m.uid_local}=|
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
                wrap = {#m.field_name} = "%5$s_menu" AND {#m.uid_local}=|
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
', $menuIndex, \TYPO3\CMS\Frontend\DataProcessing\MenuProcessor::class, \TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($menu), $configuration['levels'] ?? 1, $menu
        );

            ++$menuIndex;
        }

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup($typoScriptSetup);
    }
})();
