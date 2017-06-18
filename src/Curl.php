<?php

namespace Palmtree\Curl;

use Palmtree\ArgParser\ArgParser;

class Curl
{
    /**@var string */
    protected $url;
    /**@var resource */
    protected $handle;

    /** @var Request $request */
    protected $request;

    /** @var  Response $response */
    protected $response;

    public static $defaults = [
        'curl_opts' => [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_AUTOREFERER    => true,
            CURLOPT_USERAGENT      => 'Palmtree\Curl',
        ],
    ];

    const HTTP_NOT_FOUND = 404;
    const HTTP_OK_MIN = 200;
    const HTTP_OK_MAX = 299;

    /**
     * Curl constructor.
     *
     * @param array|string $args Array of args or URL.
     */
    public function __construct($args = [])
    {
        $this->args = $this->parseArgs($args);

        $this->handle = curl_init($this->getUrl());

        curl_setopt_array($this->handle, $this->args['curl_opts']);

        $this->request = new Request();
    }

    /**
     * @param string $url
     *
     * @return mixed
     */
    public static function getContents($url, $curlOpts = [])
    {
        $args = [
            'url'       => $url,
            'curl_opts' => $curlOpts,
        ];

        $curl = new self($args);

        return $curl->getResponse()->getBody();
    }

    /**
     * @param array|string $data
     *
     * @return Response
     */
    public function post($data)
    {
        $this->getRequest()->setBody($data);

        return $this->execute();
    }

    /**
     * @return Response
     */
    public function execute()
    {
        $headers = $this->getRequest()->getHeaderStrings();

        if (!empty($headers)) {
            curl_setopt($this->handle, CURLOPT_HTTPHEADER, $headers);
        }

        $body = $this->getRequest()->getBody();

        if (!empty($body)) {
            curl_setopt($this->handle, CURLOPT_POST, true);
            curl_setopt($this->handle, CURLOPT_POSTFIELDS, $body);
        }

        $response   = curl_exec($this->handle);
        $statusCode = curl_getinfo($this->handle, CURLINFO_HTTP_CODE);

        $this->response = new Response($response, $statusCode);

        return $this->response;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        if ($this->response === null) {
            $this->execute();
        }

        return $this->response;
    }

    /**
     * @param string $url
     *
     * @return Curl
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param array|string $args
     *
     * @return array
     */
    protected function parseArgs($args)
    {
        $parser = new ArgParser($args, 'url');

        $parser->parseSetters($this);

        $args = $parser->resolveOptions(static::$defaults);

        return $args;
    }

    public function __toString()
    {
        $body = $this->getResponse()->getBody();

        $body = $body ? : '';

        return $body;
    }
}
