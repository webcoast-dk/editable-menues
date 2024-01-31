<?php

declare(strict_types=1);

namespace WEBcoast\EditableMenus\EventListener;

use TYPO3\CMS\Core\Database\Event\AlterTableDefinitionStatementsEvent;
use WEBcoast\EditableMenus\Configuration\MenuConfiguration;

class DynamicMenuFieldsEventListener
{
    protected MenuConfiguration $menuConfiguration;

    public function __construct(MenuConfiguration $menuConfiguration)
    {
        $this->menuConfiguration = $menuConfiguration;
    }

    public function __invoke(AlterTableDefinitionStatementsEvent $event): void
    {
        $sqlData = $event->getSqlData();


        if (!empty($this->menuConfiguration->getActiveMenus())) {
            $sqlData[] = 'CREATE TABLE tx_editablemenus_menu_pages_mm (uid_local int(10) unsigned DEFAULT 0 NOT NULL, uid_foreign int(10) unsigned DEFAULT 0 NOT NULL, sorting int(11) NOT NULL DEFAULT 0, sorting_foreign int(11) NOT NULL DEFAULT 0, field_name varchar(200) DEFAULT NULL, PRIMARY KEY (uid_local, uid_foreign, field_name), INDEX `uid_local` (uid_local), INDEX `uid_foreign` (uid_foreign));';

            foreach ($this->menuConfiguration->getActiveMenus() as $menu => $configuration) {
                $sqlData[] = sprintf('CREATE TABLE pages (%s int(11) DEFAULT NULL);', $menu . '_menu');
            }
        }

        $event->setSqlData($sqlData);
    }
}
