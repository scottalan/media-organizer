<?php
include('includes/myfunctions.inc');

$handle = fopen('../fdupes.txt', 'r');
if( $handle ) {
  while( ($buffer = fgets($handle, 256)) !== false) {
    echo $buffer;
    $file = trim($buffer);
  }
  if( !feof($handle)) {
    echo "Error: Unexpected fgets() fail\n";
  }
  fclose($handle);
}

if( $file ) {
	// Check to make sure we are dealing with an image.
	$ext = get_file_ext($file);
	if( $ext == 'jpg' || $ext == 'jpeg' ) {
		$exif = exif_read_data($file, 'IFD0');
		
		//echo "<pre>" . print_r($exif, TRUE) . "</pre>";
		
		if( $exif && isset($exif['DateTimeOriginal']) && isset($exif['Make']) && isset($exif['Model'])) {
			$date = $exif['DateTimeOriginal'];
			$make = $exif['Make'];
			$make = $exif['Model'];
			echo $date;
			echo "<br />";
			echo $make;
			echo "<br />";
			echo $model;
		}
	}
}

$files = get_files('../2007/Jan 31st, 2007', "*");
$result = print_r($files);
echo $result;
echo "<br />";

?>
