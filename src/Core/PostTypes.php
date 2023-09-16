<?php
/**
 * @package Dotclear
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
declare(strict_types=1);

namespace Dotclear\Core;

use Dotclear\Interface\Core\PostTypeInterface;
use Dotclear\Interface\Core\PostTypesInterface;

/**
 * @brief   Posts types handler.
 */
class PostTypes implements PostTypesInterface
{
    /**
     * The post types stack.
     *
     * @var     array<string,PostTypeInterface>     $stack
     */
    private array $stack;

    public function exists(string $type): bool
    {
        return isset($this->stack[$type]);
    }

    public function __get(string $type): PostTypeInterface
    {
        return $this->get($type);
    }

    public function get(string $type): PostTypeInterface
    {
        if (!empty($type) && !isset($this->stack[$type])) {
            $type = 'post';
        }

        return $this->stack[$type] ?? new PostType('', '', '', 'undefined');
    }

    public function set(PostTypeInterface $descriptor): PostTypesInterface
    {
        if ('' !== $descriptor->get('type')) {
            $this->stack[$descriptor->get('type')] = $descriptor;
        }

        return $this;
    }

    public function dump(): array
    {
        return $this->stack;
    }

    public function getPostAdminURL(string $type, int|string $post_id, bool $escaped = true, array $params = []): string
    {
        return $this->get($type)->adminUrl($post_id, $escaped, $params);
    }

    public function getPostPublicURL(string $type, string $post_url, bool $escaped = true): string
    {
        return $this->get($type)->publicUrl($post_url, $escaped);
    }

    public function setPostType(string $type, string $admin_url, string $public_url, string $label = ''): void
    {
        $this->set(new PostType(
            type:       $type,
            admin_url:  $admin_url,
            public_url: $public_url,
            label:      $label,
        ));
    }

    public function getPostTypes(): array
    {
        $res = [];

        foreach ($this->stack as $desc) {
            $res[$desc->get('type')] = $desc->dump();
        }

        return $res;
    }
}
