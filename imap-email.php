<?php
class ImapEmail {
    private $connection;

    private $i;

    function __construct($connection, $i = 0)  {
        $this->connection = $connection;

        $this->i = $i;
    }

    function getBody($section = '') {
        return imap_fetchbody($this->connection, $this->i, $section);
    }

    function getHeader() {
        return imap_headerinfo($this->connection, $this->i);
    }
}