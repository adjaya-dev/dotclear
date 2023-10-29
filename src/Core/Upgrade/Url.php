<?php
/**
 * @package Dotclear
 * @subpackage Backend
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
declare(strict_types=1);

namespace Dotclear\Core\Upgrade;

use ArrayObject;
use Dotclear\App;
use Dotclear\Core\Backend\Url as BackendUrl;
use Dotclear\Helper\Html\Form\Hidden;
use Dotclear\Helper\Network\Http;
use Exception;

/**
 * URL Handler for upgrade urls
 */
class Url extends BackendUrl
{
    /**
     * @var    string  Default upgrade index page
     */
    public const INDEX = 'upgrade.php';

    /**
     * @var    string  Default backend index page
     */
    public const BACKEND = 'index.php';

    /**
     * Constructs a new instance.
     *
     * @throws  Exception   If not in upgrade context
     */
    public function __construct()
    {
        if (!App::task()->checkContext('UPGRADE')) {
            throw new Exception('Application is not in upgrade context.', 500);
        }

        $this->urls = new ArrayObject();

        // set required URLs
        $this->register('upgrade.auth', 'Auth');
        $this->register('upgrade.logout', 'Logout');
        $this->register('admin.home', self::BACKEND);

        $this->register('upgrade.home', 'Home');
        $this->register('upgrade.upgrade', 'Upgrade');
        $this->register('upgrade.backup', 'Backup');
        $this->register('upgrade.langs', 'Langs');
        $this->register('upgrade.plugins', 'Plugins');
        $this->register('upgrade.tools', 'Tools');
        $this->register('upgrade.rest', 'Rest');

        // we don't care of admin process for FileServer
        $this->register('load.plugin.file', self::INDEX, ['pf' => 'dummy.css']);
        $this->register('load.var.file', self::INDEX, ['vf' => 'dummy.json']);
    }

    /**
     * Set default upgrade URLs handlers.
     */
    public function setDefaultUrls(): void
    {
    }
}
