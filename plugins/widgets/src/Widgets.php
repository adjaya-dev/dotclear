<?php
/**
 * @package     Dotclear
 *
 * @copyright   Olivier Meunier & Association Dotclear
 * @copyright   GPL-2.0-only
 */
declare(strict_types=1);

namespace Dotclear\Plugin\widgets;

use dcCore;
use Dotclear\App;
use Dotclear\Database\MetaRecord;
use Dotclear\Helper\Html\Html;
use Dotclear\Helper\L10n;
use Dotclear\Helper\Network\Feed\Reader;
use Exception;

/**
 * @brief   The widgets handler.
 * @ingroup widgets
 */
class Widgets
{
    /**
     * Navigation widgets group.
     *
     * @var     string  WIDGETS_NAV
     */
    public const WIDGETS_NAV = 'nav';

    /**
     * extra widgets group.
     *
     * @var     string  WIDGETS_EXTRA
     */
    public const WIDGETS_EXTRA = 'extra';

    /**
     * custom widgets group.
     *
     * @var     string  WIDGETS_CUSTOM
     */
    public const WIDGETS_CUSTOM = 'custom';

    /**
     * The current widgets stack.
     *
     * @var     WidgetsStack    $widgets
     */
    public static WidgetsStack $widgets;

    /**
     * The default widgets stack.
     *
     * @var     array<string, WidgetsStack>  $default_widgets
     */
    public static array $default_widgets;

    /**
     * Initializes the default widgets.
     */
    public static function init(): void
    {
        global $__widgets;

        // Available widgets
        self::$widgets = new WidgetsStack();

        // deprecated since 2.28, use Widgets::$widgets instead
        dcCore::app()->widgets = self::$widgets;    // @phpstan-ignore-line

        // deprecated since 2.23, use Widgets::$widgets instead
        $__widgets = self::$widgets;

        self::$widgets
            ->create('search', __('Search engine'), Widgets::search(...), null, 'Search engine form')
            ->addTitle(__('Search'))
            ->setting('placeholder', __('Placeholder (HTML5 only, optional):'), '')
            ->addHomeOnly()
            ->addContentOnly()
            ->addClass()
            ->addOffline();

        self::$widgets
            ->create('navigation', __('Navigation links'), Widgets::navigation(...), null, 'List of navigation links')
            ->addTitle()
            ->addHomeOnly()
            ->addContentOnly()
            ->addClass()
            ->addOffline();

        self::$widgets
            ->create('bestof', __('Selected entries'), Widgets::bestof(...), null, 'List of selected entries')
            ->addTitle(__('Best of me'))
            ->setting('orderby', __('Sort:'), 'asc', 'combo', [__('Ascending') => 'asc', __('Descending') => 'desc'])
            ->addHomeOnly()
            ->addContentOnly()
            ->addClass()
            ->addOffline();

        self::$widgets
            ->create('langs', __('Blog languages'), Widgets::langs(...), null, 'List of available languages')
            ->addTitle(__('Languages'))
            ->addHomeOnly()
            ->addContentOnly()
            ->addClass()
            ->addOffline();

        self::$widgets
            ->create('categories', __('List of categories'), Widgets::categories(...), null, 'List of categories')
            ->addTitle(__('Categories'))
            ->setting('postcount', __('With entries counts'), 0, 'check')
            ->setting('subcatscount', __('Include sub cats in count'), false, 'check')
            ->setting('with_empty', __('Include empty categories'), 0, 'check')
            ->addHomeOnly()
            ->addContentOnly()
            ->addClass()
            ->addOffline();

        self::$widgets
            ->create('subscribe', __('Subscribe links'), Widgets::subscribe(...), null, 'Feed subscription links (RSS or Atom)')
            ->addTitle(__('Subscribe'))
            ->setting('type', __('Feeds type:'), 'atom', 'combo', ['Atom' => 'atom', 'RSS' => 'rss2'])
            ->addHomeOnly()
            ->addContentOnly()
            ->addClass()
            ->addOffline();

        self::$widgets->
            create('feed', __('Feed reader'), Widgets::feed(...), null, 'List of last entries from feed (RSS or Atom)')
            ->addTitle(__('Somewhere else'))
            ->setting('url', __('Feed URL:'), '')
            ->setting('limit', __('Entries limit:'), 10)
            ->addHomeOnly()
            ->addContentOnly()
            ->addClass()
            ->addOffline();

        self::$widgets
            ->create('text', __('Text'), Widgets::text(...), null, 'Simple text')
            ->addTitle()
            ->setting('text', __('Text:'), '', 'textarea')
            ->addHomeOnly()
            ->addContentOnly()
            ->addClass()
            ->addOffline();

        $rs         = App::blog()->getCategories(['post_type' => 'post']);
        $categories = ['' => '', __('Uncategorized') => 'null'];
        while ($rs->fetch()) {
            $categories[str_repeat('&nbsp;&nbsp;', $rs->level - 1) . ($rs->level - 1 == 0 ? '' : '&bull; ') . Html::escapeHTML($rs->cat_title)] = $rs->cat_id;
        }
        $w = self::$widgets->create('lastposts', __('Last entries'), Widgets::lastposts(...), null, 'List of last entries published');
        $w
            ->addTitle(__('Last entries'))
            ->setting('category', __('Category:'), '', 'combo', $categories);
        if (App::plugins()->moduleExists('tags')) {
            $w->setting('tag', __('Tag:'), '');
        }
        $w
            ->setting('limit', __('Entries limit:'), 10)
            ->addHomeOnly()
            ->addContentOnly()
            ->addClass()
            ->addOffline();
        unset($rs, $categories, $w);

        self::$widgets
            ->create('lastcomments', __('Last comments'), Widgets::lastcomments(...), null, 'List of last comments published')
            ->addTitle(__('Last comments'))
            ->setting('limit', __('Comments limit:'), 10)
            ->addHomeOnly()
            ->addContentOnly()
            ->addClass()
            ->addOffline();

        # --BEHAVIOR-- initWidgets -- WidgetsStack
        App::behavior()->callBehavior('initWidgets', self::$widgets);

        # Default widgets
        self::$default_widgets = [
            Widgets::WIDGETS_NAV    => new WidgetsStack(),
            Widgets::WIDGETS_EXTRA  => new WidgetsStack(),
            Widgets::WIDGETS_CUSTOM => new WidgetsStack(),
        ];

        self::$default_widgets[Widgets::WIDGETS_NAV]->append(self::$widgets->search);
        self::$default_widgets[Widgets::WIDGETS_NAV]->append(self::$widgets->bestof);
        self::$default_widgets[Widgets::WIDGETS_NAV]->append(self::$widgets->categories);
        self::$default_widgets[Widgets::WIDGETS_CUSTOM]->append(self::$widgets->subscribe);

        # --BEHAVIOR-- initDefaultWidgets -- WidgetsStack, array<string,WidgetsStack>
        App::behavior()->callBehavior('initDefaultWidgets', self::$widgets, self::$default_widgets);
    }

    /**
     * Render search form widget.
     *
     * @param   WidgetsElement  $widget     The widget
     *
     * @return  string
     */
    public static function search(WidgetsElement $widget): string
    {
        if (App::blog()->settings()->system->no_search) {
            return '';
        }

        if ($widget->offline) {
            return '';
        }

        if (!$widget->checkHomeOnly(App::url()->type)) {
            return '';
        }

        $value = App::frontend()->search ?? '';

        return $widget->renderDiv(
            (bool) $widget->content_only,
            $widget->class,
            'id="search"',
            ($widget->title ? $widget->renderTitle('<label for="q">' . Html::escapeHTML($widget->title) . '</label>') : '') .
            '<form action="' . App::blog()->url() . '" method="get" role="search">' .
            '<p><input type="text" size="10" maxlength="255" id="q" name="q" value="' . $value . '" ' .
            ($widget->placeholder ? 'placeholder="' . Html::escapeHTML($widget->placeholder) . '"' : '') .
            ' aria-label="' . __('Search') . '"/> ' .
            '<input type="submit" class="submit" value="ok" title="' . __('Search') . '" /></p>' .
            '</form>'
        );
    }

    /**
     * Render navigation widget.
     *
     * @param   WidgetsElement  $widget     The widget
     *
     * @return  string
     */
    public static function navigation(WidgetsElement $widget): string
    {
        if ($widget->offline) {
            return '';
        }

        if (!$widget->checkHomeOnly(App::url()->type)) {
            return '';
        }

        $res = ($widget->title ? $widget->renderTitle(Html::escapeHTML($widget->title)) : '') .
            '<nav role="navigation"><ul>';

        if (!App::url()->isHome(App::url()->type)) {
            // Not on home page (standard or static), add home link
            $res .= '<li class="topnav-home">' .
            '<a href="' . App::blog()->url() . '">' . __('Home') . '</a></li>';
            if (App::blog()->settings()->system->static_home) {
                // Static mode: add recent posts link
                $res .= '<li class="topnav-posts">' .
                '<a href="' . App::blog()->url() . App::url()->getURLFor('posts') . '">' . __('Recent posts') . '</a></li>';
            }
        } else {
            // On home page (standard or static)
            if (App::blog()->settings()->system->static_home) {
                // Static mode: add recent posts link
                $res .= '<li class="topnav-posts">' .
                '<a href="' . App::blog()->url() . App::url()->getURLFor('posts') . '">' . __('Recent posts') . '</a></li>';
            }
        }

        $res .= '<li class="topnav-arch">' .
        '<a href="' . App::blog()->url() . App::url()->getURLFor('archive') . '">' .
        __('Archives') . '</a></li>' .
            '</ul></nav>';

        return $widget->renderDiv((bool) $widget->content_only, $widget->class, 'id="topnav"', $res);
    }

    /**
     * Render categories widget.
     *
     * @param   WidgetsElement  $widget     The widget
     *
     * @return  string
     */
    public static function categories(WidgetsElement $widget): string
    {
        if ($widget->offline) {
            return '';
        }

        if (!$widget->checkHomeOnly(App::url()->type)) {
            return '';
        }

        $rs = App::blog()->getCategories(['post_type' => 'post', 'without_empty' => !$widget->with_empty]);
        if ($rs->isEmpty()) {
            return '';
        }

        $res = ($widget->title ? $widget->renderTitle(Html::escapeHTML($widget->title)) : '');

        $ref_level = $level = $rs->level - 1;
        while ($rs->fetch()) {
            $class = '';
            if ((App::url()->type == 'category' && App::frontend()->ctx->categories instanceof MetaRecord && App::frontend()->ctx->categories->cat_id == $rs->cat_id)
                || (App::url()->type == 'post' && App::frontend()->ctx->posts instanceof MetaRecord && App::frontend()->ctx->posts->cat_id == $rs->cat_id)) {
                $class = ' class="category-current"';
            }

            if ($rs->level > $level) {
                $res .= str_repeat('<ul><li' . $class . '>', $rs->level - $level);
            } elseif ($rs->level < $level) {
                $res .= str_repeat('</li></ul>', -($rs->level - $level));
            }

            if ($rs->level <= $level) {
                $res .= '</li><li' . $class . '>';
            }

            $res .= '<a href="' . App::blog()->url() . App::url()->getURLFor('category', $rs->cat_url) . '">' .
            Html::escapeHTML($rs->cat_title) . '</a>' .
                ($widget->postcount ? ' <span>(' . ($widget->subcatscount ? $rs->nb_total : $rs->nb_post) . ')</span>' : '');

            $level = $rs->level;
        }

        if ($ref_level - $level < 0) {
            $res .= str_repeat('</li></ul>', -($ref_level - $level));
        }

        return $widget->renderDiv((bool) $widget->content_only, 'categories ' . $widget->class, '', $res);
    }

    /**
     * Render selected posts widget.
     *
     * @param   WidgetsElement  $widget     The widget
     *
     * @return  string
     */
    public static function bestof(WidgetsElement $widget): string
    {
        if ($widget->offline) {
            return '';
        }

        if (!$widget->checkHomeOnly(App::url()->type)) {
            return '';
        }

        $params = [
            'post_selected' => true,
            'no_content'    => true,
            'order'         => 'post_dt ' . strtoupper($widget->orderby),
        ];

        $rs = App::blog()->getPosts($params);

        if ($rs->isEmpty()) {
            return '';
        }

        $res = ($widget->title ? $widget->renderTitle(Html::escapeHTML($widget->title)) : '') .
            '<ul>';

        while ($rs->fetch()) {
            $class = '';
            if (App::url()->type == 'post' && App::frontend()->ctx->posts instanceof MetaRecord && App::frontend()->ctx->posts->post_id == $rs->post_id) {
                $class = ' class="post-current"';
            }
            $res .= ' <li' . $class . '><a href="' . $rs->getURL() . '">' . Html::escapeHTML($rs->post_title) . '</a></li> ';
        }

        $res .= '</ul>';

        return $widget->renderDiv((bool) $widget->content_only, 'selected ' . $widget->class, '', $res);
    }

    /**
     * Render langs widget.
     *
     * @param   WidgetsElement  $widget     The widget
     *
     * @return  string
     */
    public static function langs(WidgetsElement $widget): string
    {
        if ($widget->offline) {
            return '';
        }

        if (!$widget->checkHomeOnly(App::url()->type)) {
            return '';
        }

        $rs = App::blog()->getLangs();

        if ($rs->count() <= 1) {
            return '';
        }

        $langs = L10n::getISOcodes();
        $res   = ($widget->title ? $widget->renderTitle(Html::escapeHTML($widget->title)) : '') .
            '<ul>';

        while ($rs->fetch()) {
            $l = (App::frontend()->ctx->cur_lang == $rs->post_lang) ? '<strong>%s</strong>' : '%s';

            $lang_name = $langs[$rs->post_lang] ?? $rs->post_lang;

            $res .= ' <li>' .
            sprintf(
                $l,
                '<a href="' . App::blog()->url() . App::url()->getURLFor('lang', $rs->post_lang) . '" ' .
                'class="lang-' . $rs->post_lang . '">' .
                $lang_name . '</a>'
            ) .
                ' </li>';
        }

        $res .= '</ul>';

        return $widget->renderDiv((bool) $widget->content_only, 'langs ' . $widget->class, '', $res);
    }

    /**
     * Render feed subscription widget.
     *
     * @param   WidgetsElement  $widget     The widget
     *
     * @return  string
     */
    public static function subscribe(WidgetsElement $widget): string
    {
        if ($widget->offline) {
            return '';
        }

        if (!$widget->checkHomeOnly(App::url()->type)) {
            return '';
        }

        $type = ($widget->type == 'atom' || $widget->type == 'rss2') ? $widget->type : 'rss2';
        $mime = $type == 'rss2' ? 'application/rss+xml' : 'application/atom+xml';
        if (App::frontend()->ctx->exists('cur_lang')) {
            $type = App::frontend()->ctx->cur_lang . '/' . $type;
        }

        $p_title = __('This blog\'s entries %s feed');
        $c_title = __('This blog\'s comments %s feed');

        $res = ($widget->title ? $widget->renderTitle(Html::escapeHTML($widget->title)) : '') .
            '<ul>';

        $res .= '<li><a type="' . $mime . '" ' .
        'href="' . App::blog()->url() . App::url()->getURLFor('feed', $type) . '" ' .
        'title="' . sprintf($p_title, ($type == 'atom' ? 'Atom' : 'RSS')) . '" class="feed">' .
        __('Entries feed') . '</a></li>';

        if (App::blog()->settings()->system->allow_comments || App::blog()->settings()->system->allow_trackbacks) {
            $res .= '<li><a type="' . $mime . '" ' .
            'href="' . App::blog()->url() . App::url()->getURLFor('feed', $type . '/comments') . '" ' .
            'title="' . sprintf($c_title, ($type == 'atom' ? 'Atom' : 'RSS')) . '" class="feed">' .
            __('Comments feed') . '</a></li>';
        }

        $res .= '</ul>';

        return $widget->renderDiv((bool) $widget->content_only, 'syndicate ' . $widget->class, '', $res);
    }

    /**
     * Render feed widget.
     *
     * @param   WidgetsElement  $widget     The widget
     *
     * @return  string
     */
    public static function feed(WidgetsElement $widget): string
    {
        if (!$widget->url) {
            return '';
        }

        if ($widget->offline) {
            return '';
        }

        if (!$widget->checkHomeOnly(App::url()->type)) {
            return '';
        }

        $limit = abs((int) $widget->limit);

        try {
            $feed = Reader::quickParse($widget->url, App::config()->cacheRoot());
            if (!$feed || !(is_countable($feed->items) ? count($feed->items) : 0)) {    // @phpstan-ignore-line
                return '';
            }
        } catch (Exception) {
            return '';
        }

        $res = ($widget->title ? $widget->renderTitle(Html::escapeHTML($widget->title)) : '') .
            '<ul>';

        $i = 0;
        foreach ($feed->items as $item) {
            $title = isset($item->title) && strlen(trim((string) $item->title)) ? $item->title : '';
            $link  = isset($item->link)  && strlen(trim((string) $item->link)) ? $item->link : '';

            if (!$link && !$title) {
                continue;
            }

            if (!$title) {
                $title = substr($link, 0, 25) . '...';
            }

            $li = $link ? '<a href="' . Html::escapeHTML($item->link) . '">' . $title . '</a>' : $title;
            $res .= ' <li>' . $li . '</li> ';
            $i++;
            if ($i >= $limit) {
                break;
            }
        }

        $res .= '</ul>';

        return $widget->renderDiv((bool) $widget->content_only, 'feed ' . $widget->class, '', $res);
    }

    /**
     * Render text widget.
     *
     * @param   WidgetsElement  $widget     The widget
     *
     * @return  string
     */
    public static function text(WidgetsElement $widget): string
    {
        if ($widget->offline) {
            return '';
        }

        if (!$widget->checkHomeOnly(App::url()->type)) {
            return '';
        }

        $res = ($widget->title ? $widget->renderTitle(Html::escapeHTML($widget->title)) : '') . $widget->text;

        return $widget->renderDiv((bool) $widget->content_only, 'text ' . $widget->class, '', $res);
    }

    /**
     * Render last posts widget.
     *
     * @param   WidgetsElement  $widget     The widget
     *
     * @return  string
     */
    public static function lastposts(WidgetsElement $widget): string
    {
        $params = [];
        if ($widget->offline) {
            return '';
        }

        if (!$widget->checkHomeOnly(App::url()->type)) {
            return '';
        }

        $params['limit']      = abs((int) $widget->limit);
        $params['order']      = 'post_dt desc';
        $params['no_content'] = true;

        if ($widget->category) {
            if ($widget->category == 'null') {
                $params['sql'] = ' AND P.cat_id IS NULL ';
            } elseif (is_numeric($widget->category)) {
                $params['cat_id'] = (int) $widget->category;
            } else {
                $params['cat_url'] = $widget->category;
            }
        }

        if ($widget->tag) {
            $params['meta_id'] = $widget->tag;
            $rs                = App::meta()->getPostsByMeta($params);
        } else {
            $rs = App::blog()->getPosts($params);
        }

        if ($rs->isEmpty()) {
            return '';
        }

        $res = ($widget->title ? $widget->renderTitle(Html::escapeHTML($widget->title)) : '') .
            '<ul>';

        while ($rs->fetch()) {
            $class = '';
            if (App::url()->type == 'post' && App::frontend()->ctx->posts instanceof MetaRecord && App::frontend()->ctx->posts->post_id == $rs->post_id) {
                $class = ' class="post-current"';
            }
            $res .= '<li' . $class . '><a href="' . $rs->getURL() . '">' .
            Html::escapeHTML($rs->post_title) . '</a></li>';
        }

        $res .= '</ul>';

        return $widget->renderDiv((bool) $widget->content_only, 'lastposts ' . $widget->class, '', $res);
    }

    /**
     * Render last comments widget.
     *
     * @param   WidgetsElement  $widget     The widget
     *
     * @return  string
     */
    public static function lastcomments(WidgetsElement $widget): string
    {
        $params = [];
        if ($widget->offline) {
            return '';
        }

        if (!$widget->checkHomeOnly(App::url()->type)) {
            return '';
        }

        $params['limit'] = abs((int) $widget->limit);
        $params['order'] = 'comment_dt desc';
        $rs              = App::blog()->getComments($params);

        if ($rs->isEmpty()) {
            return '';
        }

        $res = ($widget->title ? $widget->renderTitle(Html::escapeHTML($widget->title)) : '') . '<ul>';

        while ($rs->fetch()) {
            $res .= '<li class="' .
            ((bool) $rs->comment_trackback ? 'last-tb' : 'last-comment') .
            '"><a href="' . $rs->getPostURL() . '#c' . $rs->comment_id . '">' .
            Html::escapeHTML($rs->post_title) . ' - ' .
            Html::escapeHTML($rs->comment_author) .
                '</a></li>';
        }

        $res .= '</ul>';

        return $widget->renderDiv((bool) $widget->content_only, 'lastcomments ' . $widget->class, '', $res);
    }
}
