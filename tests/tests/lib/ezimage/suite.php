<?php
/**
 * File containing the eZImageTestSuite class
 *
 * @copyright Copyright (C) 1999-2014 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 * @package tests
 */

class eZImageTestSuite extends ezpDatabaseTestSuite
{
    public function __construct()
    {
        parent::__construct();
        $this->setName( "eZImage Test Suite" );

        $this->addTestSuite( 'eZImageManagerTest' );
        $this->addTestSuite( 'eZImageShellHandlerTest' );
    }

    public static function suite()
    {
        return new self();
    }
}

?>
