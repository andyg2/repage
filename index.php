<?php


// ======DIRECTORIES======
$p['source'] = './source/'; // path of source images
$p['dest'] = './dest/'; // path of destination images


// ======FILE NAMING======

$p['autoname'] = false; // adds dimentions to file and, if destination exists increments filename is_integer


// ======DESTINATION_DIMENSIONS====== defaults to 800 x 600 if omitted
$p['destination_width'] = 1200;
$p['destination_height'] = 900;


// ======BACKGROUND_COLOR====== defaults to white if omitted 
// Specify rgb values
// $p['background_color']['r'] = 127;
// $p['background_color']['g'] = 127;
// $p['background_color']['b'] = 127;

// Use hex value
// $p['background_color'] = '#CCCCCC';

// Detect background colors - average color of 10 pixels in from each corder
$p['background_color'] = 10; // (auto)

// ======IMAGE_PADDING======
$p['image_padding'] = 20; // add 20 pixels additional border to crop

// ======CROPPING_THRESHOLD======
$p['cropping_threshold'] = 'auto'; // or 'auto'


batch_repage($p);

function batch_repage($p) {

  $p['autoname'] = isset($p['autoname']) ? boolval($p['autoname']) : false; // adds dimentions to file and if exists increments filename integer
  $p['destination_width'] = isset($p['destination_width']) ? intval($p['destination_width']) : 800;
  $p['destination_height'] = isset($p['destination_height']) ? intval($p['destination_height']) : 600;
  $p['image_padding'] = isset($p['image_padding']) ? intval($p['image_padding']) : 40;


  if (isset($p['cropping_threshold'])) {
    if ($p['cropping_threshold'] != 'auto') {
      $p['cropping_threshold'] = max(min(intval($p['cropping_threshold']), 100), 0); // between 0 and 100
    }
  } else {
    $p['cropping_threshold'] = false;
  }

  $source = isset($p['source']) ? $p['source'] : null;
  $dest = isset($p['dest']) ? $p['dest'] : null;

  if (!$source || !$dest) {
    return false;
  }

  if (!is_dir($dest)) {
    mkdir($dest, 0755, true);
  }
  if (!is_dir($source)) {
    mkdir($source, 0755, true);
    echo 'No files in source';
    exit;
  }

  $glob = $source . "*.{png,jpeg,jpg,gif}";
  $files = glob($glob, GLOB_BRACE);

  if (!empty($files)) {
    foreach ($files as $file) {
      $p['input_image'] = $file;

      $pathinfo = pathinfo($file);
      $p['output_image'] = $dest . $pathinfo['basename'];
      pre($p);
      repage_image($p);
    }
  }
}

function repage_image($p) {

  $background = ['r' => 255, 'g' => 255, 'b' => 255];

  if (isset($p['background_color'])) {
    if (is_integer($p['background_color'])) {
      $background = corner_color($p['input_image'], $p['background_color']);
    } elseif (strpos($p['background_color'], '#') === 0) {
      $background = hex2RGB($p['background_color']);
    } elseif (isset($p['background_color']['r']) && isset($p['background_color']['g']) && isset($p['background_color']['b'])) {
      $background = $p['background_color'];
    }
  }


  foreach ($background as $k => $v) {
    if ($v > 250) {
      $v = 255;
    }
    $background[$k] = max(min($v, 255), 0);
  }
  // prex($background);
  $p['input_image_ext'] = (strtolower(pathinfo($p['input_image'], PATHINFO_EXTENSION)));
  $p['output_image_ext'] = (strtolower(pathinfo($p['output_image'], PATHINFO_EXTENSION)));


  // Autpo crop edges off image
  $orig_image = imagecreatefromfile($p['input_image']);


  // crop threshold
  if (isset($p['cropping_threshold'])) {
    if (is_integer($p['cropping_threshold'])) {
      $orig_image = imagecropauto($orig_image, IMG_CROP_THRESHOLD, $p['cropping_threshold'] / 100, array_product($background));
    } elseif ($p['cropping_threshold'] == 'auto') {
      // $colorIndex = array_product(array_filter($background)); // multiple non zeros
      $orig_image = imagecropauto($orig_image, IMG_CROP_SIDES);
    }
  }

  $width = imagesx($orig_image);
  $height = imagesy($orig_image);
  // pre($background);
  if (isset($p['image_padding'])) {
    $img_adj_width = $width + (2 * $p['image_padding']);
    $img_adj_height = $height + (2 * $p['image_padding']);
    $newimage = imagecreatetruecolor($img_adj_width, $img_adj_height);
    $border_color = imagecolorallocate($newimage, $background['r'], $background['g'], $background['b']);
    imagesavealpha($newimage, true);

    imagecopyresampled($newimage, $orig_image, $p['image_padding'], $p['image_padding'], 0, 0, $img_adj_width, $img_adj_height, $img_adj_width, $img_adj_height);
    imagefill($newimage, 1, 1, $border_color);
    $orig_image = $newimage;

    $width = $img_adj_width;
    $height = $img_adj_height;
  }


  $scale_1 = $p['destination_width'] / max($width, $height);
  $scale_2 = $p['destination_height'] / max($width, $height);
  $scale = min($scale_1, $scale_2);
  $new_width = floor($width * $scale);
  $new_height = floor($height * $scale);


  $save_image = imagecreatetruecolor($p['destination_width'], $p['destination_height']);

  // fill background with colors from corner of source image.
  $fill = imagecolorallocate($save_image, $background['r'], $background['g'], $background['b']);
  imagefill($save_image, 0, 0, $fill);

  if ($p['destination_height'] > $new_height) {
    $offset_top = ($p['destination_height'] - $new_height) / 2;
  } else {
    $offset_top = 0;
  }

  if ($p['destination_width'] > $new_height) {
    $offset_left = ($p['destination_width'] - $new_width) / 2;
  } else {
    $offset_left = 0;
  }

  if (isset($p['autoname']) && $p['autoname']) {
    $info = pathinfo($p['output_image']);
    $filename = $info['filename'];
    $path = $info['dirname'] . '/' . $filename . '-' . $p['destination_width'] . 'x' . $p['destination_height'] . '.' . $info['extension'];
    $ct = 0;
    while (file_exists($path)) {
      $ct++;
      $filename = $info['filename'] . '-' . $ct;
      $path = $info['dirname'] . '/' . $filename . '-' . $p['destination_width'] . 'x' . $p['destination_height'] . '.' . $info['extension'];
    }
    $p['output_image'] = $path;
  }


  imagecopyresampled($save_image, $orig_image, $offset_left, $offset_top, 0, 0, $new_width, $new_height, $width, $height);
  switch ($p['input_image_ext']) {
    case 'jpeg':
    case 'jpg':
      imagejpeg($save_image, $p['output_image']);
      break;
    case 'png':
      imagepng($save_image, $p['output_image']);
      break;
    case 'gif':
      imagegif($save_image, $p['output_image']);
      break;
  }
}

function imagecreatefromfile($filename) {
  switch (strtolower(pathinfo($filename, PATHINFO_EXTENSION))) {
    case 'jpeg':
    case 'jpg':
      $r = imagecreatefromjpeg($filename);
      break;

    case 'png':
      $r = imagecreatefrompng($filename);
      break;

    case 'gif':
      $r = imagecreatefromgif($filename);
      break;

    default:
      $r = false;
      break;
  }
  return ($r);
}


function corner_color($path, $inset = 0) {
  $r = [];
  // create an image resource
  $orig_image = imagecreatefromfile($path);

  // get it's width and height
  list($width, $height) = getimagesize($path);

  // create as many sample locations as required - example uses the 4 corners with optional inset
  $sample_locations = [[$inset, $inset], [$width - ($inset + 1), $inset], [$inset, $height - ($inset + 1)], [$width - ($inset + 1), $height - ($inset + 1)]];
  $sample_count = count($sample_locations);
  // pre($sample_locations);

  $r['b'] = $r['g'] = $r['r'] = 0;
  foreach ($sample_locations as $sample_location) {
    $sample_color = imagecolorat($orig_image, $sample_location[0], $sample_location[1]);
    $r['r'] += $sample_color >> 16;
    $r['g'] += $sample_color >> 8 & 255;
    $r['b'] += $sample_color & 255;
  }

  foreach ($r as $primary => $val) {
    $r[$primary] = $val / $sample_count;
  }
  // pre($r);
  return ($r);
}


function hex2RGB($hexStr, $default = []) {
  $hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr);
  if (strlen($hexStr) == 6) {
    $colorVal = hexdec($hexStr);
    $rgbArray['r'] = 0xFF & ($colorVal >> 0x10);
    $rgbArray['g'] = 0xFF & ($colorVal >> 0x8);
    $rgbArray['b'] = 0xFF & $colorVal;
  } elseif (strlen($hexStr) == 3) {
    $rgbArray['r'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
    $rgbArray['g'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
    $rgbArray['b'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
  } else {
    return $default;
  }
  return $rgbArray; // returns the rgb string or the associative array
}



function pre($a, $h = false) {
  echo $h ? '<h3 style="margin-bottom: 0px;">' . $h . '</h3><pre style="margin-top: 0px;">' : '<pre style="margin-top: 0px;">';
  print_r($a);
  echo '</pre>';
}

function prex($a, $h = false, $dbg = false) {
  pre($a, $h);
  if ($dbg) {
    echo '<pre>';
    print_r(debug_backtrace());
    echo '</pre>';
  }
  exit;
}
