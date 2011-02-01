<?php

include 'organizePHP/includes/myfunctions.inc';

function getImageInfo($file) {
  $exif = exif_read_data($file, 'EXIF');
  $timestamp = 0;
  $fileSize = 0;
  $camera_make = '';
  $camera_model = '';
  if ($exif) {
    $fileSize = $exif['FileSize'];
    if (isset($exif['DateTimeOriginal'])) {
      $timestamp = $exif['DateTimeOriginal'];
      $timestamp = explode(' ', $timestamp);
      $date = explode(':', $timestamp[0]);
      $time = explode(':', $timestamp[1]);
      $timestamp = mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]);
    }
    if (isset($exif['FileDateTime'])) {
      $timestamp = $exif['FileDateTime'];
    }
    if (isset($exif['Make'])) {
      $camera_make = $exif['Make'];
    }
    if (isset($exif['Model'])) {
      $camera_model = $exif['Model'];
    }
  }
  return array(
    'timestamp' => $timestamp,
    'fileSize' => $fileSize,
    'camera_model' => $camera_model,
    'camera_make' => $camera_make
  );
}

// Get all files in the following folder
$mediafiles = get_files('images', "*", TRUE, 'on_image_found');

function on_image_found($file) {
  $extension = get_file_ext($file);
  if ($extension == 'jpg' || $extension == 'jpeg') {
    // Get the timestamp.
    $info = getImageInfo($file);

    // Get the file time.
    $fileTime = date('g.i.s A', $info['timestamp']);

    // Create the "result" directory
    $result_dir = './_RESULTS';
    if (!is_dir($result_dir)) {
      mkdir($result_dir);
    }

    // Create the year directory....
    $year_dir = date('Y', $info['timestamp']);
    $year_dir = $result_dir . '/' . $year_dir;
    if (!is_dir($year_dir)) {
      mkdir($year_dir);
    }

    // Create the month directory.
    $month_dir = date('M jS, Y', $info['timestamp']);
    $month_dir = $year_dir . '/' . $month_dir;
    if (!is_dir($month_dir)) {
      mkdir($month_dir);
    }


    // Create the Make & Model Directory
    $make = $info['camera_make'];
    $model = $info['camera_model'];
    $camera = '';
    if (isset($make) || ($model)) {
      if ($make == '') {
        $make = '(make)' ;
      }
      if ($model == '') {
        $model = '(model)';
      }
      $camera = $make . " " . $model;
    }
    $camera_dir = $camera;
    $camera_dir = $month_dir . '/' . $camera_dir;
    if (!is_dir($camera_dir)) {
      mkdir($camera_dir);
    }

    $to_file = $camera_dir . '/' . basename($file);

    // We need to loop until we find a file that has not already
    // been taken, and add a _1, _2, _3, etc until we find that file.
    $i = 1;
    $shouldCopy = TRUE;
    $original_file = $to_file;
    $ext = get_file_ext($to_file);
    while (file_exists($to_file)) {
      $to_info = getImageInfo($to_file);
      if (($to_info['timestamp'] == $info['timestamp']) ||
              ($to_info['fileSize'] == $info['fileSize'])) {
        $shouldCopy = FALSE;
        break;
      } else {
        // Add a number at the end of this file.
        $filename = basename($original_file, "." . $ext);
        $filename .= '_' . $i;
        $to_file = $model_dir . '/' . $filename . '.' . $ext;
        $i++;
      }
    }

    // Only copy if we should copy.
    if ($shouldCopy) {
      // Now copy the file.
      copy($file, $to_file);
    }
  }
}
?>
