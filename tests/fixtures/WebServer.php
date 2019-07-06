<?php

namespace Palmtree\Curl\Tests\Fixtures;

class WebServer
{
    private $host;
    private $documentRoot;
    private $pid;
    private $port;

    public function __construct($host, $documentRoot)
    {
        $this->host         = $host;
        $this->documentRoot = $documentRoot;
    }

    private function findFreePort()
    {
        $sock = \socket_create_listen(0);
        \socket_getsockname($sock, $addr, $port);
        \socket_close($sock);

        return $port;
    }

    public function start()
    {
        $this->port = $this->findFreePort();
        // Build the command
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

        // Try to connect until the time spent exceeds the timeout specified in the configuration
        while (\microtime(true) - $start <= 5) {
            if ($this->canConnect()) {
                $connected = true;
                break;
            }
        }

        if (!$connected) {
            $this->end();
            throw new \RuntimeException('Timed out');
        }

        return $this->port;
    }

    public function end()
    {
        \exec('kill ' . (int)$this->pid);
    }

    private function canConnect()
    {
        // Disable error handler for now
        \set_error_handler(function () {
            return true;
        });
        // Try to open a connection
        $sp = \fsockopen($this->host, $this->port);
        // Restore the handler
        \restore_error_handler();
        if ($sp === false) {
            return false;
        }
        \fclose($sp);

        return true;
    }
}
