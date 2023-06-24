<?php
/**
 * @brief userPref, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
declare(strict_types=1);

namespace Dotclear\Plugin\userPref;

use dcAdmin;
use Dotclear\Core\Process;

class Backend extends Process
{
    public static function init(): bool
    {
        // Dead but useful code (for l10n)
        __('user:preferences') . __('Manage every user preference directive');

        return (static::$init = My::checkContext(My::BACKEND));
    }

    public static function process(): bool
    {
        if (static::$init) {
            My::addBackendMenuItem(dcAdmin::MENU_SYSTEM);
        }

        return static::$init;
    }
}
