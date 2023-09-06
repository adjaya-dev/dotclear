<?php
/**
 * @package Dotclear
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
declare(strict_types=1);

namespace Dotclear\Interface\Module;

use Dotclear\Module\ModuleDefine;

/**
 * Modules handler interface.
 */
interface ModulesInterface
{
    /** @var    int     Return code for package installation */
    public const PACKAGE_INSTALLED = 1;

    /** @var    int     Return code for package update */
    public const PACKAGE_UPDATED = 2;

    /** @var    string  Name of module old style installation file */
    public const MODULE_FILE_INSTALL = '_install.php';

    /** @var    string  Name of module old style initialization file */
    public const MODULE_FILE_INIT = '_init.php';

    /** @var    string  Name of module define file */
    public const MODULE_FILE_DEFINE = '_define.php';

    /** @var    string  Name of module old style prepend file */
    public const MODULE_FILE_PREPEND = '_prepend.php';

    /** @var    string  Name of module old style backend file */
    public const MODULE_FILE_ADMIN = '_admin.php';

    /** @var    string  Name of module old style configuration file */
    public const MODULE_FILE_CONFIG = '_config.php';

    /** @var    string  Name of module old style manage file */
    public const MODULE_FILE_MANAGE = 'index.php';

    /** @var    string  Name of module old style frontend file */
    public const MODULE_FILE_PUBLIC = '_public.php';

    /** @var    string  Name of module old style xmlrpc file */
    public const MODULE_FILE_XMLRPC = '_xmlrpc.php';

    /** @var    string  Name of module hard deactivation file */
    public const MODULE_FILE_DISABLED = '_disabled';

    /** @var    string  The update locked file name */
    public const MODULE_FILE_LOCKED = '_locked';

    /** @var    string  Directory for module namespace */
    public const MODULE_CLASS_DIR = 'src';

    /** @var    string  Name of module prepend class (ex _prepend.php) */
    public const MODULE_CLASS_PREPEND = 'Prepend';

    /** @var    string  Name of module installation class (ex _install.php) */
    public const MODULE_CLASS_INSTALL = 'Install';

    /** @var    string  Name of module backend class (ex _admin.php) */
    public const MODULE_CLASS_ADMIN = 'Backend';

    /** @var    string  Name of module configuration class (ex _config.php) */
    public const MODULE_CLASS_CONFIG = 'Config';

    /** @var    string  Name of module manage class (ex index.php) */
    public const MODULE_CLASS_MANAGE = 'Manage';

    /** @var    string  Name of module frontend class (ex _public.php) */
    public const MODULE_CLASS_PUPLIC = 'Frontend';

    /** @var    string  Name of module XMLRPC services class (ex _xmlrpc.php) - obsolete since 2.24 */
    public const MODULE_CLASS_XMLRPC = 'Xmlrpc';

    /**
     * Get first ocurrence of a module's defined properties.
     *
     * This method always returns a ModuleDefine class,
     * if module definition does not exist, it is created on the fly
     * with default properties.
     *
     * @param   string                  $id         The module identifier
     * @param   array<string,mixed>     $search     The search parameters
     *
     * @return  ModuleDefine    The first matching module define or properties
     */
    public function getDefine(string $id, array $search = []): ModuleDefine;

    /**
     * Get modules defined properties.
     *
     * More than one module can have same id in this stack.
     *
     * @param   array<string,mixed>     $search     The search parameters
     * @param   bool                    $to_array   Return arrays of modules properties
     *
     * @return  array   The modules defines or properties
     */
    public function getDefines(array $search = [], bool $to_array = false): array;

    /**
     * Checks all modules dependencies.
     *
     * Fills in the following information in module :
     *
     *  - missing : list reasons why module cannot be enabled. Not set if module can be enabled
     *
     *  - using : list reasons why module cannot be disabled. Not set if module can be disabled
     *
     *  - implies : reverse dependencies
     *
     * @param   ModuleDefine    $module     The module to check
     * @param   bool            $to_error   Add dependencies fails to errors
     */
    public function checkDependencies(ModuleDefine $module, $to_error = false): void;

    /**
     * Disables the dep modules.
     *
     * If module has missing dep and is not yet in hard disbaled state (_disabled) goes in.
     *
     * @return  array<int,string>   The reasons to disable modules
     */
    public function disableDepModules(): array;

    /**
     * Should run in safe mode?
     *
     * @param   null|bool   $mode   Mode, null to read current mode
     *
     * @return  bool
     */
    public function safeMode(?bool $mode = null): bool;

    /**
     * Loads modules. <var>$path</var> could be a separated list of paths
     * (path separator depends on your OS).
     *
     * <var>$ns</var> indicates if an additionnal file needs to be loaded on plugin
     * load, value could be:
     * - admin (loads module's _admin.php)
     * - public (loads module's _public.php)
     * - xmlrpc (loads module's _xmlrpc.php)
     *
     * <var>$lang</var> indicates if we need to load a lang file on plugin
     * loading.
     *
     * @param   string  $path   The path
     * @param   string  $ns     The namespace (context as 'public', 'admin', ...)
     * @param   string  $lang   The language
     */
    public function loadModules(string $path, ?string $ns = null, ?string $lang = null): void;

    /**
     * Load the _define.php file of the given module.
     *
     * @param   string  $dir    The dir
     * @param   string  $id     The module identifier
     */
    public function requireDefine(string $dir, string $id): void;

    /**
     * This method registers a module in modules list.
     *
     * @param   string  $name           The module name
     * @param   string  $desc           The module description
     * @param   string  $author         The module author
     * @param   string  $version        The module version
     * @param   mixed   $properties     The properties
     */
    public function registerModule(string $name, string $desc, string $author, string $version, $properties = []): void;

    /**
     * Reset modules list.
     */
    public function resetModulesList(): void;

    /**
     * Check if there are no modules loaded.
     *
     * @return  bool    True on no modules
     */
    public function isEmpty(): bool;

    /**
     * Install a Package.
     *
     * @param   string              $zip_file   The zip file
     * @param   ModulesInterface    $modules    The modules
     *
     * @throws  \Exception
     *
     * @return  int
     */
    public static function installPackage(string $zip_file, ModulesInterface &$modules): int;

    /**
     * This method installs all modules having a _install file.
     *
     * @see     self::installModule
     *
     * @return  array
     */
    public function installModules(): array;

    /**
     * Install a module.
     *
     * This method installs module with ID <var>$id</var> and having a _install
     * file. This file should throw exception on failure or true if it installs
     * successfully.
     * <var>$msg</var> is an out parameter that handle installer message.
     *
     * @param   string  $id     The identifier
     * @param   string  $msg    The message
     *
     * @return  null|bool
     */
    public function installModule(string $id, string &$msg): ?bool;

    /**
     * Delete a module.
     *
     * @param   string  $id         The module identifier
     * @param   bool    $disabled   Is module disabled
     *
     * @throws  \Exception
     */
    public function deleteModule(string $id, bool $disabled = false): void;

    /**
     * Deactivate a module.
     *
     * @param   string  $id     The identifier
     *
     * @throws  \Exception
     */
    public function deactivateModule(string $id): void;

    /**
     * Activate a module.
     *
     * @param   string  $id     The identifier
     *
     * @throws  \Exception
     */
    public function activateModule(string $id): void;

    /**
     * Clone a module.
     *
     * @param   string  $id     The module identifier
     */
    public function cloneModule(string $id): void;

    /**
     * Load module l10n file.
     *
     * This method will search for file <var>$file</var> in language
     * <var>$lang</var> for module <var>$id</var>.
     *<var>$file</var> should not have any extension.
     *
     * @param   string  $id     The module identifier
     * @param   string  $lang   The language code
     * @param   string  $file   The filename (without extension)
     */
    public function loadModuleL10N(string $id, ?string $lang, string $file): void;

    /**
     * Loads module l10n resources.
     *
     * @param   string  $id     The module identifier
     * @param   string  $lang   The language code
     */
    public function loadModuleL10Nresources(string $id, ?string $lang): void;

    /**
     * Returns all modules associative array or only one module if <var>$id</var> is present.
     *
     * @deprecated  since 2.26, use self::getDefines() instead
     *
     * @param   string  $id     The optionnal module identifier
     *
     * @return  array   The module(s).
     */
    public function getModules(?string $id = null): array;

    /**
     * Gets all modules (whatever are their statuses) or only one module if <var>$id</var> is present.
     *
     * @deprecated  since 2.26, use self::getDefines() instead
     *
     * @param   string  $id     The optionnal module identifier
     *
     * @return  array  The module(s).
     */
    public function getAnyModules(?string $id = null): array;

    /**
     * Determines if module exists and is enabled.
     *
     * @param   string  $id     The module identifier
     *
     * @return  bool    True if module exists, False otherwise.
     */
    public function moduleExists(string $id): bool;

    /**
     * Gets the disabled modules.
     *
     * @deprecated  since 2.26, use self::getDefines() instead
     *
     * @return  array   The disabled modules.
     */
    public function getDisabledModules(): array;

    /**
     * Gets the hard disabled modules.
     *
     * @deprecated  since 2.26, use self::getDefines() instead
     *
     * @return  array  The hard disabled modules.
     */
    public function getHardDisabledModules(): array;

    /**
     * Gets the soft disabled modules.
     *
     * (safe mode and not hard disabled)
     *
     * @deprecated  since 2.26, use self::getDefines() instead
     *
     * @return  array  The soft disabled modules.
     */
    public function getSoftDisabledModules(): array;

    /**
     * Returns root path for module with ID <var>$id</var>.
     *
     * @deprecated  since 2.26, use self::moduleInfo() instead
     *
     * @param   string  $id     The module identifier
     *
     * @return  null|string
     */
    public function moduleRoot(string $id): ?string;

    /**
     * Get a module information.
     *
     * Returns a module information that could be:
     * - root
     * - name
     * - desc
     * - author
     * - version
     * - permissions
     * - priority
     * - …
     *
     * @param   string  $id     The module identifier
     * @param   string  $info   The information
     *
     * @return  mixed
     */
    public function moduleInfo(string $id, string $info): mixed;

    /**
     * Loads namespace <var>$ns</var> specific files for all modules.
     *
     * @deprecated  since 2.27, use nothing instead !
     *
     * @param   string  $ns
     */
    public function loadNsFiles(?string $ns = null): void;

    /**
     * Loads namespace <var>$ns</var> specific file for module with ID
     * <var>$id</var>.
     *
     * @param   string  $id     The module identifier
     * @param   string  $ns     Namespace name
     */
    public function loadNsFile(string $id, ?string $ns = null): void;

    /**
     * Initialise <var>$ns</var> specific namespace for module with ID
     * <var>$id</var>.
     *
     * @param   string  $id         The module identifier
     * @param   string  $ns         Process name
     * @param   bool    $process    Execute process
     *
     * @return  string  The fully qualified class name on success. Empty string on fail.
     */
    public function loadNsClass(string $id, string $ns, bool $process = true): string;

    /**
     * Gets the errors.
     *
     * @return  array<int,string>   The errors.
     */
    public function getErrors(): array;
}
