<?php

namespace Palmtree\Curl\Tests\Fixtures;

class WebServer
{
    /** @var string */
    private $host;
    /** @var string */
    private $documentRoot;
    /** @var int */
    private $pid;
    /** @var int */
    private $port;

    private const SERVER_TIMEOUT_SECONDS = 5;

    public function __construct(string $host, string $documentRoot)
    {
        $this->host         = $host;
        $this->documentRoot = $documentRoot;
    }

    private function findFreePort(): int
    {
        $sock = \socket_create_listen(0);
        \socket_getsockname($sock, $addr, $port);
        \socket_close($sock);

        return $port;
    }

    public function start(): int
    {
        $this->port = $this->findFreePort();

        $command = \sprintf(
            'php -S %s:%d -t %s >/dev/null 2>&1 & echo $!',
            $this->host,
            $this->port,
            $this->documentRoot
        );

        \exec($command, $output);

        $this->pid = (int)$output[0];

        $start     = \microtime(true);
        $connected = false;

        while (\microtime(true) - $start <= self::SERVER_TIMEOUT_SECONDS) {
            if ($this->canConnect()) {
                $connected = true;
                break;
            }
        }

        if (!$connected) {
            $this->stop();
            throw new \RuntimeException('Timed out');
        }

        return $this->port;
    }

    public function stop(): void
    {
        \exec('kill ' . (int)$this->pid);
    }

    public function getUrl($path = '', bool $https = false): string
    {
        $scheme = $https ? 'https' : 'http';

        $url = "$scheme://$this->host:$this->port";

        if (!empty($path)) {
            $url .= "/$path";
        }

        return $url;
    }

    private function canConnect(): bool
    {
        $handle = @\fsockopen($this->host, $this->port);

        if ($handle === false) {
            return false;
        }

        \fclose($handle);

        return true;
    }
}
