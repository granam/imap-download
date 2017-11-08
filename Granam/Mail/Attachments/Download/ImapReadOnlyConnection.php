<?php
declare(strict_types=1); // on PHP 7+ are standard PHP methods strict to types of given parameters

namespace Granam\Mail\Attachments\Download;

use Granam\Strict\Object\StrictObject;

class ImapReadOnlyConnection extends StrictObject
{
    /** @var string */
    private $userName;
    /** @var string */
    private $password;
    /** @var string */
    private $server;
    /** @var int */
    private $port;
    /** @var string */
    private $mailBoxName;
    /** @var array */
    private $flags;
    /** @var resource|null */
    private $resource;

    public function __construct(
        string $userName,
        string $password,
        string $server,
        int $port = 993,
        array $flags = ['imap', 'ssl'],
        string $mailBoxName = 'INBOX'
    )
    {
        $this->userName = $userName;
        $this->password = $password;
        $this->server = $server;
        $this->port = $port;
        $this->flags = $flags;
        $this->mailBoxName = $mailBoxName;
    }

    /**
     * @return null|resource
     * @throws \RuntimeException
     */
    public function getResource()
    {
        if ($this->resource === null) {
            $this->resource = @\imap_open($this->getHostName(), $this->userName, $this->password, OP_READONLY);
            if ($this->resource === false) {
                throw new \RuntimeException('Could not connect to ' . $this->getHostName() . ' because of "' . imap_last_error() . '"');
            }
        }

        return $this->resource;
    }

    private function getHostName(): string
    {
        $flags = implode('/', $this->flags);
        if ($flags !== '') {
            $flags = '/' . $flags;
        }

        return '{' . $this->server . ':' . $this->port . $flags . '}' . $this->mailBoxName;
    }

    public function closeResource()
    {
        if ($this->resource) {
            \imap_close($this->resource);
            $this->resource = null;
        }
    }

    public function __destruct()
    {
        $this->closeResource();
    }
}