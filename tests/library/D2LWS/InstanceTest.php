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
 * PHPUnit test for D2LWS_Instance
 * @author Adam Lundrigan <adamlundrigan@cdli.ca>
 * 
 * @group D2LWS
 * @group D2LWS_Core
 */
class D2LWS_InstanceTest extends GenericTestCase
{
    
    /**
     * Test our ability to load up a mock instance manager
     */
    public function testCanGetInstanceManagerWithMockSoapClient()
    {
        $o = $this->_getInstanceManagerWithMockSoapClient();
        $this->assertInstanceOf("D2LWS_Instance", $o);
        $this->assertInstanceOf("MockSoapClient", $o->getSoapClient());
        $this->assertInstanceOf("D2LWS_Soap_Client", $o->getSoapClient());
    }

    /**
     * Test that exception will be raised if no configuration file is provided
     * @expectedException D2LWS_Exception_ConfigurationFileNotFound
     */
    public function testInstanceManagerWillThrowExceptionWhenConfigurationFileNotFound()
    {
        //TODO: Disable @ when error with 'undefined constant SOAP_1_2' has been sorted
        $i = new D2LWS_Instance("/path/to/non-existent/desire2learn.ini");
        $this->fail('D2LWS_Instance accepted a non-existent configuration file');
    }

    /**
     * Test that Instance Manager will create a stock SOAP client if
     * we haven't used setSoapClient() to assign one
     */
    public function testGetSoapClientWillCreateNewInstanceIfNoneAlreadyExists()
    {
        $o = $this->_getInstanceManager();
        //TODO: Remove @ once error with undefined constant SOAP_1_2 is located and fixed
        $this->assertInstanceOf("D2LWS_Soap_Client", @$o->getSoapClient());
    }

    /**
     * Test that Instance Manager will create a stock authentication manager if
     * we haven't used setAuthManager() to assign one
     */
    public function testGetAuthManagerWillCreateNewInstanceIfNoneAlreadyExists()
    {
        $o = $this->_getInstanceManager();
        //TODO: Remove @ once error with undefined constant SOAP_1_2 is located and fixed
        $this->assertInstanceOf("D2LWS_Authenticate", @$o->getAuthManager());
    }

    /**
     * Test that getConfig() works
     */
    public function testGetConfigPerformsVariableSubstitutionsProperly()
    {
        $o = $this->_getInstanceManagerWithMockSoapClient();
        $cfgValue = $o->getConfig('webservice.auth.wsdl');
        $this->assertNotContains("[[+server.hostname]]", $cfgValue);
    }

    /**
     * Test that getConfig() will reject empty argument
     */
    public function testGetConfigWillRejectEmptyArgument()
    {
        $o = $this->_getInstanceManagerWithMockSoapClient();
        $cfgValue = $o->getConfig('');
        $this->assertFalse($cfgValue);
    }

    /**
     * Test that getConfig() will reject non-existent sub-key
     */
    public function testGetConfigWillRejectNonExistentSubKey()
    {
        $o = $this->_getInstanceManagerWithMockSoapClient();
        $cfgValue = $o->getConfig('server.nonExistentSubKey');
        $this->assertFalse($cfgValue);
    }

    /**
     * Test that getAuthToken() will trigger exception
     * @expectedException D2LWS_Soap_Client_Exception_AuthenticationFailed
     */
    public function testGetAuthTokenWillTriggerException()
    {
        $o = $this->_getInstanceManagerWithMockSoapClient();
        $o->getSoapClient()->addCallback("AuthenticationToken", "Authenticate2",
           function ($args) {
               return false;
           }
        );
        $this->assertFalse($o->getAuthToken());
    }
    
    /**
     * Test our ability to load up Instance Manager with a
     * custom instance of the Authenticator
     */
    public function testCanCreateInstanceManagerWithCustomAuthenticator()
    {
        $o = $this->_getInstanceManagerWithMockSoapClient();
        $auth = new D2LWS_Authenticate($o);
        $o->setAuthManager($auth);
        $this->assertInstanceOf("D2LWS_Instance", $o);
        $this->assertInstanceOf("D2LWS_Authenticate", $o->getAuthManager());
        $this->assertSame($auth, $o->getAuthManager());
    }
}