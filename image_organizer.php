<?php

include 'organizePHP/includes/myfunctions.inc';
include 'organizePHP/includes/image_extensions.inc';

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
$mediafiles = get_files('media', "*", TRUE, 'on_media_found');

function on_media_found($file) {
  $extension = get_file_ext($file);
  if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'bmp') {

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
        $make = '(make)';
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

    $save_file_to = strtolower($camera_dir . '/' . basename($file));

    // We need to loop until we find a file that has not already
    // been taken, and add a _1, _2, _3, etc until we find that file.
    $i = 1;
    $shouldCopy = TRUE;
    $original_file = $save_file_to;
    $extension = get_file_ext($save_file_to);
    while (file_exists($save_file_to)) {
      $file_info = getImageInfo($save_file_to);
      if (($file_info['timestamp'] == $info['timestamp']) ||
              ($file_info['fileSize'] == $info['fileSize'])) {
        $shouldCopy = FALSE;
        break;
      } else {
        // Add a number at the end of this file.
        $filename = basename($original_file, "." . $extension);
        $filename .= '_' . $i;
        $save_file_to = $model_dir . '/' . $filename . '.' . $extension;
        $i++;
      }
    }

    // Only copy if we should copy.
    if ($shouldCopy) {
      // Now copy the file.
      copy($file, $save_file_to);
      echo 'Copying Image (from) ' . '&nbsp' . '&nbsp ' . $file;
      echo '<br />';
      echo '<br />';
      echo 'Copying Image (to)' . '&nbsp' . '&nbsp' . $save_file_to;
      echo '<br />';
      echo '<br />';
    }
  }

  // Adding Movies------------

  if ($extension == 'mov' || $extension == '3gp' || $extension == 'm4v' || $extension == 'mp4' || $extension == 'avi') {

    // Create the "result" directory
    $result_dir = './_RESULTS';
    if (!is_dir($result_dir)) {
      mkdir($result_dir);
    }

    // Create the movie directory
    $mov_dir = 'MOVIES';
    $mov_dir = $result_dir . '/' . $mov_dir;
    if (!is_dir($mov_dir)) {
      mkdir($mov_dir);
    }

    // Create the movie extension directory
    $extension_dir = $mov_dir . '/' . $extension;
    if (!is_dir($extension_dir)) {
      mkdir($extension_dir);
    }

    $save_file_to = strtolower($extension_dir . '/' . basename($file));

    // We need to loop until we find a file that has not already
    // been taken, and add a _1, _2, _3, etc until we find that file.
    $i = 1;
    $original_file = $save_file_to;
    while (file_exists($save_file_to)) {
      // Add a number at the end of this file.
      $filename = basename($original_file, "." . $extension);
      $filename .= '_' . $i;
      $save_file_to = $extension_dir . '/' . $filename . '.' . $extension;
      $i++;
    }

    // Now copy the file.
    copy($file, $save_file_to);
    echo 'Copying Movie (from)' . '&nbsp' . '&nbsp' . $file;
    echo '<br />';
    echo '<br />';
    echo 'Copying Movie (to)' . '&nbsp' . '&nbsp' . $save_file_to;
    echo '<br />';
    echo '<br />';
  }
}

echo '--------- Complete! ---------';
?>
