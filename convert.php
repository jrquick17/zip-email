<?php
require_once __DIR__.'/imap.php';

$GLOBALS['imap'] = false;

function _main() {
    set_time_limit(300);

    $action = $_GET['action'];
    if ($action === 'getFolderOptions') {
        $response = getFolderOptions();
    } else {
        $response = fetch_emails();
    }

    echo json_encode($response);
    die();
}

function create_zip($files = [], $destination = '') {
    $destination = uniqid($destination.'-').'.zip';

    $fullPath = getcwd().'/'.$destination;

    if (count($files)) {
        $overwrite = ZIPARCHIVE::CREATE;
        if (file_exists($destination)) {
            $overwrite = ZIPARCHIVE::OVERWRITE;
        }

        $zip = new ZipArchive();

        $canZip = $zip->open(
            $fullPath,
            $overwrite
        );

        if ($canZip) {
            foreach ($files as $file) {
                $zip->addFile($file['full'], $file['name']);
            }

            $zip->close();

            return $fullPath;
        }
    }

    return false;
}

function fetch_emails() {
    $response = [];

    $imap = getImapClient();
    if ($imap) {
        $response['message'] = 'DONE!';

        $folder = getRequestVar('folder');
        if ($folder) {
            $list = $imap->getFolderNames($folder);

            if (is_array($list) && count($list) > 0) {
                $imap = $imap->getFolder($list[0]);
            } else {
                $response['errors'] = [
                    'Could not find folder.'
                ];

                $imap = false;
            }
        }

        if ($imap) {
            exec('mkdir -p emails/');

            $message_count = $imap->getCount();

            $files = [];
            for ($i = 1; $i <= $message_count; ++$i) {
                $email = $imap->getEmail($i);

                $raw_full_email = $email->getBody();

                $header = $email->getHeader();

                $fileName = to_file_name($i.'_'.$header->Subject . ".txt");

                $temp = tempnam(sys_get_temp_dir(), $fileName);

                file_put_contents($temp, $raw_full_email);

                $files[] = [
                    'full' => $temp,
                    'name' => $fileName
                ];
            }

            $imap->close();

            $zip = create_zip($files, 'zip');

            if ($zip) {
                if(file_exists($zip)){
                    //Set Headers:
                    header('Pragma: public');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($zip)) . ' GMT');
                    header('Content-Type: application/force-download');
                    header('Content-Disposition: inline; filename="'.$zip.'"');
                    header('Content-Transfer-Encoding: binary');
                    header('Content-Length: ' . filesize($zip));
                    header('Connection: close');

                    readfile($zip);

                    exec('rm '.$zip);

                    exit();
                }
            }
        } else {
            $errors = $imap->getErrors();
            if (count($errors) > 0) {
                $response['errors'] = $errors;
            }
        }
    }

    return $response;
}

function getRequestVar($name) {
    $returnVar = false;
    if (isset($_POST[$name]) && strlen($_POST[$name]) > 0) {
        $returnVar = $_POST[$name];
    } else if (isset($_GET[$name]) && strlen($_GET[$name]) > 0) {
        $returnVar = $_GET[$name];
    }

    return $returnVar;
}
function to_file_name($name) {
    return strtolower(str_replace(' ', '-', $name));
}

function getImapClient() {
    if ($GLOBALS['imap'] === false) {
        $username = getRequestVar('username');
        $password = getRequestVar('password');

        if ($username && $password) {
            $GLOBALS['imap'] = new Imap($username, $password);
        }
    }

    return $GLOBALS['imap'];
}

function getFolderOptions() {
    $response = [];

    $imap = getImapClient();
    if ($imap) {
        $folders = $imap->getFolderNames();
        if (is_null($folders)) {
            $folders = [];
        }
        $response['folders'] = $folders;
    } else {
        $response['errors'] = [
            'Could not get folder options.'
        ];
    }

    return $response;
}

_main();