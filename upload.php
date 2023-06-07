<?php

if ( !isset($_FILES['fupload']) ) {
    echo 'UPLOAD MINIMAL 1 FILE';
    exit;
}

$errorArr = [
    0 => 'There is no error, the file uploaded with success',
    1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
    2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
    3 => 'The uploaded file was only partially uploaded',
    4 => 'No file was uploaded',
    6 => 'Missing a temporary folder',
    7 => 'Failed to write file to disk.',
    8 => 'A PHP extension stopped the file upload.'
];

if ( is_array($_FILES['fupload']['name']) ) {
    $findex = 1;
    foreach ( $_FILES['fupload']['name'] as $fkey => $file_name ) {
        $file_type    = $_FILES['fupload']['type'][$fkey];
        $file_tmpname = $_FILES['fupload']['tmp_name'][$fkey];
        $file_error   = $_FILES['fupload']['error'][$fkey];
        $file_size    = $_FILES['fupload']['size'][$fkey];
        try {
            $upload = upload($file_name, $file_type, $file_tmpname, $file_error, $file_size);
            echo "<a href=\"files/$upload\">File ke $findex <b>$file_name</b> Berhasil di Upload</a><br>";
        } catch (Exception $e) {
            echo "File ke $findex <b>$file_name</b> Gagal di Upload <b>{$e->getMessage()}</b><br>";
        }
        $findex++;
    }
    echo '<br>';
} else {
    $file_name    = $_FILES['fupload']['name'];
    $file_type    = $_FILES['fupload']['type'];
    $file_tmpname = $_FILES['fupload']['tmp_name'];
    $file_error   = $_FILES['fupload']['error'];
    $file_size    = $_FILES['fupload']['size'];
    try {
        $upload = upload($file_name, $file_type, $file_tmpname, $file_error, $file_size);
        echo "<a href=\"files/$upload\">File $file_name Berhasil di Upload</a><br>";
    } catch (Exception $e) {
        echo "File <b>$file_name</b> Gagal di Upload <b>{$e->getMessage()}</b><br>";
    }
}

function str_lreplace($search, $replace, $subject) {
    $pos = strrpos($subject, $search);
    if ($pos !== false) {
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }
    return $subject;
}

function upload($name, $type, $tmpname, $error, $size) {
    global $errorArr;
    if ( $error !== 0 ) {
        throw new Exception($errorArr[$error]);
    }
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $randChar = '';
    for ( $i = 0; $i < 10; $i++ ) {
        $randChar .= substr($chars, rand(0, strlen($chars)), 1);
    }
    $name = basename($name);
    $ext = pathinfo($name, PATHINFO_EXTENSION);
    $name = str_lreplace(".$ext", "-$randChar.$ext", $name);
    $target_file = __DIR__ . '/files/' . $name;
    if ( !move_uploaded_file($tmpname, $target_file) ) throw new Exception('Sorry, there was an error uploading your file.');
    return $name;
}
?>
<br>
<a href="files/">List File Upload</a><br><br>
<a href="index.html">Upload lagi</a>