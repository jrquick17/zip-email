<?php
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
    if (isset($_REQUEST['username']) && isset($_REQUEST['password'])) {
        $host = '{imap.aol.com.:993/imap/ssl/novalidate-cert}';
        if (isset($_REQUEST['folder']) && strlen($_REQUEST['folder']) > 0) {
            $host .= $_REQUEST['folder'];
        }

        $connection = imap_open($host, $_REQUEST['username'], $_REQUEST['password']);

        $message = 'DONE!';
        if ($connection) {
            exec('mkdir -p emails/');

            $message_count = imap_num_msg($connection);

            $files = [];
            for ($i = 1; $i <= 3; ++$i) {
                $raw_full_email = imap_fetchbody($connection, $i, "");

                $header = imap_headerinfo($connection, $i);

                $fileName = to_file_name($i.'_'.$header->Subject . ".txt");

                $temp = tempnam(sys_get_temp_dir(), $fileName);

                file_put_contents($temp, $raw_full_email);

                $files[] = [
                    'full' => $temp,
                    'name' => $fileName
                ];
            }

            imap_close($connection);

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
            $message = json_encode(imap_errors());
        }
    }

    header("Location: index.php?message=".$message);
    die();
}

function to_file_name($name) {
    return strtolower(str_replace(' ', '-', $name));
}

fetch_emails();