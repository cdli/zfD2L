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
 * OrgUnit API
 */
class D2LWS_OrgUnit_API extends D2LWS_Common
{

    /**
     * Get Child Org Units for specified OU
     * @param int $ouid OUID of org unit to search under
     * @param array $SearchTypes list of OU types to search for
     * @return array Child OUs
     * @todo SOAP method GetChildOrgUnitIds is broken, so we query each org-type-specific method and collate the results.
     * @see https://github.com/cdli/zfD2L/issues/2
     */
    public function getChildrenOf($ouid, $SearchTypes='*')
    {
        $i = $this->getInstance();        
        
        // Default is to search everything
        if ( empty($SearchTypes) ) {
			$SearchTypes = '*';
        }
        
        $result = new stdClass();
        $ChildOrgUnits = array();
        foreach ( (array)$SearchTypes as $Type )
        {
            $Function = ( $Type == '*' ? "GetChildOrgUnitIds" : "GetChild{$Type}s" );
            $ContainerKey =  ( $Type == '*' ? "OrgUnitIds" : "ChildOrgUnits" );
            $InfoField =  ( $Type == '*' ? "OrgUnitIdentifier" : "{$Type}Info" );
            
            try 
            {
                $typeResult = $i->getSoapClient()
                    ->setWsdl($i->getConfig('webservice.org.wsdl'))
                    ->setLocation($i->getConfig('webservice.org.endpoint'))
                    ->$Function(array(
                        'OrgUnitId'=>array(
                            'Id'=>$ouid,
                            'Source'=>'Desire2Learn'
                        )
                    ));
                if ( $typeResult instanceof stdClass && isset($typeResult->$ContainerKey) && isset($typeResult->$ContainerKey->$InfoField) )
                {
                    // Inconsistency: If only a single child is returned, $InfoField is not an array
                    if ( !is_array($typeResult->$ContainerKey->$InfoField) )
                    {
                        $typeResult->$ContainerKey->$InfoField = array($typeResult->$ContainerKey->$InfoField);
                    }
                
                    foreach ( $typeResult->$ContainerKey->$InfoField as $ouObj )
                    {
                        // The GetChild<type>s method calls don't return their type
                        if ( !isset($ouObj->OrgUnitRole) ) $ouObj->OrgUnitRole = $Type;
                        $ouObj->hasData = ( $Type != '*' );
                        $ChildOrgUnits[] = $ouObj;
                    }
                }
            } catch ( Exception $ex ) {
                /*
                 * Circumvent GH-2 for users of LE < 9.4
                 * @see https://github.com/cdli/zfD2L/issues/2)
                 */
                if ( preg_match('{Authorization failed}is', $ex->getMessage()) && $Type == '*' ) {
                    return $this->getChildrenOf($ouid, array('CourseTemplate', 'CourseOffering', 'Group', 'Section', 'Department', 'Semester'));
                }
                throw $ex;
            }
        }

        $Children = array();        
        if ( count($ChildOrgUnits) > 0 )
        {
            
            foreach ( $ChildOrgUnits as $OUI )
            {
                $subid = $OUI->OrgUnitId->Id;
                $ouAPI = $this->getSubtypeAPI($OUI->OrgUnitRole);
                if ( $ouAPI instanceof D2LWS_Common )
                {
                    if ( $OUI->hasData )
                    {
                        // Create a new instance of the model for this Org Unit type
                        // We already have the data to build it, so why not...
                        $className = preg_replace("/_API$/i", "_Model", get_class($ouAPI));
                        if ( @class_exists($className) )
                        {
                           $Children[$subid] = new $className($OUI);
                        } 
                    }
                    else
                    {
                        // Otherwise, load via SOAP call
                        $Children[$subid] = $ouAPI->findByID($OUI->OrgUnitId->Id);
                    }
                }
            }
        }
        return $Children;
    }

    /**
     * Get new D2LWS model instance of the specified org unit type
     * @param string $type OrgUnit type 
     * @return D2LWS_OrgUnit_Model 
     */
    public function getSubtypeAPI($type)
    {
        $type = preg_replace("/[^a-z0-9]/i", "", $type);
        if ( strlen($type) > 0 )
        {
            $Class = preg_replace("/_API$/", "_{$type}_API", __CLASS__);
            if ( @class_exists($Class) && is_subclass_of($Class, "D2LWS_Common") )
            {
                $api = new $Class($this->getInstance());
                return $api;
            }
        }
        
        return NULL;
    }
    
    /** 
     * Get Parent Org Units of specified OU
     */
    public function getParentsOf($ouid, $SearchTypes=NULL)
    {
        $i = $this->getInstance();  
        $Parents = array();
        
        // Default is to search everything
        if ( is_null($SearchTypes) ) {
            $SearchTypes = array('CourseTemplate', 'CourseOffering', 'Group', 'Section', 'Department', 'Semester');
        }
        
        $typeResult = $i->getSoapClient()
            ->setWsdl($i->getConfig('webservice.org.wsdl'))
            ->setLocation($i->getConfig('webservice.org.endpoint'))
            ->GetParentOrgUnitIds(array(
                'OrgUnitId'=>array(
                    'Id'=>$ouid,
                    'Source'=>'Desire2Learn'
                )
            ));
        
        if ( $typeResult instanceof stdClass && isset($typeResult->OrgUnitIds) && isset($typeResult->OrgUnitIds->OrgUnitIdentifier) )
        {
            if ( !is_array($typeResult->OrgUnitIds) )
                $typeResult->OrgUnitIds = array($typeResult->OrgUnitIds);
            
            foreach ( $typeResult->OrgUnitIds as $OrgUnit )
            {
                if ( isset($OrgUnit->OrgUnitIdentifier) && isset($OrgUnit->OrgUnitIdentifier->OrgUnitRole) )
                {
                    if ( in_array($OrgUnit->OrgUnitIdentifier->OrgUnitRole, $SearchTypes) )
                    {
                        $ouAPI = $this->getSubtypeAPI($OrgUnit->OrgUnitIdentifier->OrgUnitRole);
                        if ( $ouAPI instanceof D2LWS_Common )
                        {
                            $className = preg_replace("/_API$/i", "_Model", get_class($ouAPI));
                            if ( @class_exists($className) )
                            {
                                try
                                {
                                    $Parents[$OrgUnit->OrgUnitIdentifier->OrgUnitId->Id] =$ouAPI->findById($OrgUnit->OrgUnitIdentifier->OrgUnitId->Id);
                                }
                                catch ( Exception $ex )
                                {
                                    
                                }
                            }
                        }
                    }
                }
            }
        }
        return $Parents;
    }
    
    
    public function getEnrollmentsFor($ouid)
    {
        $Enrollments = array();
        $i = $this->getInstance();  
        
        $result = $i->getSoapClient()
            ->setWsdl($i->getConfig('webservice.user.wsdl'))
            ->setLocation($i->getConfig('webservice.user.endpoint'))
            ->GetEnrollmentsByOrgUnit(array(
                'OrgUnitId'=>array(
                    'Id'=>$ouid,
                    'Source'=>'Desire2Learn'
                )
            ));
        
        if ( $result instanceof stdClass && isset($result->OrgUnitEnrollments) && isset($result->OrgUnitEnrollments->OrgUnitEnrollmentInfo) )
        {
            if ( !is_array($result->OrgUnitEnrollments->OrgUnitEnrollmentInfo) )
            {
                $result->OrgUnitEnrollments->OrgUnitEnrollmentInfo = array(
                    $result->OrgUnitEnrollments->OrgUnitEnrollmentInfo
                );
            }
            
            $Enrollments = $result->OrgUnitEnrollments->OrgUnitEnrollmentInfo;            
        }
        
        return $Enrollments;
    }
    
    /**
     * Create Parent-Child Association between Org Units
     * @param D2LWS_OrgUnit_Model $parent
     * @param D2LWS_OrgUnit_Model $child 
     */
    public function createParentChildAssociation(D2LWS_OrgUnit_Model $parent, D2LWS_OrgUnit_Model $child)
    {
        $i = $this->getInstance();  
        $result = $i->getSoapClient()
            ->setWsdl($i->getConfig('webservice.org.wsdl'))
            ->setLocation($i->getConfig('webservice.org.endpoint'))
            ->AddChildOrgUnit(array(
                'OrgUnitId'=>array(
                    'Id'=>$parent->getID(),
                    'Source'=>'Desire2Learn'
                ),
                'ChildOrgUnitId'=>array(
                    'Id'=>$child->getID(),
                    'Source'=>'Desire2Learn'
                )
            ));
        return ( $result instanceof stdClass && empty($result) );
    }
    
}
