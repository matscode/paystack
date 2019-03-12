<?php
/**
 * Description
 *
 * @package     PayStack
 * @category    Source
 * @author      Michael Akanji <matscode@gmail.com>
 * @date        2019-03-09
 */

namespace Matscode\Http;

use function _\internal\parent;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Matscode\Utility\StringPlay;

class RequestBridge extends Client
{
    private
        $_requestMethod,
        $_requestHeaders = [],
        $_requestPath,
        $_requestBody = [];

    public function __construct($endpoint, $method = 'POST')
    {
        // set the default request method
        $this->setRequestMethod($method);

        parent::__construct(['base_uri' => StringPlay::removeSlashes($endpoint) . '/']);
    }

    public function setRequestMethod($method)
    {
        $this->_requestMethod = $method;

        return $this;
    }

    public function setRequestHeader($header, $value)
    {
        $this->_requestHeaders[$header] = $value;

        return $this;
    }

    public function setRequestAuthKey($auth_key)
    {
        $this->setRequestHeader('Authorization', 'Bearer ' . $auth_key);

        return $this;
    }

    public function setRequestPath($path)
    {
        $this->_requestPath = $path;

        return $this;
    }

    public function setRequestBody(array $request_body)
    {
        $this->_requestBody = $request_body;

        return $this;
    }

    /**
     * This method is bound to init the connection to a server
     */
    public function connect()
    {
        $request = new Request($this->_requestMethod, $this->_requestPath, $this->_requestHeaders, $this->_requestBody);

        try {
            return $this->send($request);
        } catch (GuzzleException $e) {
            dump($e);
        }
    }
}
