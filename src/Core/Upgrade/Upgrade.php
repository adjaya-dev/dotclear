<?php
/**
 * @package     Dotclear
 *
 * @copyright   Olivier Meunier & Association Dotclear
 * @copyright   GPL-2.0-only
 */
declare(strict_types=1);

namespace Dotclear\Core\Upgrade;

use Dotclear\App;
use Dotclear\Core\Session;
use Dotclear\Database\Structure;
use Dotclear\Helper\File\Files;
use Dotclear\Core\Install\Utils;
use Exception;

/**
 * @brief   Dotclear upgrade procedure.
 *
 * This class is called from backend authentication page
 * or CLI command.
 */
class Upgrade
{
    /**
     * Do Dotclear upgrade if necessary.
     *
     * @throws  Exception
     *
     * @return  bool|int
     */
    public static function dotclearUpgrade()
    {
        $version = App::version()->getVersion('core');

        if ($version === '') {
            return false;
        }

        if (version_compare($version, App::config()->dotclearVersion(), '<') == 1 || str_contains(App::config()->dotclearVersion(), 'dev')) {
            try {
                if (App::con()->driver() == 'sqlite') {
                    return false; // Need to find a way to upgrade sqlite database
                }

                # Database upgrade
                $_s = new Structure(App::con(), App::con()->prefix());

                # Fill database structrue
                Utils::dbSchema($_s);

                $si      = new Structure(App::con(), App::con()->prefix());
                $changes = $si->synchronize($_s);

                /* Some other upgrades
                ------------------------------------ */
                $cleanup_sessions = self::growUp($version);

                # Drop content from session table if changes or if needed (only if use Dotclear default session handler)
                if ($changes != 0 || $cleanup_sessions) {
                    App::con()->execute('DELETE FROM ' . App::con()->prefix() . Session::SESSION_TABLE_NAME);
                }

                # Empty templates cache directory
                try {
                    App::cache()->emptyTemplatesCache();
                    App::cache()->emptyModulesStoreCache();
                } catch (Exception) {
                }

                return $changes;
            } catch (Exception $e) {
                throw new Exception(__('Something went wrong with auto upgrade:') .
                    ' ' . $e->getMessage());
            }
        }

        # No upgrade?
        return false;
    }

    /**
     * Make necessary updates in DB and in filesystem.
     *
     * This method reads files from subfolder "GrowUp".
     *
     * @param   null|string     $version    The version
     *
     * @return  bool    True if a session cleanup is requested
     */
    public static function growUp(?string $version): bool
    {
        if ($version === '' || is_null($version)) {
            return false;
        }

        /**
         * Update it in a step that needed sessions to be removed
         *
         * @var     bool
         */
        $cleanup_sessions = false;

        // Prepare upgrades scan
        $path = 'GrowUp';
        $dir  = implode(DIRECTORY_SEPARATOR, [__DIR__, $path, '']);
        $ns   = implode('\\', [__NAMESPACE__, $path, '']);

        // Scan GrowUp folder to find available upgrades
        $upgrades = [];
        foreach (Files::scanDir($dir) as $file) {
            // Need only growup files
            if (!str_contains($file, $path . '_') || !str_contains($file, '.php')) {
                continue;
            }

            // Remove unwanted file name parts and split it by _
            $parts = explode('_', substr($file, 7, -4));

            $equal = '<';
            // remove eq or at least lt
            if (array_pop($parts) == 'eq') {
                $equal = '<=';
                // if eq exists remove also lt
                array_pop($parts);
            }

            $ver = '';
            foreach ($parts as $part) {
                // join by . numeric and _ alpha
                $ver .= (is_numeric($part) ? '.' : '-') . $part;
            }

            // set growup version info
            $upgrades[] = [
                'version' => substr($ver, 1),
                'equal'   => $equal,
                'file'    => $dir . $file,
                'class'   => $ns . substr($file, 0, -4),
            ];
        }

        // Sort growup versions
        usort($upgrades, fn ($a, $b) => version_compare($a['version'], $b['version'], '>') ? 1 : -1);

        // Check upgrades by version
        foreach ($upgrades as $upgrade) {
            // current version need upgrade
            if (version_compare($version, $upgrade['version'], $upgrade['equal'])) {
                require_once $upgrade['file'];
                $cleanup_sessions = $upgrade['class']::init($cleanup_sessions);
            }
        }

        // Set dc version
        App::version()->setVersion('core', App::config()->dotclearVersion());
        Utils::blogDefaults();

        return $cleanup_sessions;
    }

    /**
     * Convert old-fashion serialized array setting to new-fashion json encoded array.
     *
     * @param   string  $ns         Settings workspace name
     * @param   string  $setting    The setting ID
     */
    public static function settings2array(string $ns, string $setting): void
    {
        $strReqSelect = 'SELECT setting_id,blog_id,setting_ns,setting_type,setting_value FROM ' . App::con()->prefix() . App::blogWorkspace()::NS_TABLE_NAME . ' ' .
            "WHERE setting_id = '%s' " .
            "AND setting_ns = '%s' " .
            "AND setting_type = 'string'";
        $rs = App::con()->select(sprintf($strReqSelect, $setting, $ns));
        while ($rs->fetch()) {
            $value = @unserialize($rs->setting_value);
            if (!$value) {
                $value = [];
            }
            settype($value, 'array');
            $value = json_encode($value, JSON_THROW_ON_ERROR);
            $rs2   = 'UPDATE ' . App::con()->prefix() . App::blogWorkspace()::NS_TABLE_NAME . ' ' . // @phpstan-ignore-line
            "SET setting_type='array', setting_value = '" . App::con()->escape($value) . "' " .
            "WHERE setting_id='" . App::con()->escape($rs->setting_id) . "' " .
            "AND setting_ns='" . App::con()->escape($rs->setting_ns) . "' ";
            if ($rs->blog_id == '') {
                $rs2 .= 'AND blog_id IS null';
            } else {
                $rs2 .= "AND blog_id = '" . App::con()->escape($rs->blog_id) . "'"; // @phpstan-ignore-line
            }
            App::con()->execute($rs2);
        }
    }

    /**
     * Convert old-fashion serialized array pref to new-fashion json encoded array.
     *
     * @param   string  $ws     Preferences workspace name
     * @param   string  $pref   The preference ID
     */
    public static function prefs2array(string $ws, string $pref): void
    {
        $strReqSelect = 'SELECT pref_id,user_id,pref_ws,pref_type,pref_value FROM ' . App::con()->prefix() . App::userWorkspace()::WS_TABLE_NAME . ' ' .
            "WHERE pref_id = '%s' " .
            "AND pref_ws = '%s' " .
            "AND pref_type = 'string'";
        $rs = App::con()->select(sprintf($strReqSelect, $pref, $ws));
        while ($rs->fetch()) {
            $value = @unserialize($rs->pref_value);
            if (!$value) {
                $value = [];
            }
            settype($value, 'array');
            $value = json_encode($value, JSON_THROW_ON_ERROR);
            $rs2   = 'UPDATE ' . App::con()->prefix() . App::userWorkspace()::WS_TABLE_NAME . ' ' . // @phpstan-ignore-line
            "SET pref_type='array', pref_value = '" . App::con()->escape($value) . "' " .
            "WHERE pref_id='" . App::con()->escape($rs->pref_id) . "' " .
            "AND pref_ws='" . App::con()->escape($rs->pref_ws) . "' ";
            if ($rs->user_id == '') {
                $rs2 .= 'AND user_id IS null';
            } else {
                $rs2 .= "AND user_id = '" . App::con()->escape($rs->user_id) . "'"; // @phpstan-ignore-line
            }
            App::con()->execute($rs2);
        }
    }

    /**
     * Remove files and/or folders.
     *
     * @param   array<string>|null  $files      The files
     * @param   array<string>|null  $folders    The folders
     */
    public static function houseCleaning(?array $files = null, ?array $folders = null): void
    {
        if (App::config()->dotclearRoot() === '') {
            return;
        }

        if (is_array($files)) {
            foreach ($files as $f) {
                if (file_exists(App::config()->dotclearRoot() . '/' . $f)) {
                    @unlink(App::config()->dotclearRoot() . '/' . $f);
                }
            }
        }

        if (is_array($folders)) {
            foreach ($folders as $f) {
                if (file_exists(App::config()->dotclearRoot() . '/' . $f)) {
                    Files::deltree(App::config()->dotclearRoot() . '/' . $f);
                }
            }
        }
    }
}
