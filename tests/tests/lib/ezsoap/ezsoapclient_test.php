<?php
/**
 * File containing the eZSOAPClientTest class.
 *
 * @copyright Copyright (C) 1999-2014 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 * @package tests
 */

class eZSOAPClientTest extends ezpTestCase
{
    public static function providerTestSoapClientConstructorUseSSL()
    {
        return array(
            array( 80, false ),
            array( 80, false, false ),
            array( 80, true, true ),
            array( 443, true ),
            array( 443, false, false ),
            array( 443, true, true ),
            array( 'ssl', true ),
            array( 'ssl', true, true ),
            array( 'ssl', false, false ),
        );
    }

    /**
     * @dataProvider providerTestSoapClientConstructorUseSSL
     */
    public function testSoapClientConstructorUseSSL( $port, $expectedUseSSLResult, $useSSL = null )
    {
        $client = new eZSOAPClient( 'soap.example.com', '/', $port, $useSSL );
        $this->assertEquals( $this->readAttribute( $client, 'UseSSL' ), $expectedUseSSLResult );
    }

    public static function providerTestSoapClientConstructorPort()
    {
        return array(
            array( 80, 80 ),
            array( 443, 443 ),
            array( 'ssl', 443 )
        );
    }

    /**
     * @dataProvider providerTestSoapClientConstructorPort
     */
    public function testSoapClientConstructorPort( $port, $expectedPortResult )
    {
        $client = new eZSOAPClient( 'soap.example.com', '/', $port );
        $this->assertEquals( $this->readAttribute( $client, 'Port' ), $expectedPortResult );
    }
}

?>
