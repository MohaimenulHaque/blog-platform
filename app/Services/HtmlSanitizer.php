<?php

namespace App\Services;

use DOMDocument;
use DOMElement;
use DOMNode;

class HtmlSanitizer
{
    /**
     * Tags that are allowed to pass through untouched.
     *
     * @var array<string, true>
     */
    protected array $allowedTags = [
        'p' => true,
        'h1' => true,
        'h2' => true,
        'h3' => true,
        'h4' => true,
        'h5' => true,
        'h6' => true,
        'strong' => true,
        'b' => true,
        'em' => true,
        'i' => true,
        'u' => true,
        's' => true,
        'a' => true,
        'img' => true,
        'video' => true,
        'source' => true,
        'iframe' => true,
        'ul' => true,
        'ol' => true,
        'li' => true,
        'blockquote' => true,
        'pre' => true,
        'code' => true,
        'table' => true,
        'thead' => true,
        'tbody' => true,
        'tfoot' => true,
        'tr' => true,
        'th' => true,
        'td' => true,
        'caption' => true,
        'figure' => true,
        'figcaption' => true,
        'hr' => true,
        'br' => true,
        'span' => true,
    ];

    /**
     * Attributes allowed per tag, keyed by tag name.
     *
     * @var array<string, array<string, true>>
     */
    protected array $allowedAttributes = [
        'a' => ['href' => true, 'target' => true, 'rel' => true, 'title' => true],
        'img' => ['src' => true, 'alt' => true, 'title' => true, 'width' => true, 'height' => true],
        'video' => ['src' => true, 'controls' => true, 'width' => true, 'height' => true, 'poster' => true],
        'source' => ['src' => true, 'type' => true],
        'iframe' => ['src' => true, 'width' => true, 'height' => true, 'title' => true, 'allow' => true, 'allowfullscreen' => true, 'frameborder' => true, 'loading' => true],
        'th' => ['colspan' => true, 'rowspan' => true, 'scope' => true],
        'td' => ['colspan' => true, 'rowspan' => true],
        'figure' => ['class' => true],
        'pre' => ['class' => true],
        'code' => ['class' => true],
    ];

    /**
     * CSS properties allowed inside style attributes.
     *
     * @var array<string, true>
     */
    protected array $allowedStyles = [
        'text-align' => true,
        'float' => true,
        'width' => true,
        'height' => true,
        'margin' => true,
        'padding' => true,
        'background-color' => true,
        'color' => true,
    ];

    /**
     * Hosts allowed for embedded iframes (video embeds).
     *
     * @var array<string, true>
     */
    protected array $allowedFrameHosts = [
        'youtube.com' => true,
        'youtube-nocookie.com' => true,
        'youtu.be' => true,
        'player.vimeo.com' => true,
        'vimeo.com' => true,
    ];

    /**
     * Sanitize untrusted HTML to a safe, well-formed subset.
     */
    public function sanitize(?string $html): ?string
    {
        if (blank($html)) {
            return null;
        }

        $dom = $this->load($html);

        if (! $dom) {
            return null;
        }

        $root = $dom->documentElement;

        if (! $root) {
            return null;
        }

        $this->walk($root);

        return $this->innerHtml($root);
    }

    /**
     * Recursively strip or scrub every node in the tree.
     */
    protected function walk(?DOMNode $node): void
    {
        if (! $node) {
            return;
        }

        $children = [];
        foreach ($node->childNodes as $child) {
            $children[] = $child;
        }

        foreach ($children as $child) {
            if ($child instanceof DOMElement) {
                $tag = strtolower($child->tagName);

                if (! isset($this->allowedTags[$tag])) {
                    $this->unwrap($child);

                    continue;
                }

                if ($tag === 'iframe' && ! $this->isAllowedFrameSource($child->getAttribute('src'))) {
                    $child->parentNode?->removeChild($child);

                    continue;
                }

                $this->scrubAttributes($child, $tag);
                $this->walk($child);
            }
        }
    }

    /**
     * Replace the given element with its children, dropping the element itself.
     */
    protected function unwrap(DOMElement $element): void
    {
        $parent = $element->parentNode;

        if (! $parent) {
            return;
        }

        while ($element->firstChild) {
            $parent->insertBefore($element->firstChild, $element);
        }

        $parent->removeChild($element);
    }

    /**
     * Remove unsafe attributes from an allowed element.
     */
    protected function scrubAttributes(DOMElement $element, string $tag): void
    {
        $attributes = [];

        foreach ($element->attributes as $attribute) {
            $attributes[] = $attribute->name;
        }

        $allowed = $this->allowedAttributes[$tag] ?? [];

        foreach ($attributes as $name) {
            $name = strtolower($name);

            if ($name === 'style') {
                $this->scrubStyle($element);

                continue;
            }

            if ($name === 'class' && ($tag === 'figure' || $tag === 'pre' || $tag === 'code')) {
                continue;
            }

            if ($tag === 'iframe' && $name === 'src') {
                if (! $this->isAllowedFrameSource($element->getAttribute('src'))) {
                    $element->removeAttribute($name);
                }

                continue;
            }

            if ($name === 'src' && ($tag === 'img' || $tag === 'source' || $tag === 'video')) {
                if (! $this->isSafeUrl($element->getAttribute('src'))) {
                    $element->removeAttribute($name);
                }

                continue;
            }

            if ($name === 'href' && $tag === 'a') {
                if (! $this->isSafeUrl($element->getAttribute('href'))) {
                    $element->removeAttribute($name);
                }

                continue;
            }

            if (str_starts_with($name, 'on')) {
                $element->removeAttribute($name);

                continue;
            }

            if (! isset($allowed[$name])) {
                $element->removeAttribute($name);
            }
        }
    }

    /**
     * Keep only a safe subset of CSS declarations inside the style attribute.
     */
    protected function scrubStyle(DOMElement $element): void
    {
        $kept = [];

        foreach (explode(';', $element->getAttribute('style')) as $declaration) {
            $parts = array_map('trim', explode(':', $declaration, 2));

            if (count($parts) !== 2) {
                continue;
            }

            [$property, $value] = $parts;

            $property = strtolower($property);
            $value = strtolower($value);

            if (! isset($this->allowedStyles[$property])) {
                continue;
            }

            if (! preg_match('/^[a-z0-9#%.,\s-]+$/', $value)) {
                continue;
            }

            $kept[] = $property.': '.$value;
        }

        if ($kept) {
            $element->setAttribute('style', implode('; ', $kept));
        } else {
            $element->removeAttribute('style');
        }
    }

    /**
     * Determine whether a URL is safe to keep.
     */
    protected function isSafeUrl(string $url): bool
    {
        $url = trim($url);

        if ($url === '' || str_starts_with($url, '#')) {
            return true;
        }

        if (str_starts_with($url, 'mailto:') || str_starts_with($url, 'tel:')) {
            return true;
        }

        if (preg_match('#^https?://#i', $url) === 1) {
            return true;
        }

        if (str_starts_with($url, '/') && ! str_starts_with($url, '//')) {
            return true;
        }

        if (preg_match('#^//#', $url) === 1) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether an iframe source points to an allowed embed host.
     */
    protected function isAllowedFrameSource(string $url): bool
    {
        if ($url === '' || preg_match('#^https?://#i', $url) !== 1) {
            return false;
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        foreach (array_keys($this->allowedFrameHosts) as $allowed) {
            if ($host === $allowed || str_ends_with($host, '.'.$allowed)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Load the HTML into a DOMDocument without emitting warnings.
     */
    protected function load(string $html): ?DOMDocument
    {
        $dom = new DOMDocument('1.0', 'UTF-8');

        libxml_use_internal_errors(true);

        $dom->loadHTML(
            '<?xml encoding="utf-8" ?><div id="__sanitizer_root">'.$html.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOWARNING
        );

        libxml_clear_errors();

        return $dom;
    }

    /**
     * Extract the inner HTML of the given root element.
     */
    protected function innerHtml(DOMNode $root): string
    {
        $inner = '';

        foreach ($root->childNodes as $child) {
            $inner .= $root->ownerDocument->saveHTML($child);
        }

        return $inner;
    }
}
