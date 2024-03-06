<?php
/**
 * @package     Dotclear
 *
 * @copyright   Olivier Meunier & Association Dotclear
 * @copyright   GPL-2.0-only
 */
declare(strict_types=1);

namespace Dotclear\Core\Upgrade\GrowUp;

use Dotclear\Core\Upgrade\Upgrade;

/**
 * @brief   Upgrade step.
 */
class GrowUp_2_30_lt
{
    public static function init(bool $cleanup_sessions): bool
    {
        // A bit of housecleaning for no longer needed folders
        Upgrade::houseCleaning(
            // Files
            [
                'admin/images/admin.png',
                'admin/images/attach.png',
                'admin/images/check-on.png',
                'admin/images/check-off.png',
                'admin/images/check-wrn.png',
                'admin/images/comments.png',
                'admin/images/edit-mini.png',
                'admin/images/hidden.png',
                'admin/images/junk.png',
                'admin/images/locker.png',
                'admin/images/plus.png',
                'admin/images/scheduled.png',
                'admin/images/selected.png',
                'admin/images/superadmin.png',
                'admin/images/trackbacks.png',
                'admin/style/cancel.png',
                'admin/style/drag.png',
                'admin/style/search.png',
                'admin/style/settings.png',
                'admin/style/trash.png',
                'admin/style/user.png',
            ],
            // Folders
            [
            ]
        );

        return $cleanup_sessions;
    }
}
