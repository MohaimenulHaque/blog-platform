<?php

namespace Tests\Unit;

use App\Services\HtmlSanitizer;
use PHPUnit\Framework\TestCase;

class HtmlSanitizerTest extends TestCase
{
    private HtmlSanitizer $sanitizer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sanitizer = new HtmlSanitizer;
    }

    public function test_it_strips_script_tags(): void
    {
        $this->assertSame(
            '<p>Hello world</p>alert("xss")',
            $this->sanitizer->sanitize('<p>Hello world</p><script>alert("xss")</script>')
        );
    }

    public function test_it_strips_inline_event_handlers(): void
    {
        $this->assertSame(
            '<a href="https://example.com">link</a>',
            $this->sanitizer->sanitize('<a href="https://example.com" onclick="alert(1)">link</a>')
        );
    }

    public function test_it_strips_javascript_urls(): void
    {
        $this->assertSame(
            '<a>link</a>',
            $this->sanitizer->sanitize('<a href="javascript:alert(1)">link</a>')
        );
    }

    public function test_it_allows_common_rich_text_tags(): void
    {
        $input = '<h2>Title</h2><p>Some <strong>bold</strong> and <em>italic</em> text.</p><ul><li>list item</li></ul>';

        $this->assertSame($input, $this->sanitizer->sanitize($input));
    }

    public function test_it_removes_unknown_tags_but_keeps_their_text(): void
    {
        $this->assertSame(
            'This is custom text',
            $this->sanitizer->sanitize('<custom-tag>This is custom text</custom-tag>')
        );
    }

    public function test_it_removes_unsafe_iframes(): void
    {
        $this->assertSame(
            '',
            $this->sanitizer->sanitize('<iframe src="https://evil.example.com"></iframe>')
        );
    }

    public function test_it_allows_youtube_iframes(): void
    {
        $output = $this->sanitizer->sanitize('<iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" allowfullscreen></iframe>');

        $this->assertStringContainsString('youtube.com', $output);
        $this->assertStringContainsString('<iframe', $output);
    }

    public function test_it_removes_style_attributes_with_unknown_properties(): void
    {
        $this->assertSame(
            '<p>Hello</p>',
            $this->sanitizer->sanitize('<p style="position:fixed;display:none">Hello</p>')
        );
    }
}
