<?php
/**
 * File containing the WorkflowEventRegressionFetchTemplateRepeatType class.
 *
 * @copyright Copyright (C) 1999-2014 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 * @package tests
 */

class WorkflowEventRegressionFetchTemplateRepeatType extends eZWorkflowEventType
{
    const WORKFLOW_TYPE_STRING = 'fetchtemplaterepeat';
    function __construct()
    {
        $this->eZWorkflowEventType( WorkflowEventRegressionFetchTemplateRepeatType::WORKFLOW_TYPE_STRING, "WorkflowEventRegressionFetchTemplateRepeatType test" );
        $this->setTriggerTypes( array( 'content' => array( 'publish' => array( 'before' ) ) ) );
    }

    function execute( $process, $event )
    {
        if ( !isset( $_POST['CompletePublishing'] ) )
        {
            $index = eZSys::indexFile( true );
            $requestUri = eZSys::indexFile( false ) . eZSys::requestUri();
            $replace = "@" . preg_quote( $index ) . "@i";
            $requestUri = preg_replace( array( $replace ), array(''), $requestUri, 1 );

            $process->Template = array( 'templateName' => 'file:' . dirname( __FILE__ ) . basename( __FILE__, '.php' ) .'.tpl',
                                        'templateVars' => array( 'uri' => $requestUri ),
                                        'path' => array( array( 'url' => false, 'text' => 'Workflow event regression: fetch template repeat' ) ) );
            return eZWorkflowType::STATUS_FETCH_TEMPLATE_REPEAT;
        }
        else
        {
            return eZWorkflowType::STATUS_ACCEPTED;
        }
    }
}

eZWorkflowEventType::registerEventType(
    WorkflowEventRegressionFetchTemplateRepeatType::WORKFLOW_TYPE_STRING, "WorkflowEventRegressionFetchTemplateRepeatType" );
?>
