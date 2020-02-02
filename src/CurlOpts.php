<?php

namespace Palmtree\Curl;

use Palmtree\Curl\Exception\InvalidArgumentException;

class CurlOpts implements \ArrayAccess, \IteratorAggregate
{
    /** @var array */
    public static $defaults = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_AUTOREFERER    => true,
        CURLOPT_USERAGENT      => 'Palmtree\Curl',
        CURLOPT_HEADER         => true,
    ];
    /** @var array */
    private $opts = [];

    public function __construct(array $opts = [])
    {
        foreach (\array_replace(self::$defaults, $opts) as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function toArray(): array
    {
        return $this->opts;
    }

    public function set(int $key, $value): self
    {
        if ($key === CURLOPT_HEADER && !$value) {
            throw new InvalidArgumentException('CURLOPT_HEADER cannot be set to false');
        }

        $this->opts[$key] = $value;

        return $this;
    }

    public function offsetExists($offset)
    {
        return isset($this->opts[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->opts[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        unset($this->opts[$offset]);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->opts);
    }
}
