<?php
require_once __DIR__.'/email-hosts.php';
require_once __DIR__.'/imap-email.php';

class Imap {
    private $connection = false;

    private $folders = [];

    private $host = false;
    private $username = false;
    private $password = false;

    public function __construct($host = EmailHosts::AOL, $username, $password) {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;

        $this->connection = $this->getConnection();
    }

    private function _get($a, $b = false) {
        return $a ? $a : $b;
    }

    private function _getConnection($connection = false) {
        return $this->_get($connection, $this->connection);
    }

    private function _getHost($host) {
        return $this->_get($host, $this->connection);
    }

    public function close($connection = false) {
        $connection = $this->_getConnection($connection);
        if ($connection) {
            /** @var resource $connection */
            imap_close($connection);
        } else {
            return false;
        }
    }

    public function getConnection($host = false, $username = false, $password = false) {
        if ($host === false) {
            $host = $this->host;
        }

        if ($username === false) {
            $username = $this->username;
        }

        if ($password === false) {
            $password = $this->password;
        }

        return imap_open($host, $username, $password);
    }

    public function getCount($connection = false) {
        $connection = $this->_getConnection($connection);

        if ($connection) {
            /** @var resource $connection */
            return imap_num_msg($connection);
        }

        return false;
    }

    public function getEmail($i) {
        return new ImapEmail($this, $i);
    }

    public function getErrors() {
        return imap_errors();
    }

    public function getFolder($name = false) {
        if (!isset($folders[$name])) {
            $this->folders[$name] = $this->getConnection($name);
        }

        return $this->folders[$name];
    }

    public function getFolderNames($pattern = '*', $host = false) {
        return imap_list($this->connection, $this->_getHost($host), $pattern);
    }
}