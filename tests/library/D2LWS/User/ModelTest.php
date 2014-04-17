<?php
/**
 * Desire2Learn Web Serivces for Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 * https://github.com/adamlundrigan/zfD2L/blob/0.1a0/LICENSE
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
 * PHPUnit test for D2LWS_User_Model
 * @author Adam Lundrigan <adamlundrigan@cdli.ca>
 * 
 * @group D2LWS
 * @group D2LWS_Model
 * @group D2LWS_User
 */
class D2LWS_User_ModelTest extends GenericTestCase
{

    /**
     * Test that constructor works as expected
     */
    public function testCreateNewInstanceWithValidConstructorArgument()
    {
        $mock = $this->_createMockDataObject();
        $oUser = new D2LWS_User_Model($mock);
        $this->assertEquals($mock, $oUser->getRawData());
    }

    /**
     * Test that we can create a "blank" instance of the model
     * by providing no argument to the constructor
     */
    public function testCreateNewInstanceWithoutConstructorArgument()
    {
        $oUser = new D2LWS_User_Model();
        $this->assertInstanceOf('stdClass', $oUser->getRawData());
    }

    /**
     * Test that we can create a "blank" instance of the model
     * by providing NULL argument to the constructor
     * @expectedException PHPUnit_Framework_Error
     */
    public function testCreateNewInstanceWithInvalidConstructorArgument()
    {
        $oUser = new D2LWS_User_Model('shouldNotAcceptString');
    }

    /**
     * Test that setUserID and getUserID work as expected
     */
    public function testSetAndGetUserID()
    {
        $testObj = $this->_createMockModel();
        $baseObj = $this->_createMockModel();

        // Set the User ID
        $testObj->setUserID(99);

        // Assert that a change occurred in the test object
        $this->assertNotEquals($testObj, $baseObj);

        // Assert that User ID field was updated
        $this->assertEquals(99, $testObj->getUserID());

        // Assert that no other return values were affected
        $this->_assertModelsSameExcept($testObj, $baseObj, 'UserID');
    }

    /**
     * Test that setUserName and getUserName work as expected
     */
    public function testSetAndGetUserName()
    {
        $testObj = $this->_createMockModel();
        $baseObj = $this->_createMockModel();

        // Set the Username
        $testObj->setUserName('testUserName');

        // Assert that a change occurred in the test object
        $this->assertNotEquals($testObj, $baseObj);

        // Assert that Username field was updated
        $this->assertEquals('testUserName', $testObj->getUserName());

        // Assert that no other return values were affected
        $this->_assertModelsSameExcept($testObj, $baseObj, 'UserName');
    }

    /**
     * Test that setOrgDefinedID and getOrgDefinedID work as expected
     */
    public function testSetAndGetOrgDefinedID()
    {
        $testObj = $this->_createMockModel();
        $baseObj = $this->_createMockModel();

        // Set the Org Defined ID
        $testObj->setOrgDefinedID('myOrgID');

        // Assert that a change occurred in the test object
        $this->assertNotEquals($testObj, $baseObj);

        // Assert that Org Defined ID field was updated
        $this->assertEquals('myOrgID', $testObj->getOrgDefinedID());

        // Assert that no other return values were affected
        $this->_assertModelsSameExcept($testObj, $baseObj, 'OrgDefinedID');
    }

    /**
     * Test that setRoleID and getRoleID work as expected
     */
    public function testSetAndGetRoleID()
    {
        $testObj = $this->_createMockModel();
        $baseObj = $this->_createMockModel();

        // Set the Role ID
        $testObj->setRoleID(33);

        // Assert that a change occurred in the test object
        $this->assertNotEquals($testObj, $baseObj);

        // Assert that Role ID field was updated
        $this->assertEquals(33, $testObj->getRoleID());

        // Assert that no other return values were affected
        $this->_assertModelsSameExcept($testObj, $baseObj, 'RoleID');
    }

    /**
     * Test that setPassword and getPassword work as expected
     */
    public function testSetAndGetPassword()
    {
        $testObj = $this->_createMockModel();
        $baseObj = $this->_createMockModel();

        // Set the Password
        $testObj->setPassword('myTestPassword');

        // Assert that a change occurred in the test object
        $this->assertNotEquals($testObj, $baseObj);

        // Assert that Password field was updated
        $this->assertEquals('myTestPassword', $testObj->getPassword());

        // Assert that no other return values were affected
        $this->_assertModelsSameExcept($testObj, $baseObj, 'Password');
    }

    /**
     * Test that setFirstName and getFirstName work as expected
     */
    public function testSetAndGetFirstName()
    {
        $testObj = $this->_createMockModel();
        $baseObj = $this->_createMockModel();

        // Set the First Name
        $testObj->setFirstName('Testy');

        // Assert that a change occurred in the test object
        $this->assertNotEquals($testObj, $baseObj);

        // Assert that First Name field was updated
        $this->assertEquals('Testy', $testObj->getFirstName());

        // Assert that no other return values were affected
        $this->_assertModelsSameExcept($testObj, $baseObj, 'FirstName');
    }

    /**
     * Test that setLastName and getLastName work as expected
     */
    public function testSetAndGetLastName()
    {
        $testObj = $this->_createMockModel();
        $baseObj = $this->_createMockModel();

        // Set the Last Name
        $testObj->setLastName('McTesterson');

        // Assert that a change occurred in the test object
        $this->assertNotEquals($testObj, $baseObj);

        // Assert that Last Name field was updated
        $this->assertEquals('McTesterson', $testObj->getLastName());

        // Assert that no other return values were affected
        $this->_assertModelsSameExcept($testObj, $baseObj, 'LastName');
    }

    /**
     * Test that setEmailAddress and getEmailAddress work as expected
     */
    public function testSetAndGetEmailAddress()
    {
        $testObj = $this->_createMockModel();
        $baseObj = $this->_createMockModel();

        // Set the Email Address
        $testObj->setEmailAddress('test@test.com');

        // Assert that a change occurred in the test object
        $this->assertNotEquals($testObj, $baseObj);

        // Assert that Email Address field was updated
        $this->assertEquals('test@test.com', $testObj->getEmailAddress());

        // Assert that no other return values were affected
        $this->_assertModelsSameExcept($testObj, $baseObj, 'EmailAddress');
    }

    /**
     * Test that setGender and getGender work as expected
     */
    public function testSetAndGetGender()
    {
        $testObj = $this->_createMockModel();
        $baseObj = $this->_createMockModel();

        // Set the Gender
        $testObj->setGender('Male');

        // Assert that a change occurred in the test object
        $this->assertNotEquals($testObj, $baseObj);

        // Assert that Gender field was updated
        $this->assertEquals('Male', $testObj->getGender());

        // Assert that no other return values were affected
        $this->_assertModelsSameExcept($testObj, $baseObj, 'Gender');
    }

    /**
     * Test that setBirthDate and getBirthDate work as expected
     */
    public function testSetAndGetBirthDate()
    {
        $testObj = $this->_createMockModel();
        $baseObj = $this->_createMockModel();

        // Set the Birth Date
        $testObj->setBirthDate('1981-01-26T11:22:33-03:30');

        // Assert that a change occurred in the test object
        $this->assertNotEquals($testObj, $baseObj);

        // Assert that Birth Date field was updated
        $this->assertEquals('1981-01-26T11:22:33-03:30', $testObj->getBirthDate());

        // Assert that no other return values were affected
        $this->_assertModelsSameExcept($testObj, $baseObj, 'BirthDate');
    }
    
    /**
     * @group GH-9
     */
    public function testSetAndGetBirthDateWithNumericalTimestamp()
    {
        $testObj = $this->_createMockModel();
        $baseObj = $this->_createMockModel();

        // Set the Birth Date
        $testObj->setBirthDate(1234567890);
        
        // Assert that a change occurred in the test object
        $this->assertNotEquals($testObj, $baseObj);

        // Assert that Birth Date field was updated
        $this->assertEquals(date("Y-m-d\TH:i:sP", 1234567890), $testObj->getBirthDate());

        // Assert that no other return values were affected
        $this->_assertModelsSameExcept($testObj, $baseObj, 'BirthDate');
    }

    /**
     * @group GH-1
     */
    public function testSetAndGetBirthDateWorksForOldPeople()
    {
        $testObj = $this->_createMockModel();
        $baseObj = $this->_createMockModel();

        // Set the Birth Date
        $testObj->setBirthDate(-115939800);

        // Assert that a change occurred in the test object
        $this->assertNotEquals($testObj, $baseObj);

        // Assert that Birth Date field was updated
        $this->assertEquals(date("Y-m-d\TH:i:sP", -115939800), $testObj->getBirthDate());

        // Assert that no other return values were affected
        $this->_assertModelsSameExcept($testObj, $baseObj, 'BirthDate');
    }
    
    /**
     * Create mock model object
     * @return D2LWS_User_Model
     */
    public function _createMockModel()
    {
        return new D2LWS_User_Model();
    }

}
