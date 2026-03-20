<?php

declare (strict_types=1);
namespace Stevebauman\Purify\Definitions;

use Html_Purifier_html_Definition;
class Html5Definition implements Definition
{
    /**
     * Apply rules to the HTML Purifier definition.
     *
     *
     */
    public static function apply(Html_Purifier_html_Definition $definition): void
    {
        // http://developers.whatwg.org/sections.html
        $definition->add_element('section', 'Block', 'Flow', 'Common');
        $definition->add_element('nav', 'Block', 'Flow', 'Common');
        $definition->add_element('article', 'Block', 'Flow', 'Common');
        $definition->add_element('aside', 'Block', 'Flow', 'Common');
        $definition->add_element('header', 'Block', 'Flow', 'Common');
        $definition->add_element('footer', 'Block', 'Flow', 'Common');
        $definition->add_element('address', 'Block', 'Flow', 'Common');
        $definition->add_element('hgroup', 'Block', 'Required: h1 | h2 | h3 | h4 | h5 | h6', 'Common');
        // http://developers.whatwg.org/grouping-content.html
        $definition->add_element('figure', 'Block', 'Optional: (figcaption, Flow) | (Flow, figcaption) | Flow', 'Common');
        $definition->add_element('figcaption', 'Inline', 'Flow', 'Common');
        // http://developers.whatwg.org/the-video-element.html#the-video-element
        $definition->add_element('video', 'Block', 'Optional: (source, Flow) | (Flow, source) | Flow', 'Common', ['src' => 'URI', 'type' => 'Text', 'width' => 'Length', 'height' => 'Length', 'poster' => 'URI', 'preload' => 'Enum#auto,metadata,none', 'controls' => 'Bool']);
        $definition->add_element('source', 'Block', 'Flow', 'Common', ['src' => 'URI', 'type' => 'Text']);
        // http://developers.whatwg.org/interactive-elements.html
        $definition->add_element('details', 'Block', 'Flow', 'Common');
        $definition->add_element('summary', 'Inline', 'Flow', 'Common', ['open' => 'Bool']);
        // http://developers.whatwg.org/text-level-semantics.html
        $definition->add_element('u', 'Inline', 'Inline', 'Common');
        $definition->add_element('s', 'Inline', 'Inline', 'Common');
        $definition->add_element('var', 'Inline', 'Inline', 'Common');
        $definition->add_element('sub', 'Inline', 'Inline', 'Common');
        $definition->add_element('sup', 'Inline', 'Inline', 'Common');
        $definition->add_element('mark', 'Inline', 'Inline', 'Common');
        $definition->add_element('wbr', 'Inline', 'Empty', 'Core');
        // http://developers.whatwg.org/edits.html
        $definition->add_element('ins', 'Block', 'Flow', 'Common', ['cite' => 'URI', 'datetime' => 'CDATA']);
        $definition->add_element('del', 'Block', 'Flow', 'Common', ['cite' => 'URI', 'datetime' => 'CDATA']);
        $definition->add_attribute('table', 'height', 'Text');
        $definition->add_attribute('td', 'border', 'Text');
        $definition->add_attribute('th', 'border', 'Text');
        $definition->add_attribute('tr', 'width', 'Text');
        $definition->add_attribute('tr', 'height', 'Text');
        $definition->add_attribute('tr', 'border', 'Text');
    }
}