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
        //$this->_debug($this->dom); echo "\n";
        return $this->_getStyle($textFragment, $this->dom, [
            'fg' => false,
            'bg' => false,
            'bold' => false,
            'italic' => false,
            'underline' => false
        ]);
    }

    private function _debug(\SimpleXMLElement $e, $indent = 0) {
        foreach ($e as $k => $v) {
            echo "\n" . str_pad($k, $indent, ' ', STR_PAD_LEFT) . '[' . $v->attributes() . '] ' . $v[0];
            $this->_debug($v, $indent + 4);
        }
    }


    private function _getStyle($textFragment, $sxe, $styles) {
        foreach ($sxe as $k => $v) {
            if($k == 'span') {
                // Check for styles
                $class = $v->attributes()->__toString();
                switch ($class) {
                    case 'bold':
                    case 'italic':
                    case 'underline':
                        $styles[$class] = true;
                        break;
                    default:
                        $colour_info = explode('-', $class);
                        $styles['fg'] = $colour_info[2];
                        $styles['bg'] = $colour_info[3];
                }
            }
            if (strpos($v[0], $textFragment) !== false) {
                return $styles;
            }
            return $this->_getStyle($textFragment, $v, $styles);
        }
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
        $this->assertFalse($im_a_style['fg']);
        $this->assertFalse($im_a_style['bg']);
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
        $this->assertFalse(IRCTextParser::DEFAULT_FOREGROUND, $im_a_style['bg']);
        $this->assertFalse($im_a_style['bold']);
        $this->assertFalse($im_a_style['underline']);
        $this->assertFalse($im_a_style['italic']);
    }

    public function testMessageNestedFormats() {
        $hf = new HtmlFacility(IRCTextParser::parse("abc\x02d\x031,2e\x0ffg"));

        $this->assertTrue($hf->isValid());

        $b_style = $hf->getStyle("b");
        $this->assertFalse($b_style['fg']);
        $this->assertFalse($b_style['bg']);
        $this->assertFalse($b_style['bold']);
        $this->assertFalse($b_style['underline']);
        $this->assertFalse($b_style['italic']);

        $d_style = $hf->getStyle("d");
        $this->assertTrue($d_style['bold']);
        $this->assertFalse($d_style['fg']);
        $this->assertFalse($d_style['bg']);
        $this->assertFalse($d_style['underline']);
        $this->assertFalse($d_style['italic']);

        $e_style = $hf->getStyle('e');
        $this->assertEquals(1, $e_style['fg']);
        $this->assertEquals(2, $e_style['bg']);
        $this->assertTrue($e_style['bold']);
        $this->assertFalse($e_style['underline']);
        $this->assertFalse($e_style['italic']);
    }

    public function testNestedFormats() {
        $hf = new HtmlFacility(IRCTextParser::parse(
            "abc\x02d\x031,2efg\x02hijk\x02lm\x03nopqrstuvwxyz"
        ));

        // \x02 => bold, \x03 => colour

        $this->assertTrue($hf->isValid());

        $h_style = $hf->getStyle('h');
        $this->assertEquals(1, $h_style['fg']);
        $this->assertEquals(2, $h_style['bg']);
        $this->assertFalse($h_style['bold']);

        $n_style = $hf->getStyle('n');
        $this->assertEquals(IRCTextParser::DEFAULT_FOREGROUND, $n_style['fg']);
        $this->assertEquals(IRCTextParser::DEFAULT_BACKGROUND, $n_style['bg']);
        $this->assertTrue($n_style['bold']);
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
