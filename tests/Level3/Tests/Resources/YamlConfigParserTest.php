<?php

namespace Level3\Tests\Resources;

use Level3\Resources\YamlConfigParser;

class YamlConfigParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetShouldFailWithInvalidArgument()
    {
        new YamlConfigParser(sprintf('%s/does-not-exist.yaml', __DIR__));
    }

    /**
     * @expectedException Level3\Resources\Exceptions\ConfigParseError
     */
    public function testGetShouldFailWithConfigParseError()
    {
        $yamlParser = new YamlConfigParser(sprintf('%s/invalid.yaml', __DIR__));
        $yamlParser->getConfig();
    }
    
    public function testGetConfig() {
        $yamlParser = new YamlConfigParser(sprintf('%s/valid.yaml', __DIR__));
        $config = $yamlParser->getconfig();

        $addr = array(
            "given" => "Chris",
            "family"=> "Dumars",
            "address"=> array(
                "lines"=> "458 Walkman Dr.
        Suite #292",
                "city"=> "Royal Oak",
                "state"=> "MI",
                "postal"=> 48046,
            ),
        );
        $expect = array (
            "invoice"=> 34843,
            "date"=> "2001-01-23",
            "bill-to"=> $addr,
            "ship-to"=> $addr,
            "product"=> array(
                array(
                    "sku"=> "BL394D",
                    "quantity"=> 4,
                    "description"=> "Basketball",
                    "price"=> 450,
                ),
                array(
                    "sku"=> "BL4438H",
                    "quantity"=> 1,
                    "description"=> "Super Hoop",
                    "price"=> 2392,
                ),
            ),
            "tax"=> 251.42,
            "total"=> 4443.52,
            "comments"=> "Late afternoon is best. Backup contact is Nancy Billsmer @ 338-4338.",
        );

        $this->assertThat($config, $this->equalTo($expect));
    }
}