<?php

declare(strict_types=1);


namespace WEBcoast\EditableMenus\Configuration;


use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\SingletonInterface;

class MenuConfiguration implements SingletonInterface
{
    protected array $menus = [];

    public function __construct(PackageManager $packageManager)
    {
        foreach ($packageManager->getActivePackages() as $package) {
            if (file_exists($menuConfigFile = $package->getPackagePath() . 'Configuration/Menus.php')) {
                $addedMenus = require_once $menuConfigFile;
                foreach ($addedMenus as $menu => &$configuration) {
                    if (!array_key_exists($menu, $this->menus)) {
                        $configuration['extension'] = $package->getValueFromComposerManifest('extra')?->{'typo3/cms'}?->{'extension-key'};
                    }
                }
                $this->menus = array_replace_recursive($this->menus, $addedMenus);
            }
        }
    }

    public function getMenus(): array
    {
        return $this->menus;
    }

    public function getActiveMenus(): array
    {
        return array_filter($this->menus, function ($menu) {
            return !($menu['disabled'] ?? null);
        });
    }
}
