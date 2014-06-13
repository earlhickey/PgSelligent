<?php

namespace Selligent\Client;

use ZfcBase\EventManager\EventProvider;
use Zend\Soap\Client;

/**
 * Selligent Individual API wrapper
 *
 * @author pG
 * @version 1.0
 * @package Selligent\Client
 * @copyright 2014
 */
class Selligent extends EventProvider
{
    private $login;
    private $password;
    private $listId;
    private $clientId;
    private $soapUrl;
    private $soapClient;

    /**
     * Create a new instance
     * @param string $login Your API login
     * @param string $password Your API password
     * @param string $listId Your list id
     * @param string $clientId Your client id
     * @param string $soapUrl
     */
    function __construct($config)
    {
        /**
         * Set automation user properties
         */
        $this->login = $config['login'];
        $this->password = $config['password'];

        /**
         * Set list properties
         */
        $this->listId = $config['listId'];

        /**
         * Set client id
         */
        $this->clientId = $config['clientId'];

        /**
         * set Soap client
         * containing client id
         */
        $this->soapUrl = sprintf('http://%s.emsecure.net/automation/individual.asmx?WSDL', $this->clientId);
    }

    /**
     * Subscribe
     * @param object $recipient
     * @return array
     */
    public function subscribe($recipient)
    {
        // preparate
        $recipient = $this->preparate($recipient);

        // create input data
        $changes = array(
            array('Key' => 'LASTNAME', 'Value' => $recipient->lastname),
            array('Key' => 'FIRSTNAME', 'Value' => $recipient->firstname),
            array('Key' => 'GENDER', 'Value' => $recipient->gender),
            array('Key' => 'DATEOFBIRTH', 'Value' => $recipient->dateOfBirth),
            // maybe user is opt-out so make sure to opt-in
            array('Key' => 'OPTOUT', 'Value' => null),
            array('Key' => 'OPTOUT_DT', 'Value' => null),
        );

        // check if user exists
        $user = $this->getUserByConstraint(sprintf("MAIL = '%s'", $recipient->email));

        if ($user) {
            // update an existing user
            $result = $this->updateUser($user['ID'], $changes);
        } else {
            // add email to input data
            $changes[] = array('Key' => 'MAIL', 'Value' => $recipient->email);

            // create a new user
            $result = $this->createUser($changes);
        }

        $this->getEventManager()->trigger(__FUNCTION__, $this, array('recipient' => $recipient, 'result' => $result));

        return $result;
    }


    /**
     * Unsubscribe
     * @param object $recipient
     * @return bool
     */
    public function unsubscribe($recipient)
    {
        // check if user exists
        $user = $this->getUserByConstraint(sprintf("MAIL = '%s'", $recipient->email));

        if ($user) {
            $changes = array(
                array('Key' => 'OPTOUT', 'Value' => true),
                array('Key' => 'OPTOUT_DT', 'Value' => date('Y/m/d H:i:s', time())),
            );

            $result = $this->updateUser($user['ID'], $changes);

            return $result;
        }

        return false;
    }


    /**
     *
     * Preparate the recipient data
     *
     * @param   array   $recipient
     * @return  array   $recipient
     *
     */
    private function preparate($recipient)
    {
        // Clean date of birth notation
        if ($recipient->dateOfBirth != '') {
            // test is string can be parsed to DateTime to prevent fatal error
            $stamp = strtotime($recipient->dateOfBirth);
            if ( is_numeric($stamp) && checkdate(date('m', $stamp), date('d', $stamp), date('Y', $stamp)) ) {
                $date = new \DateTime($recipient->dateOfBirth);
                $recipient->dateOfBirth = $date->format('Y-m-d');
            } else {
                // reset this date
                $recipient->dateOfBirth = null;
            }
        }

        // Make sure gender is uppercast and V is set to F
        $gender = strtoupper($recipient->gender);
        $recipient->gender = ($gender == 'V') ? 'F' : $gender;

        return $recipient;
    }

    /**
     * Optimize SOAP result set from array with objects to plain array
     */
    private function optimizeSoapResultSet($resultSet)
    {
        $result = array();
        foreach ($resultSet as $value) {
            $result[$value->Key] = $value->Value;
        }

        return $result;
    }

    /**
     * Retrieve user information based on a contraint
     *
     * @param string $constraint
     * @return array $result->ResultSet->Property
     */
    private function getUserByConstraint($constraint)
    {
        // Prepare call
        $inputData = array('List' => $this->listId, 'Constraint' => $constraint);

        // SOAP call
        $result = $this->getSoapClient()->GetUserByConstraint($inputData, $ResultSet = array(), $ErrorStr = '');

        // Error
        if ($result->ErrorStr != '') {
            // let's asume it's an 'no user found' error
            $this->getEventManager()->trigger(__FUNCTION__, $this, array('constraint' => $constraint, 'result' => $result->ErrorStr));
            return false;
        }

        return $this->optimizeSoapResultSet($result->ResultSet->Property);
    }

    /**
     * Create a user in the specified list
     *
     * @param array $changes
     * @return bool
     */
    private function createUser($changes)
    {
        // Prepare call
        $inputData = array('List' => $this->listId, 'Changes' => $changes);

        // SOAP call
        $result = $this->getSoapClient()->CreateUser($inputData, $ID = '', $ErrorStr = '');

        // Error
        if ($result->ErrorStr != '') {
            $this->getEventManager()->trigger(__FUNCTION__, $this, array('inputData' => $inputData, 'result' => $result->ErrorStr));
            return false;
        }

        return $result->ID;
    }

    /**
     * Update a user in the specified list
     *
     * @param int $userId
     * @param array $changes
     * @return bool
     */
    private function updateUser($userId, $changes)
    {
        // Prepare call
        $inputData = array('List' => $this->listId, 'UserID' => $userId, 'Changes' => $changes);

        // SOAP call
        $result = $this->getSoapClient()->UpdateUser($inputData, $ErrorStr = '');

        // Error
        if ($result->ErrorStr != '') {
            $this->getEventManager()->trigger(__FUNCTION__, $this, array('inputData' => $inputData, 'result' => $result->ErrorStr));
            return false;
        }

        return true;
    }

    /**
     * Load SOAP client
     * @return object   Soap client
     */
    private function getSoapClient()
    {
        if(null === $this->soapClient) {

            $this->soapClient = new \soapclient($this->soapUrl);

            $header = new \SoapHeader(
                'http://tempuri.org/', 'AutomationAuthHeader', array(
                    'Login' => $this->login,
                    'Password' => $this->password,
                )
            );

            $this->soapClient->__setSoapHeaders($header);

        }

        return $this->soapClient;
    }

}
