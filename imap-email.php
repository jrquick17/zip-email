<?php
class ImapEmail {
    private $imap;

    private $i;

    /**
     * ImapEmail constructor.
     *
     * @param Imap $imap
     * @param int  $i
     */
    function __construct($imap, $i = 0)  {
        $this->imap = $imap;

        $this->i = $i;
    }

    function getBody($section = '') {
        return imap_fetchbody($this->getConnection(), $this->i, $section);
    }

    function getConnection() {
        return $this->imap->getConnection();
    }

    function getHeader() {
        return imap_headerinfo($this->getConnection(), $this->i);
    }
}