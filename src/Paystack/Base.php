<?php
/**
 *
 * Description
 *
 * @package        Paystack
 * @category       Source
 * @author         Michael Akanji <matscode@gmail.com>
 * @date           2017-06-26
 * @copyright (c)  2016 - 2017, TECRUM (http://www.tecrum.com)
 *
 */

namespace Matscode\Paystack;

use Matscode\Http\RequestBridge;
use Matscode\Utility\StringPlay;

class Base
{

    private
        $_apiBaseUrl = 'https://api.paystack.co/', // with trailing slash
        $_curl,
        $_requestBridge,
        $_secretKey,

        /*Getting Error Infomation*/
        $_errorMessages = [];

    public
        $resource,
        $action,
        $args = [],
        $data,
        // response from the endpoint
        $response;

    public function __construct($secretKey)
    {
        {
            // register an error handler
            $whoops = new \Whoops\Run;
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
            $whoops->register();
        }

        // TODO: Get auth key from .env
        $this->_secretKey     = $secretKey;
        $this->_requestBridge = new RequestBridge($this->_apiBaseUrl);
        $this->_requestBridge->setRequestAuthKey($this->_secretKey);

        return $this;
    }

    public function setAction($action)
    {
        return $this->action = StringPlay::removeSlashes($action);
    }

    public function getAction()
    {
        return $this->action;
    }

    public function setResource($resource)
    {
        return $this->resource = StringPlay::removeSlashes($resource);
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function setArgs(array $args)
    {
        $this->args = $args;
    }

    /**
     * Join keywords intended to be appended to the endpoint w/ a suffixed forward slash
     *
     * @return string
     */
    private function getParseArgs()
    {
        if (count($this->args)) {
            return '/' . implode('/', $this->args);
        }
        // return empty string
        return '';
    }

    /**
     * Initiate Request to the paystack RESTful API and return response Obj
     *
     * @param array  $withData
     * @param string $requestMethod
     * @param bool   $returnArray set to true to return response as associate array
     *
     * @return mixed
     * @throws \Exception
     */
    public function sendRequest(array $withData = [], $requestMethod = 'POST', $returnArray = false)
    {
        $this->data = $withData;

        $uriPath = $this->getResource() . '/' . $this->getAction() . $this->getParseArgs();

        dump($this->data);
        // send the request and return result as json object
        $response = $this->_requestBridge->setRequestPath($uriPath)->setRequestBody($this->data)->connect();

        // $this->_curl = (new CURL($this->_endPoint, $requestMethod))
        //     ->setRequestHeader('Authorization', 'Bearer ' . $this->_secretKey);

        // $this->response = json_decode($this->_curl->run($this->data, 'json'));

        return $this->response;
    }

    /**
     * @param mixed $errorMessages
     */
    public function setErrorMessages($errorMessages)
    {
        //if errorMessages is string
        if (is_string($errorMessages)) {
            $this->_errorMessages[] = $errorMessages;
        }
        //if errorMessages is array
        if (is_array($errorMessages)) {
            $this->_errorMessages = array_merge($this->_errorMessages, $errorMessages);
        }
    }

    /**
     * @param bool   $toString
     * @param string $delimiter
     *
     * @return array|string
     */
    public function getErrorMessages($toString = false, $delimiter = '<br>')
    {
        $errorMessages = $this->_errorMessages;
        if ($toString) {
            // return errorMessage as String
            unset($errorMessages); //to avoid datatype conflict
            $errorMessages = join($delimiter, $this->_errorMessages);
        }

        return $errorMessages;
    }


}
