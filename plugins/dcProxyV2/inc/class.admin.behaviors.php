<?php
/**
 * @brief dcProxyV2, a plugin for Dotclear 2
 *
 * Admin behaviours
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
class dcProxyV2AdminBehaviors
{
    // Count : 55

    public static function adminBlogFilter($filters)
    {
        return dcCore::app()->behavior->callBehavior('adminBlogFilter', dcCore::app(), $filters);
    }
    public static function adminBlogListHeader($rs, $cols)
    {
        return dcCore::app()->behavior->callBehavior('adminBlogListHeader', dcCore::app(), $rs, $cols);
    }
    public static function adminBlogListValue($rs, $cols)
    {
        return dcCore::app()->behavior->callBehavior('adminBlogListValue', dcCore::app(), $rs, $cols);
    }
    public static function adminBlogPreferencesForm($blog_settings)
    {
        return dcCore::app()->behavior->callBehavior('adminBlogPreferencesForm', dcCore::app(), $blog_settings);
    }
    public static function adminBlogsActionsPage($that)
    {
        return dcCore::app()->behavior->callBehavior('adminBlogsActionsPage', dcCore::app(), $that);
    }
    public static function adminColumnsLists($cols)
    {
        return dcCore::app()->behavior->callBehavior('adminColumnsLists', dcCore::app(), $cols);
    }
    public static function adminCommentFilter($filters)
    {
        return dcCore::app()->behavior->callBehavior('adminCommentFilter', dcCore::app(), $filters);
    }
    public static function adminCommentListHeader($rs, $cols)
    {
        return dcCore::app()->behavior->callBehavior('adminCommentListHeader', dcCore::app(), $rs, $cols);
    }
    public static function adminCommentListValue($rs, $cols)
    {
        return dcCore::app()->behavior->callBehavior('adminCommentListValue', dcCore::app(), $rs, $cols);
    }
    public static function adminCommentsActions($getRS, $getAction, $getRedirection)
    {
        return dcCore::app()->behavior->callBehavior('adminCommentsActions', dcCore::app(), $getRS, $getAction, $getRedirection);
    }
    public static function adminCommentsActionsPage($that)
    {
        return dcCore::app()->behavior->callBehavior('adminCommentsActionsPage', dcCore::app(), $that);
    }
    public static function adminCommentsSpamForm()
    {
        return dcCore::app()->behavior->callBehavior('adminCommentsSpamForm', dcCore::app());
    }
    public static function adminCurrentThemeDetails($id, $define)
    {
        return dcCore::app()->behavior->callBehavior('adminCurrentThemeDetails', dcCore::app(), $id, $define->dump());
    }
    public static function adminDashboardContents($__dashboard_contents)
    {
        return dcCore::app()->behavior->callBehavior('adminDashboardContents', dcCore::app(), $__dashboard_contents);
    }
    public static function adminDashboardFavorites($favorites)
    {
        return dcCore::app()->behavior->callBehavior('adminDashboardFavorites', dcCore::app(), $favorites);
    }
    public static function adminDashboardFavs($f)
    {
        return dcCore::app()->behavior->callBehavior('adminDashboardFavs', dcCore::app(), $f);
    }
    public static function adminDashboardFavsIcon($k, $icons)
    {
        return dcCore::app()->behavior->callBehavior('adminDashboardFavsIcon', dcCore::app(), $k, $icons);
    }
    public static function adminDashboardItems($__dashboard_items)
    {
        return dcCore::app()->behavior->callBehavior('adminDashboardItems', dcCore::app(), $__dashboard_items);
    }
    public static function adminDashboardOptionsForm()
    {
        return dcCore::app()->behavior->callBehavior('adminDashboardOptionsForm', dcCore::app());
    }
    public static function adminFiltersLists($sorts)
    {
        return dcCore::app()->behavior->callBehavior('adminFiltersLists', dcCore::app(), $sorts);
    }
    public static function adminMediaFilter($filters)
    {
        return dcCore::app()->behavior->callBehavior('adminMediaFilter', dcCore::app(), $filters);
    }
    public static function adminModulesListGetActions($list, $define)
    {
        return dcCore::app()->behavior->callBehavior('adminModulesListGetActions', $list, $define->getId(), $define->dump());
    }
    public static function adminPageFooter($text)
    {
        return dcCore::app()->behavior->callBehavior('adminPageFooter', dcCore::app(), $text);
    }
    public static function adminPagesActionsPage($that)
    {
        return dcCore::app()->behavior->callBehavior('adminPagesActionsPage', dcCore::app(), $that);
    }
    public static function adminPagesListHeader($rs, $cols)
    {
        return dcCore::app()->behavior->callBehavior('adminPagesListHeader', dcCore::app(), $rs, $cols);
    }
    public static function adminPagesListValue($rs, $cols)
    {
        return dcCore::app()->behavior->callBehavior('adminPagesListValue', dcCore::app(), $rs, $cols);
    }
    public static function adminPostFilter($filters)
    {
        return dcCore::app()->behavior->callBehavior('adminPostFilter', dcCore::app(), $filters);
    }
    public static function adminPostListHeader($rs, $cols)
    {
        return dcCore::app()->behavior->callBehavior('adminPostListHeader', dcCore::app(), $rs, $cols);
    }
    public static function adminPostListValue($rs, $cols)
    {
        return dcCore::app()->behavior->callBehavior('adminPostListValue', dcCore::app(), $rs, $cols);
    }
    public static function adminPostMiniListHeader($rs, $cols)
    {
        return dcCore::app()->behavior->callBehavior('adminPostMiniListHeader', dcCore::app(), $rs, $cols);
    }
    public static function adminPostMiniListValue($rs, $cols)
    {
        return dcCore::app()->behavior->callBehavior('adminPostMiniListValue', dcCore::app(), $rs, $cols);
    }
    public static function adminPostsActions($getRS, $getAction, $getRedirection)
    {
        return dcCore::app()->behavior->callBehavior('adminPostsActions', dcCore::app(), $getRS, $getAction, $getRedirection);
    }
    public static function adminPostsActionsPage($that)
    {
        return dcCore::app()->behavior->callBehavior('adminPostsActionsPage', dcCore::app(), $that);
    }
    public static function adminPreferencesForm()
    {
        return dcCore::app()->behavior->callBehavior('adminPreferencesForm', dcCore::app());
    }
    public static function adminRteFlags($rte)
    {
        return dcCore::app()->behavior->callBehavior('adminRteFlags', dcCore::app(), $rte);
    }
    public static function adminSearchPageCombo($table)
    {
        return dcCore::app()->behavior->callBehavior('adminSearchPageCombo', dcCore::app(), $table);
    }
    public static function adminSearchPageDisplay($args)
    {
        return dcCore::app()->behavior->callBehavior('adminSearchPageDisplay', dcCore::app(), $args);
    }
    public static function adminSearchPageHead($args)
    {
        return dcCore::app()->behavior->callBehavior('adminSearchPageHead', dcCore::app(), $args);
    }
    public static function adminSearchPageProcess($args)
    {
        return dcCore::app()->behavior->callBehavior('adminSearchPageProcess', dcCore::app(), $args);
    }
    public static function adminUsersActions($users, $blogs, $action, $redir)
    {
        return dcCore::app()->behavior->callBehavior('adminUsersActions', dcCore::app(), $users, $blogs, $action, $redir);
    }
    public static function adminUsersActionsContent($action, $hidden_fields)
    {
        return dcCore::app()->behavior->callBehavior('adminUsersActionsContent', dcCore::app(), $action, $hidden_fields);
    }
    public static function adminUserFilter($filters)
    {
        return dcCore::app()->behavior->callBehavior('adminUserFilter', dcCore::app(), $filters);
    }
    public static function adminUserListHeader($rs, $cols)
    {
        return dcCore::app()->behavior->callBehavior('adminUserListHeader', dcCore::app(), $rs, $cols);
    }
    public static function adminUserListValue($rs, $cols)
    {
        return dcCore::app()->behavior->callBehavior('adminUserListValue', dcCore::app(), $rs, $cols);
    }

    public static function exportFull($exp)
    {
        return dcCore::app()->behavior->callBehavior('exportFull', dcCore::app(), $exp);
    }
    public static function exportSingle($exp, $blog_id)
    {
        return dcCore::app()->behavior->callBehavior('exportSingle', dcCore::app(), $exp, $blog_id);
    }

    public static function importExportModules($modules)
    {
        return dcCore::app()->behavior->callBehavior('importExportModules', $modules, dcCore::app());
    }
    public static function importFull($line, $that)
    {
        return dcCore::app()->behavior->callBehavior('importFull', $line, $that, dcCore::app());
    }
    public static function importInit($that)
    {
        return dcCore::app()->behavior->callBehavior('importInit', $that, dcCore::app());
    }
    public static function importPrepareDC12($line, $that)
    {
        return dcCore::app()->behavior->callBehavior('importPrepareDC12', $line, $that, dcCore::app());
    }
    public static function importSingle($line, $that)
    {
        return dcCore::app()->behavior->callBehavior('importSingle', $line, $that, dcCore::app());
    }

    public static function pluginsToolsHeaders($config = false)
    {
        return dcCore::app()->behavior->callBehavior('pluginsToolsHeaders', dcCore::app(), $config);
    }
    public static function pluginsToolsTabs()
    {
        return dcCore::app()->behavior->callBehavior('pluginsToolsTabs', dcCore::app());
    }
    public static function pluginBeforeDelete($define)
    {
        return dcCore::app()->behavior->callBehavior('pluginBeforeDelete', $define->dump());
    }
    public static function pluginAfterDelete($define)
    {
        return dcCore::app()->behavior->callBehavior('pluginAfterDelete', $define->dump());
    }
    public static function pluginBeforeAdd($define)
    {
        return dcCore::app()->behavior->callBehavior('pluginBeforeAdd', $define->dump());
    }
    public static function pluginAfterAdd($define)
    {
        return dcCore::app()->behavior->callBehavior('pluginAfterAdd', $define->dump());
    }
    public static function pluginBeforeDeactivate($define)
    {
        return dcCore::app()->behavior->callBehavior('pluginBeforeDeactivate', $define->dump());
    }
    public static function pluginAfterDeactivate($define)
    {
        return dcCore::app()->behavior->callBehavior('pluginAfterDeactivate', $define->dump());
    }
    public static function pluginBeforeUpdate($define)
    {
        return dcCore::app()->behavior->callBehavior('pluginBeforeUpdate', $define->dump());
    }
    public static function pluginAfterUpdate($define)
    {
        return dcCore::app()->behavior->callBehavior('pluginAfterUpdate', $define->dump());
    }

    public static function restCheckStoreUpdate($store, $mod, $url)
    {
        return dcCore::app()->behavior->callBehavior('restCheckStoreUpdate', dcCore::app(), $store, $mod, $url);
    }

    public static function themesToolsHeaders($config = false)
    {
        return dcCore::app()->behavior->callBehavior('themesToolsHeaders', dcCore::app(), $config);
    }
    public static function themesToolsTabs()
    {
        return dcCore::app()->behavior->callBehavior('themesToolsTabs', dcCore::app());
    }
    public static function themeBeforeDeactivate($define)
    {
        return dcCore::app()->behavior->callBehavior('themeBeforeDeactivate', $define->dump());
    }
    public static function themeAfterDeactivate($define)
    {
        return dcCore::app()->behavior->callBehavior('themeAfterDeactivate', $define->dump());
    }
    public static function themeBeforeDelete($define)
    {
        return dcCore::app()->behavior->callBehavior('themeBeforeDelete', $define->dump());
    }
    public static function themeAfterDelete($define)
    {
        return dcCore::app()->behavior->callBehavior('themeAfterDelete', $define->dump());
    }
    public static function themeBeforeAdd($define)
    {
        return dcCore::app()->behavior->callBehavior('themeBeforeAdd', $define->dump());
    }
    public static function themeAfterAdd($define)
    {
        return dcCore::app()->behavior->callBehavior('themeAfterAdd', $define->dump());
    }
    public static function themeBeforeUpdate($define)
    {
        return dcCore::app()->behavior->callBehavior('themeBeforeUpdate', $define->dump());
    }
    public static function themeAfterUpdate($define)
    {
        return dcCore::app()->behavior->callBehavior('themeAfterUpdate', $define->dump());
    }
}
