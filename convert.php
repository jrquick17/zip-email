<?php
require_once __DIR__.'/imap.php';

$GLOBALS['imap'] = false;

function _main() {
    set_time_limit(300);

    $action = $_GET['action'];
    if ($action === 'getFolderOptions') {
        $message = getFolderOptions();
    } else {
        $message = fetch_emails();
    }

    header("Location: index.php?message=".$message);
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
    $message = '';

    $imap = getImapClient();
    if ($imap) {
        $message = 'DONE!';

        if (isset($_POST['folder']) && strlen($_POST['folder']) > 0) {
            $list = $imap->getFolderNames($_POST['folder']);

            if (is_array($list) && count($list) > 0) {
                $imap = $imap->getFolder($list[0]);
            } else {
                $message = 'COULD NOT FIND FOLDER.';
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
                $message = json_encode($errors);
            }
        }
    }

    return $message;
}

function to_file_name($name) {
    return strtolower(str_replace(' ', '-', $name));
}

function getImapClient() {
    if ($GLOBALS['imap'] === false) {
        if (isset($_POST['username']) && isset($_POST['password'])) {
            $GLOBALS['imap'] = new Imap($_POST['username'], $_POST['password']);
        }
    }

    return $GLOBALS['imap'];
}

function getFolderOptions() {
    $message = '';
    $imap = getImapClient();
    if ($imap) {
        $folders = $imap->getFolderNames();
    } else {
        $message = 'Could not get folder options';
    }

    return $message;
}

_main();