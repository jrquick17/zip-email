<?php
abstract class EmailHosts {
    const HOST_LIST = [
        'aol' => [
            'display' => 'AOL',
            'host'    => '{imap.aol.com.:993/imap/ssl/novalidate-cert}'
        ],
        'gmail' => [
            'display' => 'Gmail',
            'host'    => '{imap.gmail.com.:993/imap/ssl/novalidate-cert}'
        ],
        'outlook' => [
            'display' => 'Outlook',
            'host'    => '{imap-mail.outlook.com.:993/imap/tls/novalidate-cert}'
        ],
        'yahoo' => [
            'display' => 'Yahoo',
            'host'    => '{imap.mail.yahoo.com.:993/imap/ssl/novalidate-cert}'
        ]
    ];

    public static function getImapHosts() {
        $hosts = [];

        foreach (EmailHosts::HOST_LIST as $hostData) {
            $hosts[] = $hostData['display'];
        }

        return $hosts;
    }
}