<?php
/**
 * @package     Dotclear
 *
 * @copyright   Olivier Meunier & Association Dotclear
 * @copyright   GPL-2.0-only
 */
declare(strict_types=1);

namespace Dotclear\Exception;

/**
 * @brief   Post form precondition Exception.
 *
 * @since   2.28
 */
class PreconditionException extends GenericClientException
{
    public const CODE  = 412;
    public const LABEL = 'Precondition Failed';
}
