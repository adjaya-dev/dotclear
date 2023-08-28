<?php
/**
 * @brief dcLegacyEditor, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
declare(strict_types=1);

namespace Dotclear\Plugin\dcLegacyEditor;

use dcCore;
use Dotclear\Core\Backend\Notices;
use Dotclear\Core\Backend\Page;
use Dotclear\Core\Core;
use Dotclear\Core\Process;
use Exception;

class Manage extends Process
{
    public static function init(): bool
    {
        Core::backend()->editor_is_admin   = self::status(My::checkContext(My::MANAGE));
        Core::backend()->editor_std_active = self::status(My::checkContext(My::MANAGE)) && My::settings()->active;

        return self::status();
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        if (!empty($_POST['saveconfig'])) {
            try {
                Core::backend()->editor_std_active = (empty($_POST['dclegacyeditor_active'])) ? false : true;
                My::settings()->put('active', Core::backend()->editor_std_active, 'boolean');

                Notices::addSuccessNotice(__('The configuration has been updated.'));
                My::redirect();
            } catch (Exception $e) {
                dcCore::app()->error->add($e->getMessage());
            }
        }

        return true;
    }

    /**
     * Renders the page.
     */
    public static function render(): void
    {
        Page::openModule(My::name());

        require My::path() . '/tpl/index.php';

        Page::closeModule();
    }
}
