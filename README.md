### Image repager - attempts to remove background, crops and positions centrally

#### From this

<img src="https://github.com/andyg2/repage/raw/master/source/absolute-vodka.jpg?raw=true" width="400">

#### To this

<img src="https://github.com/andyg2/repage/raw/master/dest/absolute-vodka.jpg?raw=true" width="400">

### DIRECTORIES

```php
$p['source'] = './source/'; // path of source images with trailing slash
$p['dest'] = './dest/'; // path of destination images with trailing slash
```

### FILE NAMING

#### Adds dimentions to file [basename]-1200x900.jpg and, if destination exists increments filename is_integer [basename]-1200x900-2.jpg

```php
$p['autoname'] = false;
```

### DESTINATION_DIMENSIONS

#### defaults to 800 x 600 if omitted

```php
$p['destination_width'] = 1200;
$p['destination_height'] = 900;
```

### BACKGROUND_COLOR - defaults to white if omitted

#### Specify rgb values

```php
$p['background_color']['r'] = 127;
$p['background_color']['g'] = 127;
$p['background_color']['b'] = 127;
```

#### Or: Use hex value

```php
$p['background_color'] = '#CCCCCC';
```

#### Or: Detect background colors - average color of 10 pixels in from each corder

```php
$p['background_color'] = 10; // (auto)
```

### IMAGE_PADDING

#### Add 20 pixels additional border to crop

```php
$p['image_padding'] = 20;
```

### CROPPING_THRESHOLD

#### Automatic

```php
$p['cropping_threshold'] = 'auto';
```

#### Or Specify a threshold

```php
$p['cropping_threshold'] = 50;
```

## Examples

### Batch images

#### args for batch_repage($p)

##### Process all images in ./source/*.{png,jpeg,jpg,gif}, save as original filenames in ./dest/* as a 1200 x 900 image, get background color from 10px in from the corners, add additional 20px padding to cropped image.

```php
$p['source'] = './source/';
$p['dest'] = './dest/';
$p['autoname'] = false;
$p['destination_width'] = 1200;
$p['destination_height'] = 900;
$p['background_color'] = 10;
$p['image_padding'] = 20;
$p['cropping_threshold'] = 'auto';
```

### Single image

#### args for repage_image($p)

##### Process './source/jack-daniels.jpeg', save as './dest/jack-daniels-400x300.jpeg' (autoname) as a 400 x 300 image, white background color for fropping and fill, add 100px padding around cropped image.

```php
$p['input_image'] = './source/jack-daniels.jpeg';
$p['output_image'] = './dest/jack-daniels.jpeg';
$p['autoname'] = true;
$p['destination_width'] = 400;
$p['destination_height'] = 300;
$p['background_color']['r'] = 255;
$p['background_color']['g'] = 255;
$p['background_color']['b'] = 255;
$p['image_padding'] = 100;
$p['cropping_threshold'] = 'auto';
```

#### Source
<img src="https://github.com/andyg2/repage/blob/master/source/jack-daniels.jpeg?raw=true" width="400">

#### Result
<img src="https://github.com/andyg2/repage/blob/master/dest/jack-daniels-400x300.jpeg?raw=true" width="400">
