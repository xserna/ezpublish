<?php
/**
 * File containing the eZSysTest class
 *
 * @copyright Copyright (C) 1999-2014 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 * @package tests
 */

class eZSysTest extends ezpTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->setName( "eZSysTest" );
    }

    /**
     * Test eZSys $AccessPath as it worked prior to 4.4 without propertied to
     * define scope of path with RemoveSiteAccessIfDefaultAccess=enabled
     */
    public function testIndexFileRemoveSiteAccessIfDefaultAccessEnabled()
    {
        // TEST SETUP --------------------------------------------------------
        $ini = eZINI::instance();

        $defaultAccess = $ini->variable( 'SiteSettings', 'DefaultAccess' );
        $this->setSiteAccess( $defaultAccess );

        // Make sure to preserve ini settings in case other tests depend on them
        $orgRemoveSiteaccess = $ini->variable( 'SiteAccessSettings', 'RemoveSiteAccessIfDefaultAccess' );

        // ENABLE RemoveSiteAccessIfDefaultAccess
        $ini->setVariable( 'SiteAccessSettings', 'RemoveSiteAccessIfDefaultAccess', 'enabled' );
        // -------------------------------------------------------------------

        // TEST --------------------------------------------------------------
        $indexFile = eZSys::indexFile();
        self::assertEquals( "", $indexFile );

        eZSys::addAccessPath( array( 'testing', 'indexFile' ) );
        $indexFile = eZSys::indexFile();
        self::assertEquals( "/testing/indexFile", $indexFile );
        // -------------------------------------------------------------------

        // TEST TEAR DOWN ----------------------------------------------------
        $ini->setVariable( 'SiteAccessSettings', 'RemoveSiteAccessIfDefaultAccess', $orgRemoveSiteaccess );
        eZSys::clearAccessPath();
        // -------------------------------------------------------------------
    }

    /**
     * Test eZSys $AccessPath as it worked prior to 4.4 without propertied to
     * define scope of path with RemoveSiteAccessIfDefaultAccess=disabled
     */
    public function testIndexFileRemoveSiteAccessIfDefaultAccessDisabled()
    {
        // TEST SETUP --------------------------------------------------------
        $ini = eZINI::instance();

        $defaultAccess = $ini->variable( 'SiteSettings', 'DefaultAccess' );
        $this->setSiteAccess( $defaultAccess );

        // Make sure to preserve ini settings in case other tests depend on them
        $orgRemoveSiteaccess = $ini->variable( 'SiteAccessSettings', 'RemoveSiteAccessIfDefaultAccess' );

        // DISABLE RemoveSiteAccessIfDefaultAccess
        $ini->setVariable( 'SiteAccessSettings', 'RemoveSiteAccessIfDefaultAccess', 'disabled' );
        // -------------------------------------------------------------------

        // TEST --------------------------------------------------------------
        $indexFile = eZSys::indexFile();
        self::assertEquals( "/$defaultAccess", $indexFile );

        eZSys::addAccessPath( array( 'testing', 'indexFile' ) );
        $indexFile = eZSys::indexFile();
        self::assertEquals( "/$defaultAccess/testing/indexFile", $indexFile );
        // -------------------------------------------------------------------

        // TEST TEAR DOWN ----------------------------------------------------
        $ini->setVariable( 'SiteAccessSettings', 'RemoveSiteAccessIfDefaultAccess', $orgRemoveSiteaccess );
        eZSys::clearAccessPath();
        // -------------------------------------------------------------------
    }

    public function testGlobBrace()
    {
        $pattern = "kernel/classes/ez{content,url}*.php";

        $files = eZSys::globBrace( $pattern );
        self::assertGreaterThan( 20, count( $files ), __METHOD__ . " test with GLOB_BRACE" );
    }

    public function testGlobBraceSupported()
    {
        if ( !defined( 'GLOB_BRACE' ) )
            self::markAsSkipped( "This test can only be run on systems supporting GLOB_BRACE." );

        $pattern = "kernel/classes/ez{content,url}*.php";

        $eZSysGlob = eZSys::globBrace( $pattern );
        $phpGlob = glob( $pattern, GLOB_BRACE );
        self::assertEquals( $eZSysGlob, $phpGlob, "Comparing glob() with eZSys::glob() using GLOB_BRACE" );

        $eZSysGlob = eZSys::globBrace( $pattern, GLOB_NOSORT );
        $phpGlob = glob( $pattern, GLOB_NOSORT | GLOB_BRACE );
        self::assertEquals( $eZSysGlob, $phpGlob, "Comparing glob() with eZSys::glob() using GLOB_MARK | GLOB_BRACE" );
    }

    /**
     * Test eZSys $AccessPath as it works as of 4.4 with propertied to
     * define scope of path with RemoveSiteAccessIfDefaultAccess=enabled
     */
    public function testIndexFileRemoveSiteAccessIfDefaultAccessEnabled2()
    {
        // TEST SETUP --------------------------------------------------------
        $ini = eZINI::instance();

        $defaultAccess = $ini->variable( 'SiteSettings', 'DefaultAccess' );
        $this->setSiteAccess( $defaultAccess );

        // Make sure to preserve ini settings in case other tests depend on them
        $orgRemoveSiteaccess = $ini->variable( 'SiteAccessSettings', 'RemoveSiteAccessIfDefaultAccess' );

        // ENABLE RemoveSiteAccessIfDefaultAccess
        $ini->setVariable( 'SiteAccessSettings', 'RemoveSiteAccessIfDefaultAccess', 'enabled' );
        // -------------------------------------------------------------------

        // TEST --------------------------------------------------------------
        $indexFile = eZSys::indexFile();
        self::assertEquals( "", $indexFile );

        eZSys::setAccessPath( array( 'testing', 'indexFile' ), 'test-path', false );
        $indexFile = eZSys::indexFile();
        self::assertEquals( "/testing/indexFile", $indexFile );
        // -------------------------------------------------------------------

        // TEST TEAR DOWN ----------------------------------------------------
        $ini->setVariable( 'SiteAccessSettings', 'RemoveSiteAccessIfDefaultAccess', $orgRemoveSiteaccess );
        eZSys::clearAccessPath( false );
        // -------------------------------------------------------------------
    }

    /**
     * Test eZSys $AccessPath as it works as of 4.4 with propertied to
     * define scope of path with RemoveSiteAccessIfDefaultAccess=disabled
     */
    public function testIndexFileRemoveSiteAccessIfDefaultAccessDisabled2()
    {
        // TEST SETUP --------------------------------------------------------
        $ini = eZINI::instance();

        $defaultAccess = $ini->variable( 'SiteSettings', 'DefaultAccess' );
        $this->setSiteAccess( $defaultAccess );

        // Make sure to preserve ini settings in case other tests depend on them
        $orgRemoveSiteaccess = $ini->variable( 'SiteAccessSettings', 'RemoveSiteAccessIfDefaultAccess' );

        // DISABLE RemoveSiteAccessIfDefaultAccess
        $ini->setVariable( 'SiteAccessSettings', 'RemoveSiteAccessIfDefaultAccess', 'disabled' );
        // -------------------------------------------------------------------

        // TEST --------------------------------------------------------------
        $indexFile = eZSys::indexFile();
        self::assertEquals( "/$defaultAccess", $indexFile );

        eZSys::setAccessPath( array( 'testing', 'indexFile' ), 'test-path', false );
        $indexFile = eZSys::indexFile();
        self::assertEquals( "/$defaultAccess/testing/indexFile", $indexFile );
        // -------------------------------------------------------------------

        // TEST TEAR DOWN ----------------------------------------------------
        $ini->setVariable( 'SiteAccessSettings', 'RemoveSiteAccessIfDefaultAccess', $orgRemoveSiteaccess );
        eZSys::clearAccessPath( false );
        // -------------------------------------------------------------------
    }

    public function testIsSSLNow()
    {
        $ini = eZINI::instance();

        self::assertFalse( eZSys::isSSLNow() );

        $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
        self::assertTrue( eZSys::isSSLNow() );
        unset( $_SERVER['HTTP_X_FORWARDED_PROTO'] );

        $_SERVER['HTTP_X_FORWARDED_PORT'] = $ini->variable( 'SiteSettings', 'SSLPort' );
        self::assertTrue( eZSys::isSSLNow() );
        unset( $_SERVER['HTTP_X_FORWARDED_PORT'] );

        $_SERVER['HTTP_X_FORWARDED_SERVER'] = $ini->variable( 'SiteSettings', 'SSLProxyServerName' );
        self::assertTrue( eZSys::isSSLNow() );
        unset( $_SERVER['HTTP_X_FORWARDED_SERVER'] );
    }

    public function testServerProtocol()
    {
        self::assertEquals( 'http', eZSys::serverProtocol() );

        $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
        self::assertEquals( 'https', eZSys::serverProtocol() );
        unset( $_SERVER['HTTP_X_FORWARDED_PROTO'] );
    }

    /* -----------------------------------------------------------------------
     * HELPER FUNCTIONS
     * -----------------------------------------------------------------------
     */
     private function setSiteAccess( $accessName )
     {
         eZSiteAccess::change( array( 'name' => $accessName,
                                      'type' => eZSiteAccess::TYPE_URI,
                                      'uri_part' => array( $accessName ) ) );
     }
}

?>
