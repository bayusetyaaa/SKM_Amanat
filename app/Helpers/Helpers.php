<?php

namespace App\Helpers;

class Helpers
{
    public static function appClasses()
    {
        $data = [
            'myLayout' => 'vertical',
            'myTheme' => 'theme-default',
            'myStyle' => 'light',
            'hasCustomizer' => false,
            'showDropdownOnHover' => true,
            'displayCustomizer' => false,
            'contentLayout' => 'compact',
            'headerType' => 'fixed',
            'navbarType' => 'fixed',
            'isNavbar' => true,
            'footerFixed' => false,
            'menuFixed' => true,
            'menuCollapsed' => false,
            'menuFlipped' => false,
            'menuOffcanvas' => false,
            'customizerControls' => [],
            'layoutTheme' => 'light',
            'navbarBgColor' => '',
            'menuBgColor' => '',
            'isMenu' => true,
            'isFooter' => true,
        ];

        return $data;
    }

    public static function updatePageConfig($pageConfigs)
    {
        $demo = 'custom';
        if (isset($pageConfigs)) {
            if (count($pageConfigs) > 0) {
                foreach ($pageConfigs as $config => $val) {
                    Config::set('custom.' . $demo . '.' . $config, $val);
                }
            }
        }
    }
}
