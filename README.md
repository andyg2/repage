## Image repager - attempts to remove background, crops and positions centrally

## From this
<img src="https://github.com/andyg2/repage/raw/master/source/absolute-vodka.jpg?raw=true" width="400">

## To this
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

