<?php
//
// Definition of eZRSSExport class
//
// Created on: <18-Sep-2003 11:13:56 kk>
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ publish
// SOFTWARE RELEASE: 3.10.x
// COPYRIGHT NOTICE: Copyright (C) 1999-2006 eZ systems AS
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//

/*! \file ezrssexport.php
*/

/*!
  \class eZRSSExport ezrssexport.php
  \brief Handles RSS Export in eZ publish

  RSSExport is used to create RSS feeds from published content. See kernel/rss for more files.
*/

include_once( 'kernel/classes/ezpersistentobject.php' );
include_once( 'kernel/classes/ezrssexportitem.php' );
include_once( "lib/ezdb/classes/ezdb.php" );

define( "EZ_RSSEXPORT_STATUS_VALID", 1 );
define( "EZ_RSSEXPORT_STATUS_DRAFT", 0 );

class eZRSSExport extends eZPersistentObject
{
    /*!
     Initializes a new RSSExport.
    */
    function eZRSSExport( $row )
    {
        $this->eZPersistentObject( $row );
    }

    /*!
     \reimp
    */
    static function definition()
    {
        return array( "fields" => array( "id" => array( 'name' => 'ID',
                                                        'datatype' => 'integer',
                                                        'default' => 0,
                                                        'required' => true ),
                                         'title' => array( 'name' => 'Title',
                                                           'datatype' => 'string',
                                                           'default' => ezi18n( 'kernel/rss', 'New RSS Export' ),
                                                           'required' => true ),
                                         'url' => array( 'name' => 'URL',
                                                         'datatype' => 'string',
                                                         'default' => '',
                                                         'required' => true ),
                                         'site_access' => array( 'name' => 'SiteAccess',
                                                                 'datatype' => 'string',
                                                                 'default' => '',
                                                                 'required' => true ),
                                         'modified' => array( 'name' => 'Modified',
                                                              'datatype' => 'integer',
                                                              'default' => 0,
                                                              'required' => true ),
                                         'modifier_id' => array( 'name' => 'ModifierID',
                                                              'datatype' => 'integer',
                                                              'default' => 0,
                                                              'required' => true,
                                                                 'foreign_class' => 'eZUser',
                                                                 'foreign_attribute' => 'contentobject_id',
                                                                 'multiplicity' => '1..*' ),
                                         'created' => array( 'name' => 'Created',
                                                              'datatype' => 'integer',
                                                              'default' => 0,
                                                              'required' => true ),
                                         'creator_id' => array( 'name' => 'CreatorID',
                                                              'datatype' => 'integer',
                                                              'default' => 0,
                                                              'required' => true,
                                                                'foreign_class' => 'eZUser',
                                                                'foreign_attribute' => 'contentobject_id',
                                                                'multiplicity' => '1..*' ),
                                         'description' => array( 'name' => 'Description',
                                                                 'datatype' => 'string',
                                                                 'default' => '',
                                                                 'required' => false ),
                                         'image_id' => array( 'name' => 'ImageID',
                                                              'datatype' => 'integer',
                                                              'default' => 0,
                                                              'required' => false ),
                                         'rss_version' => array( 'name' => 'RSSVersion',
                                                                 'datatype' => 'string',
                                                                 'default' => 0,
                                                                 'required' => true ),
                                         'active' => array( 'name' => 'Active',
                                                            'datatype' => 'integer',
                                                            'default' => 0,
                                                            'required' => true ),
                                         'status' => array( 'name' => 'Status',
                                                            'datatype' => 'integer',
                                                            'default' => 0,
                                                            'required' => true ),
                                         'access_url' => array( 'name' => 'AccessURL',
                                                                'datatype' => 'string',
                                                                'default' => 'rss_feed',
                                                                'required' => false ),
                                         'number_of_objects' => array( 'name' => 'NumberOfObjects',
                                                                       'datatype' => 'integer',
                                                                       'default' => 0,
                                                                       'required' => true ),
                                         'main_node_only' => array( 'name' => 'MainNodeOnly',
                                                                    'datatype' => 'integer',
                                                                    'default' => 0,
                                                                    'required' => true ) ),
                      "keys" => array( "id", 'status' ),
                      'function_attributes' => array( 'item_list' => 'itemList',
                                                      'modifier' => 'modifier',
                                                      'rss-xml' => 'rssXml',
                                                      'image_path' => 'imagePath',
                                                      'image_node' => 'imageNode' ),
                      "increment_key" => "id",
                      "sort" => array( "title" => "asc" ),
                      "class_name" => "eZRSSExport",
                      "name" => "ezrss_export" );

    }

    /*!
     \static
     Creates a new RSS Export with the new RSS Export
     \param User ID

     \return the URL alias object
    */
    static function create( $user_id )
    {
        $config = eZINI::instance( 'site.ini' );
        $dateTime = time();
        $row = array( 'id' => null,
                      'title' => ezi18n( 'kernel/classes', 'New RSS Export' ),
                      'site_access' => '',
                      'modifier_id' => $user_id,
                      'modified' => $dateTime,
                      'creator_id' => $user_id,
                      'created' => $dateTime,
                      'status' => 0,
                      'url' => 'http://'. $config->variable( 'SiteSettings', 'SiteURL' ),
                      'description' => '',
                      'image_id' => 0,
                      'active' => 1,
                      'access_url' => '' );
        return new eZRSSExport( $row );
    }

    /*!
     Store Object to database
     \note Transaction unsafe. If you call several transaction unsafe methods you must enclose
     the calls within a db transaction; thus within db->begin and db->commit.
    */
    function store( $storeAsValid = false )
    {
        include_once( "kernel/classes/datatypes/ezuser/ezuser.php" );
        $dateTime = time();
        $user = eZUser::currentUser();
        if (  $this->ID == null )
        {
            eZPersistentObject::store();
            return;
        }

        $db = eZDB::instance();
        $db->begin();
        if ( $storeAsValid )
        {
            $oldStatus = $this->attribute( 'status' );
            $this->setAttribute( 'status', EZ_RSSEXPORT_STATUS_VALID );
        }
        $this->setAttribute( 'modified', $dateTime );
        $this->setAttribute( 'modifier_id', $user->attribute( "contentobject_id" ) );
        eZPersistentObject::store();
        $db->commit();
        if ( $storeAsValid )
        {
            $this->setAttribute( 'status', $oldStatus );
        }
    }

    /*!
     Remove the RSS Export.
     \note Transaction unsafe. If you call several transaction unsafe methods you must enclose
     the calls within a db transaction; thus within db->begin and db->commit.
    */
    function remove()
    {
        $exportItems = $this->fetchItems();

        $db = eZDB::instance();
        $db->begin();
        foreach ( $exportItems as $item )
        {
            $item->remove();
        }
        eZPersistentObject::remove();
        $db->commit();
    }

    /*!
     \static
      Fetches the RSS Export by ID.

     \param RSS Export ID
    */
    static function fetch( $id, $asObject = true, $status = EZ_RSSEXPORT_STATUS_VALID )
    {
        return eZPersistentObject::fetchObject( eZRSSExport::definition(),
                                                null,
                                                array( "id" => $id,
                                                       'status' => $status ),
                                                $asObject );
    }

    /*!
     \static
      Fetches the RSS Export by feed access url and is active.

     \param RSS Export access url
    */
    static function fetchByName( $access_url, $asObject = true )
    {
        return eZPersistentObject::fetchObject( eZRSSExport::definition(),
                                                null,
                                                array( 'access_url' => $access_url,
                                                       'active' => 1,
                                                       'status' => 1 ),
                                                $asObject );
    }

    /*!
     \static
      Fetches complete list of RSS Exports.
    */
    static function fetchList( $asObject = true )
    {
        return eZPersistentObject::fetchObjectList( eZRSSExport::definition(),
                                                    null, array( 'status' => 1 ), null, null,
                                                    $asObject );
    }

    function itemList()
    {
        return $this->fetchItems();
    }

    function imageNode()
    {
        if ( isset( $this->ImageID ) and $this->ImageID )
        {
            include_once( "kernel/classes/ezcontentobjecttreenode.php" );
            return eZContentObjectTreeNode::fetch( $this->ImageID );
        }
        return null;
    }

    function imagePath()
    {
        if ( isset( $this->ImageID ) and $this->ImageID )
        {
            include_once( "kernel/classes/ezcontentobjecttreenode.php" );
            $objectNode = eZContentObjectTreeNode::fetch( $this->ImageID );
            if ( isset( $objectNode ) )
            {
                $path_array = $objectNode->attribute( 'path_array' );
                for ( $i = 0; $i < count( $path_array ); $i++ )
                {
                    $treenode = eZContentObjectTreeNode::fetch( $path_array[$i], false, false );
                    if( $i == 0 )
                        $retValue = $treenode['name'];
                    else
                        $retValue .= '/' . $treenode['name'];
                }
                return $retValue;
            }
        }
        return null;

    }

    function modifier()
    {
        if ( isset( $this->ModifierID ) and $this->ModifierID )
        {
            include_once( "kernel/classes/datatypes/ezuser/ezuser.php" );
            return eZUser::fetch( $this->ModifierID );
        }
        return null;
    }

    function rssXml()
    {
        switch( $this->attribute( 'rss_version' ) )
        {
            case '1.0':
            {
                return $this->fetchRSS1_0();
            } break;

            case '2.0':
            {
                return $this->fetchRSS2_0();
            } break;

            default:
            {
                return null;
            } break;
        }

        return null;
    }

    /*!
      Fetches RSS Items related to this RSS Export. The RSS Export Items contain information about which nodes to export information from

      \param RSSExport ID (optional). Uses current RSSExport's ID as default

      \return RSSExportItem list. null if no RSS Export items found
    */
    function fetchItems( $id = false, $status = EZ_RSSEXPORT_STATUS_VALID )
    {
        if ( $id === false )
        {
            if ( isset( $this ) )
            {
                $id = $this->ID;
                $status = $this->Status;
            }
            else
            {
                $itemList = null;
                return $itemList;
            }
        }
        if ( $id !== null )
            $itemList = eZRSSExportItem::fetchFilteredList( array( 'rssexport_id' => $id, 'status' => $status ) );
        else
            $itemList = null;
        return $itemList;
    }

    function getObjectListFilter()
    {
        if ( $this->MainNodeOnly == 1 )
        {
            $this->MainNodeOnly = true;
        }
        else
        {
            $this->MainNodeOnly = false;
        }

        return array( 'number_of_objects' => intval($this->NumberOfObjects),
                      'main_node_only'    => $this->MainNodeOnly
                     );
    }

    /*!
     Get a RSS xml document based on the RSS 2.0 standard based on the RSS Export settings defined by this object

     \param

     \return RSS 2.0 XML document
    */
    function fetchRSS2_0( $id = null )
    {
        if ( $id != null )
        {
            $rssExport = eZRSSExport::fetch( $id );
            return $rssExport->fetchRSS2_0();
        }

        $locale = eZLocale::instance();

        // Get URL Translation settings.
        $config = eZINI::instance();
        if ( $config->variable( 'URLTranslator', 'Translation' ) == 'enabled' )
        {
            $useURLAlias = true;
        }
        else
        {
            $useURLAlias = false;
        }

        $baseItemURL = $this->attribute( 'url' ).'/'; //.$this->attribute( 'site_access' ).'/';

        $doc = new DOMDocument();
        $root = $doc->createElement( 'rss' );
        $root->setAttribute( 'version', '2.0' );
        $doc->appendChild( $root );

        $channel = $doc->createElement( 'channel' );
        $root->appendChild( $channel );

        $channelTitle = $doc->createElement( 'title', $this->attribute( 'title' ) );
        $channel->appendChild( $channelTitle );

        $channelLink = $doc->createElement( 'link', $this->attribute( 'url' ) );
        $channel->appendChild( $channelLink );

        $channelDescription = $doc->createElement( 'description', $this->attribute( 'description' ) );
        $channel->appendChild( $channelDescription );

        $channel->appendChild( $doc->createElement( 'language', $locale->httpLocaleCode() ) );
        $channel->appendChild( $channelLanguage );

        $imageURL = $this->fetchImageURL();
        if ( $imageURL !== false )
        {
            $image = $doc->createElement( 'image' );

            $imageUrlNode = $doc->createElement( 'url', $imageURL );
            $image->appendChild( $imageUrlNode );

            $imageTitle = $doc->createElement( 'title', $this->attribute( 'title' ) );
            $image->appendChild( $imageTitle );

            $imageLink = $doc->createElement( 'link', $this->attribute( 'url' ) );
            $image->appendChild( $imageLink );

            $channel->appendChild( $image );
        }

        $cond = array(
                    'rssexport_id'  => $this->ID,
                    'status'        => $this->Status
                    );
        $rssSources = eZRSSExportItem::fetchFilteredList( $cond );

        $nodeArray = eZRSSExportItem::fetchNodeList( $rssSources, $this->getObjectListFilter() );

        if ( is_array( $nodeArray ) && count( $nodeArray ) )
        {
            $attributeMappings = eZRSSExportItem::getAttributeMappings( $rssSources );
        }

        foreach ( $nodeArray as $node )
        {
            $object = $node->attribute( 'object' );
            $dataMap = $object->dataMap();
            if ( $useURLAlias === true )
            {
                $nodeURL = $baseItemURL . $node->urlAlias();
            }
            else
            {
                $nodeURL = $baseItemURL . 'content/view/full/'.$object->attribute( 'id' );
            }

            // keep track if there's any match
            $doesMatch = false;
            // start mapping the class attribute to the respective RSS field
            foreach ( $attributeMappings as $attributeMapping )
            {
                // search for correct mapping by path
                if ( $attributeMapping[0]->attribute( 'class_id' ) == $object->attribute( 'contentclass_id' ) and
                     in_array( $attributeMapping[0]->attribute( 'source_node_id' ), $node->attribute( 'path_array' ) ) )
                {
                    // found it
                    $doesMatch = true;
                    // now fetch the attributes
                    $title =  $dataMap[$attributeMapping[0]->attribute( 'title' )];
                    $description =  $dataMap[$attributeMapping[0]->attribute( 'description' )];
                    break;
                }
            }

            if( !$doesMatch )
            {
                // no match
                eZDebug::writeWarning( __CLASS__.'::'.__FUNCTION__.': Cannot find matching RSS source node for content object in '.__FILE__.', Line '.__LINE__ );
                $retValue = null;
                return $retValue;
            }

            // title RSS element with respective class attribute content
            unset( $itemTitle );

            $titleContent =  $title->attribute( 'content' );
            if ( strtolower( get_class( $titleContent ) ) == 'ezxmltext' )
            {
                $outputHandler = $titleContent->attribute( 'output' );
                $itemTitleText = $outputHandler->attribute( 'output_text' );
            }
            else
            {
                $itemTitleText = $titleContent;
            }

            $itemTitle = $doc->createElement( 'title', $itemTitleText );

            // title RSS element with respective class attribute content
            unset( $itemDescription );

            $descriptionContent =  $description->attribute( 'content' );
            if ( strtolower( get_class( $descriptionContent ) ) == 'ezxmltext' )
            {
                $outputHandler =  $descriptionContent->attribute( 'output' );
                $itemDescriptionText = $outputHandler->attribute( 'output_text' );
            }
            else
            {
                $itemDescriptionText = $descriptionContent;
            }

            $itemDescription = $doc->createElement( 'description', $itemDescriptionText );

            unset( $itemLink );
            $itemLink = $doc->createElement( 'link', $nodeURL );

            unset( $item );
            $item = $doc->createElement( 'item' );

            unset( $itemPubDate );
            $itemPubDate = $doc->createElement( 'pubDate', gmdate( 'D, d M Y H:i:s', $object->attribute( 'published' ) ) .' GMT' );

            $item->appendChild( $itemPubDate );
            $item->appendChild( $itemTitle );
            $item->appendChild( $itemLink );
            $item->appendChild( $itemDescription );

            $channel->appendChild( $item );
        }

        return $doc;
    }

    /*!
     Get a RSS xml document based on the RSS 1.0 standard based on the RSS Export settings defined by this object

     \param object ID

     \return RSS 1.0 XML document
    */
    function fetchRSS1_0( $id = null )
    {
        if ( $id != null )
        {
            $rssExport = eZRSSExport::fetch( $id );
            return $rssExport->fetchRSS1_0();
        }

        $imageURL = $this->fetchImageURL();

        // Get URL Translation settings.
        $config = eZINI::instance( 'site.ini' );
        if ( $config->variable( 'URLTranslator', 'Translation' ) == 'enabled' )
        {
            $useURLAlias = true;
        }
        else
        {
            $useURLAlias = false;
        }

        $baseItemURL = $this->attribute( 'url' ).'/'; //.$this->attribute( 'site_access' ).'/';

        $doc = new DOMDocument();
        $root = $doc->createElementNS( 'http://www.w3.org/1999/02/22-rdf-syntax-ns#', 'rdf:RDF' );
        $doc->appendChild( $root );

        $channel = $doc->createElementNS( 'http://purl.org/rss/1.0/', 'channel' );
        $channel->setAttributeNS( 'http://www.w3.org/1999/02/22-rdf-syntax-ns#', 'rdf:about', $this->attribute( 'url' ) );
        $root->appendChild( $channel );

        $channelTitle = $doc->createElement( 'title', $this->attribute( 'title' ) );
        $channel->appendChild( $channelTitle );

        $channelUrl = $doc->createElement( 'link', $this->attribute( 'url' ) );
        $channel->appendChild( $channelUrl );

        $channelDescription = $doc->createElement( 'description', $this->attribute( 'description' ) );
        $channel->appendChild( $channelDescription );

        if ( $imageURL !== false )
        {
            $channelImage = $doc->createElemen( 'image' );
            $channelImage->setAttributeNS( 'http://www.w3.org/1999/02/22-rdf-syntax-ns#', 'rdf:resource', $imageURL );
            $channel->appendChild( $channelImage );

            $image = $doc->createElement( 'image' );
            $image->setAttribute( 'http://www.w3.org/1999/02/22-rdf-syntax-ns#', 'rdf:about', $imageURL );

            $imageTitle = $doc->createElement( 'title', $this->attribute( 'title' ) );
            $image->appendChild( $imageTitle );

            $imageLink = $doc->createElement( 'link', $this->attribute( 'url' ) );
            $image->appendChild( $imageLink );

            $imageUrlNode = $doc->createElement( 'url', $imageURL );
            $image->appendChild( $imageUrlNode );

            $root->appendChild( $image );
        }

        $items = $doc->createElement( 'items' );
        $channel->appendChild( $items );

        $rdfSeq = $doc->createElementNS( 'http://www.w3.org/1999/02/22-rdf-syntax-ns#', 'rdf:Seq' );
        $items->appendChild( $rdfSeq );

        $cond = array(
                    'rssexport_id'  => $this->ID,
                    'status'        => $this->Status
                    );
        $rssSources = eZRSSExportItem::fetchFilteredList( $cond );

        $nodeArray = eZRSSExportItem::fetchNodeList( $rssSources, $this->getObjectListFilter() );

        if( is_array( $nodeArray ) && count( $nodeArray ) )
        {
            $attributeMappings = eZRSSExportItem::getAttributeMappings( $rssSources );
        }

        foreach ( $nodeArray as $node )
        {
            $object =  $node->attribute( 'object' );
            $dataMap =  $object->dataMap();
            if ( $useURLAlias === true )
            {
                $nodeURL = $baseItemURL.$node->urlAlias();
            }
            else
            {
                $nodeURL = $baseItemURL.'content/view/full/'.$object->attribute( 'id' );
            }

            unset( $rdfSeqLi );
            $rdfSeqLi = $doc->createElementNS( 'http://www.w3.org/1999/02/22-rdf-syntax-ns#', 'rdf:li' );
            $rdfSeqLi->setAttributeNS( 'http://www.w3.org/1999/02/22-rdf-syntax-ns#', 'rdf:resource', $nodeURL );
            $rdfSeq->appendChild( $rdfSeqLi );

            // keep track if there's any match
            $doesMatch = false;
            // start mapping the class attribute to the respective RSS field
            foreach ( $attributeMappings as $attributeMapping )
            {
                // search for correct mapping by path
                if ( $attributeMapping[0]->attribute( 'class_id' ) == $object->attribute( 'contentclass_id' ) and
                     in_array( $attributeMapping[0]->attribute( 'source_node_id' ), $node->attribute( 'path_array' ) ) )
                {
                    // found it
                    $doesMatch = true;
                    // now fetch the attributes
                    $title =  $dataMap[$attributeMapping[0]->attribute( 'title' )];
                    $description =  $dataMap[$attributeMapping[0]->attribute( 'description' )];
                    break;
                }
            }

            if( !$doesMatch )
            {
                // no match
                $debug = eZDebug::instance();
                $debug->writeWarning( __CLASS__.'::'.__FUNCTION__.': Cannot find matching RSS source node for content object in '.__FILE__.', Line '.__LINE__ );
                $retValue = null;
                return $retValue;
            }

            // title RSS element with respective class attribute content
            unset( $itemTitle );

            $titleContent =  $title->attribute( 'content' );
            if ( strtolower( get_class( $titleContent ) ) == 'ezxmltext' )
            {
                $outputHandler =  $titleContent->attribute( 'output' );
                $itemTitleText = $outputHandler->attribute( 'output_text' );
            }
            else
            {
                $itemTitleText = $titleContent;
            }

            $itemTitle = $doc->createElement( 'title', $itemTitleText );

            // description RSS element with respective class attribute content
            unset( $itemDescription );

            $descriptionContent =  $description->attribute( 'content' );
            if ( strtolower( get_class( $descriptionContent ) ) == 'ezxmltext' )
            {
                $outputHandler =  $descriptionContent->attribute( 'output' );
                $itemDescriptionText = $outputHandler->attribute( 'output_text' );
            }
            else
            {
                $itemDescriptionText = $descriptionContent;
            }

            $itemDescription = $doc->createElement( 'description', $itemDescriptionText );

            unset( $itemLink );
            $itemLink = $doc->createElement( 'link', $nodeURL );

            unset( $item );
            $item = $doc->createElementNS( 'http://purl.org/rss/1.0/', 'item' );
            $item->setAttributeNS( 'http://www.w3.org/1999/02/22-rdf-syntax-ns#', 'rdf:about', $nodeURL );

            $item->appendChild( $itemTitle );
            $item->appendChild( $itemLink );
            $item->appendChild( $itemDescription );

            $root->appendChild( $item );
        }

        return $doc;
    }

    /*!
     \private

     Fetch Image from current ezrss export object. If non exist, or invalid, return false

     \return valid image url
    */
    function fetchImageURL()
    {

        $imageNode =  $this->attribute( 'image_node' );
        if ( !$imageNode )
            return false;

        $imageObject =  $imageNode->attribute( 'object' );
        if ( !$imageObject )
            return false;

        $dataMap =  $imageObject->attribute( 'data_map' );
        if ( !$dataMap )
            return false;

        $imageAttribute =  $dataMap['image'];
        if ( !$imageAttribute )
            return false;

        $imageHandler =  $imageAttribute->attribute( 'content' );
        if ( !$imageHandler )
            return false;

        $imageAlias =  $imageHandler->imageAlias( 'rss' );
        if( !$imageAlias )
            return false;

        $url = eZSys::hostname() . eZSys::wwwDir() .'/'. $imageAlias['url'];
        $url = preg_replace( "#^(//)#", "/", $url );

        return 'http://'.$url;
    }
}
?>
