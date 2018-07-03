<?php
function create_zip($files = array(),$destination = '',$overwrite = false) {
    //if the zip file already exists and overwrite is false, return false
    if(file_exists($destination) && !$overwrite) { return false; }
    //vars
    $valid_files = array();
    //if files were passed in...
    if(is_array($files)) {
        //cycle through each file
        foreach($files as $file) {
            //make sure the file exists
            if(file_exists($file)) {
                $valid_files[] = $file;
            }
        }
    }
    //if we have good files...
    if(count($valid_files)) {
        //create the archive
        $zip = new ZipArchive();
        if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
            return false;
        }
        //add the files
        foreach($valid_files as $file) {
            $zip->addFile($file,$file);
        }
        //debug
        //echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;

        //close the zip -- done!
        $zip->close();

        //check to make sure the file exists
        return file_exists($destination);
    }
    else
    {
        return false;
    }
}

$host = '{imap.aol.com.:993/imap/ssl/novalidate-cert}';

$connection = imap_open($host, 'jrquick628@aol.com', '512mbddr2');

if ($connection) {
    exec('mkdir -p emails/');

    $message_count = imap_num_msg($connection);

    $files = [];
    for ($i = 1; $i <= $message_count; ++$i) {
        $raw_full_email = imap_fetchbody($connection, $i, "");

        $file = fopen('emails/'.$i.".txt", "wb");

        fwrite($file, $raw_full_email);

        fclose($file);

        $percent = number_format($i / $message_count, 3) * 100;

        echo $percent.'% completed.'."\r\n";
    }

    imap_close($connection);

    create_zip($files, 'emails');
} else {
    echo json_encode(imap_errors());
}