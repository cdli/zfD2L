<?php
/**
 * Desire2Learn Web Serivces for Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 * https://github.com/adamlundrigan/zfD2L/blob/master/LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to adamlundrigan@cdli.ca so we can send you a copy immediately.
 * 
 * @copyright  Copyright (c) 2011 Centre for Distance Learning and Innovation
 * @license    New BSD License 
 * @author     Adam Lundrigan <adamlundrigan@cdli.ca>
 * @author     Thomas Hawkins <thawkins@mun.ca>
 */

/**
 * PHPUnit test for D2LWS_Grade_Value_API
 * @author Adam Lundrigan <adamlundrigan@cdli.ca>
 * 
 * @group D2LWS
 * @group D2LWS_API
 * @group D2LWS_Grade
 * @group D2LWS_Grade_Value
 */
class D2LWS_Grade_Value_APITest extends GenericTestCase
{
    
    /**
     * Load all grade values by org unit
     */
    public function testFindAllByOrgUnit()
    {
        $mock = $this->_getInstanceManagerWithMockSoapClient();

        // SOAP client should return an array of GradeValue records when
        // the specified OrgUnitID exists in D2L and has grade objects
        $mock->getSoapClient()->addCallback("GradesManagement", "GetGradeValuesByOrgUnit",
            function($args) {            
                $result = new stdClass();
                $result->GradeValues = new stdClass();
                $result->GradeValues->GradeValueInfo = array();
                
                for ( $i = 0; $i < 3; $i++ ) {
                    $modelObj = new D2LWS_Grade_Value_Model();                
                    $mockObj = $modelObj->getRawData();
                    $mockObj->OrgUnitId->Id = $args['OrgUnitId']['Id'];
                    $mockObj->OrgUnitId->Source = $args['OrgUnitId']['Source'];
                    $result->GradeValues->GradeValueInfo[] = $mockObj;
                }
                
                return $result;
            }
        );

        // Set up the OrgUnit
        $ou = new D2LWS_OrgUnit_CourseOffering_Model();
        $ou->setID(9999);
        
        // Find the grade objects
        $apiGObj = new D2LWS_Grade_Value_API($mock);
        $gObjSet = $apiGObj->findAllByOrgUnit($ou);

        $this->assertInternalType('array', $gObjSet);
        $this->assertContainsOnly('D2LWS_Grade_Value_Model', $gObjSet);
        $this->assertEquals(3, count($gObjSet));
    }
    
    /**
     * Load all grade objects by org unit
     * @expectedException D2LWS_Grade_Value_Exception_MalformedResponse
     * @todo Should zero results really throw an exception?
     */
    public function testFindAllByOrgUnitWhenOrgUnitHasNoGradeValues()
    {
        $mock = $this->_getInstanceManagerWithMockSoapClient();

        // SOAP client should return an array of GradeValue records when
        // the specified OrgUnitID exists in D2L and has grade objects
        $mock->getSoapClient()->addCallback("GradesManagement", "GetGradeValuesByOrgUnit",
            function($args) {            
                $result = new stdClass();
                $result->GradeValues = new stdClass();                
                return $result;
            }
        );

        // Set up the OrgUnit
        $ou = new D2LWS_OrgUnit_CourseOffering_Model();
        $ou->setID(9999);
        
        // Find the grade objects
        $apiGObj = new D2LWS_Grade_Value_API($mock);
        $gObjSet = $apiGObj->findAllByOrgUnit($ou);
    }
    
    /**
     * Load all grade objects by org unit
     * @expectedException D2LWS_Grade_Value_Exception_InvalidArgument
     */
    public function testFindAllByOrgUnitWhenInstanceOfStdClassIsPassed()
    {
        $mock = $this->_getInstanceManagerWithMockSoapClient();
    
        // Find the grade objects
        $apiGObj = new D2LWS_Grade_Value_API($mock);
        $gObjSet = $apiGObj->findAllByOrgUnit(new stdClass());        
    }
    
    /**
     * Load all grade objects by org unit
     * @expectedException D2LWS_Grade_Value_Exception_InvalidArgument
     */
    public function testFindAllByOrgUnitWhenNonValueIsPassed()
    {
        $mock = $this->_getInstanceManagerWithMockSoapClient();
        $mock->getSoapClient()->addCallback("GradesManagement", "GetGradeValuesByOrgUnit",
            function($args) {}
        );
     
        // Find the grade objects
        $apiGObj = new D2LWS_Grade_Value_API($mock);
        $gObjSet = $apiGObj->findAllByOrgUnit('OhNoItsAString!'); 
    }
    
}