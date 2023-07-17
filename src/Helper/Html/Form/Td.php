<?php
/**
 * @class Td
 * @brief HTML Forms Td creation helpers
 *
 * @package Dotclear
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
declare(strict_types=1);

namespace Dotclear\Helper\Html\Form;

class Td extends Component
{
    public const DEFAULT_ELEMENT = 'td';

    /**
     * Constructs a new instance.
     *
     * @param      mixed   $id       The identifier
     * @param      string  $element  The element
     */
    public function __construct($id = null, ?string $element = null)
    {
        parent::__construct(self::class, $element ?? self::DEFAULT_ELEMENT);
        if ($id !== null) {
            $this->setIdentifier($id);
        }
    }

    /**
     * Renders the HTML component.
     *
     * @return     string
     */
    public function render(): string
    {
        $buffer = '<' . ($this->getElement() ?? self::DEFAULT_ELEMENT) .
            (isset($this->colspan) ? ' colspan=' . strval((int) $this->colspan) : '') .
            (isset($this->rowspan) ? ' rowspan=' . strval((int) $this->rowspan) : '') .
            (isset($this->headers) ? ' headers="' . $this->headers . '"' : '') .
            $this->renderCommonAttributes() . '>';
        if ($this->text) {
            $buffer .= $this->text;
        }
        if (isset($this->items) && is_array($this->items)) {
            foreach ($this->items as $item) {
                $buffer .= $item->render() . "\n";
            }
        }
        $buffer .= '</' . ($this->getElement() ?? self::DEFAULT_ELEMENT) . '>';

        return $buffer;
    }

    /**
     * Gets the default element.
     *
     * @return     string  The default element.
     */
    public function getDefaultElement(): string
    {
        return self::DEFAULT_ELEMENT;
    }
}
