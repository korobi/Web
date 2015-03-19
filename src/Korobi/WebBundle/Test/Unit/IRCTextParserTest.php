<?php

namespace Korobi\WebBundle\Test\Unit;

use Korobi\WebBundle\Parser\IRCTextParser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HtmlFacility {

    private $dom;

    public function __construct($html) {
        // Add a div so that everything is contained (validity check)
        // and another to be able to loop through the outer part of the dom
        $this->dom = @simplexml_load_string('<div><div>' . $html . '</div></div>');
    }

    public function isValid() {
        return $this->dom !== false;
    }

    /**
     * Parses the dom to look for a specific element.
     * It is recommended you only look for single chars as
     *
     * @param $textFragment
     * @return mixed
     */
    public function getStyle($textFragment) {
        $structure = $this->parse($this->dom);
        list($found, $styles) = $this->_getStyle($textFragment, $structure, [
            'fg' => IRCTextParser::DEFAULT_FOREGROUND,
            'bg' => IRCTextParser::DEFAULT_BACKGROUND,
            'bold' => false,
            'italic' => false,
            'underline' => false
        ]);
        if (!$found) {
            // Fail test?
        }
        return $styles;
    }

    private function parse(\SimpleXMLElement $e) {
        $c = [];
        foreach ($e as $tag => $content) {
            $c[] = [
                'content' => $content[0]->__toString(),
                'class' => $content->attributes()->__toString(),
                'tag' => $tag,
                'child' => $this->parse($content)
            ];
        }
        return $c;
    }

    private function _getStyle($textFragment, $structure, $styles) {
        foreach ($structure as $value) {
            $prev_styles = $styles;
            if($value['tag'] == 'span') {
                // Check for styles
                switch ($value['class']) {
                    case 'bold':
                    case 'italic':
                    case 'underline':
                        $styles[$value['class']] = true;
                        break;
                    default:
                        $colour_info = explode('-', $value['class']);
                        $styles['fg'] = $colour_info[2];
                        $styles['bg'] = $colour_info[3];
                }
            }
            if (strpos($value['content'], $textFragment) !== false) {
                return [true, $styles];
            }
            // Go look in children if there is some
            if(!empty($value['child'])) {
                list($found, $styles) = $this->_getStyle($textFragment, $value['child'], $styles);
                if($found) {
                    // If the fragment was found further down, return it
                    // (if not, don't take $new_styles into account)
                    return [$found, $styles];
                }
            }
            $styles = $prev_styles; // The fragment was not found in this iteration, resetting
        }
        return [false, $styles]; // not found, styles
    }
}

class IRCTextParserTest extends WebTestCase {

    /************************************
     * Message tests
     */

    public function testStrippedMessage() {
        $message = "I'm a test";
        $this->assertEquals(
            $message,
            IRCTextParser::parse($message)
        );
    }

    public function testMessageWithTags() {
        $parsed_message = IRCTextParser::parse("<script>alert('woo');</script>");
        $this->assertNotContains($parsed_message, '<');
        $this->assertNotContains($parsed_message, '>');
    }

    public function testMessageWithFgAndBg() {
        $hf = new HtmlFacility(IRCTextParser::parse("I'm a \x031,2test"));

        $this->assertTrue($hf->isValid());

        $test_style = $hf->getStyle('test');
        $this->assertEquals(1, $test_style['fg']);
        $this->assertEquals(2, $test_style['bg']);
        $this->assertFalse($test_style['bold']);
        $this->assertFalse($test_style['underline']);
        $this->assertFalse($test_style['italic']);

        $im_a_style = $hf->getStyle("I'm a ");
        $this->assertEquals(IRCTextParser::DEFAULT_FOREGROUND, $im_a_style['fg']);
        $this->assertEquals(IRCTextParser::DEFAULT_BACKGROUND, $im_a_style['bg']);
        $this->assertFalse($im_a_style['bold']);
        $this->assertFalse($im_a_style['underline']);
        $this->assertFalse($im_a_style['italic']);
    }

    public function testMessageWithFgAndBgUsingReverse() {
        $hf = new HtmlFacility(IRCTextParser::parse("\x16I'm a \x031,2test"));

        $this->assertTrue($hf->isValid());

        $test_style = $hf->getStyle('test');
        $this->assertEquals(2, $test_style['fg']);
        $this->assertEquals(1, $test_style['bg']);
        $this->assertFalse($test_style['bold']);
        $this->assertFalse($test_style['underline']);
        $this->assertFalse($test_style['italic']);

        $im_a_style = $hf->getStyle("I'm a ");
        $this->assertEquals(IRCTextParser::DEFAULT_BACKGROUND, $im_a_style['fg']);
        $this->assertEquals(IRCTextParser::DEFAULT_FOREGROUND, $im_a_style['bg']);
        $this->assertFalse($im_a_style['bold']);
        $this->assertFalse($im_a_style['underline']);
        $this->assertFalse($im_a_style['italic']);
    }

    public function testMessageNestedFormats() {
        $hf = new HtmlFacility(IRCTextParser::parse("abc\x02d\x031,2e\x0ffg"));

        $this->assertTrue($hf->isValid());

        $b_style = $hf->getStyle("b");
        $this->assertEquals(IRCTextParser::DEFAULT_FOREGROUND, $b_style['fg']);
        $this->assertEquals(IRCTextParser::DEFAULT_BACKGROUND, $b_style['bg']);
        $this->assertFalse($b_style['bold']);
        $this->assertFalse($b_style['underline']);
        $this->assertFalse($b_style['italic']);

        $d_style = $hf->getStyle("d");
        $this->assertTrue($d_style['bold']);
        $this->assertEquals(IRCTextParser::DEFAULT_FOREGROUND, $d_style['fg']);
        $this->assertEquals(IRCTextParser::DEFAULT_BACKGROUND, $d_style['bg']);
        $this->assertFalse($d_style['underline']);
        $this->assertFalse($d_style['italic']);

        $e_style = $hf->getStyle('e');
        $this->assertEquals(1, $e_style['fg']);
        $this->assertEquals(2, $e_style['bg']);
        $this->assertTrue($e_style['bold']);
        $this->assertFalse($e_style['underline']);
        $this->assertFalse($e_style['italic']);
    }

    public function testMessageComplexNestedFormats() {
        $hf = new HtmlFacility(IRCTextParser::parse(
            "abc\x02d\x031,2efg\x02hijk\x02lm\x03nopq\x031rst\x03,3uvwxyz"
        ));

        // \x02 => bold, \x03 => colour

        $this->assertTrue($hf->isValid());

        $h_style = $hf->getStyle('h');
        $this->assertEquals(1, $h_style['fg']);
        $this->assertEquals(2, $h_style['bg']);
        $this->assertFalse($h_style['bold']);

        $n_style = $hf->getStyle('n');
        $this->assertEquals(IRCTextParser::DEFAULT_FOREGROUND, $n_style['fg']);
        $this->assertEquals(2, $n_style['bg']);
        $this->assertTrue($n_style['bold']);

        $r_style = $hf->getStyle('r');
        $this->assertEquals(1, $r_style['fg']);
        $this->assertEquals(2, $r_style['bg']);

        $u_style = $hf->getStyle('u');
        $this->assertEquals(1, $u_style['fg']);
        $this->assertEquals(3, $u_style['bg']);
    }

    /************************************
     * Color tests
     */

    public function testSimpleColour() {
        $message = "05Hello world!";
        $this->assertEquals([
                "fg" => 5,
                "bg" => IRCTextParser::DEFAULT_BACKGROUND,
                "skip" => 2
            ],
            IRCTextParser::parseColour($message)
        );
    }

    public function testSimpleColourWithBackground() {
        $message = "05,04Hello world!";
        $this->assertEquals([
                "fg" => 5,
                "bg" => 4,
                "skip" => 5
            ],
            IRCTextParser::parseColour($message)
        );
    }

    public function testColoursWithSingleNumbers() {
        $message = "5,4Hello world!";
        $this->assertEquals([
                "fg" => 5,
                "bg" => 4,
                "skip" => 3
            ],
            IRCTextParser::parseColour($message)
        );
    }

    public function testColoursWithDefaults() {
        $message = "5Hello world!";
        $this->assertEquals([
                "fg" => 5,
                "bg" => 2,
                "skip" => 1
            ],
            IRCTextParser::parseColour($message, 99, 2)
        );
    }

    public function testInvalidColourCode() {
        $message = "320Hello world!";
        $this->assertEquals([
                "fg" => 3,
                "bg" => IRCTextParser::DEFAULT_BACKGROUND,
                "skip" => 1
            ],
            IRCTextParser::parseColour($message)
        );

        $message = "250,320Hello world!";
        $this->assertEquals([
                "fg" => 2,
                "bg" => IRCTextParser::DEFAULT_BACKGROUND,
                "skip" => 1
            ],
            IRCTextParser::parseColour($message)
        );
    }

    public function testInvalidColourFragment() {
        $message = "Hello world!";
        $this->assertEquals([
                "fg" => IRCTextParser::DEFAULT_FOREGROUND,
                "bg" => IRCTextParser::DEFAULT_BACKGROUND,
                "skip" => 0
            ],
            IRCTextParser::parseColour($message)
        );
    }

}
