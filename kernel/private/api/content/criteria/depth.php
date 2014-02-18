<?php
/**
 * File containing ezpContentDepthCriteria class
 *
 * @copyright Copyright (C) 1999-2014 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 * @package kernel
 */

/**
 * Depth criteria
 * @package API
 */
class ezpContentDepthCriteria implements ezpContentCriteriaInterface
{
    /**
     * Maximum depth to dig while fetching
     * @var int
     */
    private $depth;

    public function __construct( $depth )
    {
        $this->depth = (int)$depth;
    }

    public function translate()
    {
        return array(
            'type'      => 'param',
            'name'      => array( 'Depth' ),
            'value'     => array( $this->depth )
        );
    }

    public function __toString()
    {
        return 'With depth '.$this->depth;
    }
}
?>
