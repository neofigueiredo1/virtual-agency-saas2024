<?php
/*
 * @package		SimpleImage class
 * @version		2.3
 * @author		Cory LaViska for A Beautiful Site, LLC. (http://www.abeautifulsite.net/)
 * @author		Nazar Mokrynskyi <nazar@mokrynskyi.com> - merging of forks, namespace support, PhpDoc editing, adaptive_resize() method, other fixes
 * @license		This software is dual-licensed under the GNU General Public License and the MIT License
 * @copyright	A Beautiful Site, LLC.
 */
// namespace	abeautifulsite;
// use			Exception;
/**
 * Class SimpleImage
 * This class makes image manipulation in PHP as simple as possible.
 * @package SimpleImage
 */
class SimpleImage {

	/**
	 * @var int Default output image quality
	 */
	public $quality	= 100;

	public $image, $filename, $original_info, $width, $height;

	/**
	 * Create instance and load an image, or create an image from scratch
	 *
	 * @param null|string	$filename	Path to image file (may be omitted to create image from scratch)
	 * @param int			$width		Image width (is used for creating image from scratch)
	 * @param int|null		$height		If omitted - assumed equal to $width (is used for creating image from scratch)
	 * @param null|string	$color		Hex color string, array(red, green, blue) or array(red, green, blue, alpha).
	 * 									Where red, green, blue - integers 0-255, alpha - integer 0-17<br>
	 * 									(is used for creating image from scratch)
	 *
	 * @return SimpleImage
	 * @throws Exception
	 */
	function __construct ($filename = null, $width = null, $height = null, $color = null) {
		if ($filename) {
			$this->load($filename);
		} elseif ($width) {
			$this->create($width, $height, $color);
		}
		return $this;
	}
	/**
	 * Destroy image resource
	 */
	function __destruct () {
		if ($this->image) {
			if(is_resource($this->image)) { 
		      imagedestroy($this->image); 
		    }
		}
	}
	/**
	 * Load an image
	 *
	 * @param string		$filename	Path to image file
	 *
	 * @return SimpleImage
	 * @throws Exception
	 */
	function load ($filename) {
		// Require GD library
		if (!extension_loaded('gd')) {
			throw new Exception('Required extension GD is not loaded.');
		}
		$this->filename	= $filename;
		$info			= getimagesize($this->filename);
		switch ($info['mime']) {
			case 'image/gif':
				$this->image = imagecreatefromgif($this->filename);
				break;
			case 'image/jpg':
			case 'image/jpeg':
				$this->image = imagecreatefromjpeg($this->filename);
				break;
			case 'image/png':
				$this->image = imagecreatefrompng($this->filename);
				break;
			case 'image/webp':
				$this->image = imagecreatefromwebp($this->filename);
				break;
			default:
				throw new Exception('Invalid image: '.$this->filename);
				break;
		}
		$this->original_info = array(
			'width'       => $info[0],
			'height'      => $info[1],
			'orientation' => $this->get_orientation(),
			'exif'        => function_exists('exif_read_data') && $info['mime'] === 'image/jpeg' ? $this->exif = @exif_read_data($this->filename) : null,
			'format'      => preg_replace('/^image\//', '', $info['mime']),
			'mime'        => $info['mime']
		);
		$this->width	= $info[0];
		$this->height	= $info[1];
		imagesavealpha($this->image, true);
		imagealphablending($this->image, true);
		return $this;
	}
	/**
	 * Create an image from scratch
	 *
	 * @param int			$width	Image width
	 * @param int|null		$height	If omitted - assumed equal to $width
	 * @param null|string	$color	Hex color string, array(red, green, blue) or array(red, green, blue, alpha).
	 * 								Where red, green, blue - integers 0-255, alpha - integer 0-127
	 *
	 * @return SimpleImage
	 */
	function create ($width, $height = null, $color = null) {
		$height					= $height ? $height : $width;
		$this->width			= $width;
		$this->height			= $height;
		$this->image			= imagecreatetruecolor($width, $height);
		$this->original_info	= array(
			'width'       => $width,
			'height'      => $height,
			'orientation' => $this->get_orientation(),
			'exif'        => null,
			'format'      => 'png',
			'mime'        => 'image/png'
		);
		if ($color) {
			$this->fill($color);
		}
		return $this;
	}
	/**
	 * Fill image with color
	 *
	 * @param string		$color	Hex color string, array(red, green, blue) or array(red, green, blue, alpha).
	 * 								Where red, green, blue - integers 0-255, alpha - integer 0-127
	 *
	 * @return SimpleImage
	 */
	function fill ($color = '#000000') {
		$rgba		= $this->normalize_color($color);
		$fill_color	= imagecolorallocatealpha($this->image, $rgba['r'], $rgba['g'], $rgba['b'], $rgba['a']);
		imagefilledrectangle($this->image, 0, 0, $this->width, $this->height, $fill_color);
		return $this;
	}
	/**
	 * Save an image
	 *
	 * The resulting format will be determined by the file extension.
	 *
	 * @param null|string	$filename	If omitted - original file will be overwritten
	 * @param null|int		$quality	Output image quality in percents 0-100
	 *
	 * @return SimpleImage
	 * @throws Exception
	 */
	function save ($filename = null, $quality = null) {
		$quality	= $quality ? $quality : $this->quality;
		$filename	= $filename ? $filename : $this->filename;
		imageinterlace($this->image, true);
		// Determine format via file extension (fall back to original format)
		$format = $this->file_ext($filename) ? $this->file_ext($filename) : $this->original_info['format'];
		// Determine output format
		switch (strtolower($format)) {
			case 'gif':
				$result = imagegif($this->image, $filename);
				break;
			case 'jpg':
			case 'jpeg':
				$result = imagejpeg($this->image, $filename, round($quality));
				break;
			case 'png':
				$result = imagepng($this->image, $filename, round(9 * $quality / 100));
				break;
			default:
				throw new Exception('Unsupported format');
		}
		if (!$result) {
			throw new Exception('Unable to save image: '.$filename);
		}
		return $this;
	}
	/**
	 * Get info about the original image
	 *
	 * @return array <pre> array(
	 * 	width		=> 320,
	 * 	height		=> 200,
	 * 	orientation	=> ['portrait', 'landscape', 'square'],
	 * 	exif		=> array(...),
	 * 	mime		=> ['image/jpeg', 'image/gif', 'image/png'],
	 * 	format		=> ['jpeg', 'gif', 'png']
	 * )</pre>
	 */
	function get_original_info () {
		return $this->original_info;
	}
	/**
	 * Get the current width
	 *
	 * @return int
	 */
	function get_width () {
		return imagesx($this->image);
	}
	/**
	 * Get the current height
	 *
	 * @return int
	 */
	function get_height () {
		return imagesy($this->image);
	}
	/**
	 * Get the current orientation
	 *
	 * @return string	portrait|landscape|square
	 */
	function get_orientation () {
		if (imagesx($this->image) > imagesy($this->image)) {
			return 'landscape';
		}
		if (imagesx($this->image) < imagesy($this->image)) {
			return 'portrait';
		}
		return 'square';
	}
	/**
	 * Flip an image horizontally or vertically
	 *
	 * @param string		$direction	x|y
	 *
	 * @return SimpleImage
	 */
	function flip ($direction) {
		$new	= imagecreatetruecolor($this->width, $this->height);
		imagealphablending($new, false);
		imagesavealpha($new, true);
		switch (strtolower($direction)) {
			case 'y':
				for ($y = 0; $y < $this->height; $y++) {
					imagecopy($new, $this->image, 0, $y, 0, $this->height - $y - 1, $this->width, 1);
				}
				break;
			default:
				for ($x = 0; $x < $this->width; $x++) {
					imagecopy($new, $this->image, $x, 0, $this->width - $x - 1, 0, 1, $this->height);
				}
				break;
		}
		$this->image = $new;
		return $this;
	}
	/**
	 * Rotate an image
	 *
	 * @param int			$angle		0-360
	 * @param string		$bg_color	Hex color string, array(red, green, blue) or array(red, green, blue, alpha).
	 * 									Where red, green, blue - integers 0-255, alpha - integer 0-127
	 *
	 * @return SimpleImage
	 */
	function rotate ($angle, $bg_color = '#000000') {
		$rgba			= $this->normalize_color($bg_color);
		$bg_color		= imagecolorallocatealpha($this->image, $rgba['r'], $rgba['g'], $rgba['b'], $rgba['a']);
		$new			= imagerotate($this->image, -($this->keep_within($angle, -360, 360)), $bg_color);
		imagesavealpha($new, true);
		imagealphablending($new, true);
		$this->width	= imagesx($new);
		$this->height	= imagesy($new);
		$this->image	= $new;
		return $this;
	}
	/**
	 * Rotates and/or flips an image automatically so the orientation will be correct (based on exif 'Orientation')
	 *
	 * @return SimpleImage
	 */
				function auto_orient () {
					// Adjust orientation
					switch ($this->original_info['exif']['Orientation']) {
						case 1:	// Do nothing
							break;
						case 2:	// Flip horizontal
							$this->flip('x');
							break;
						case 3:	// Rotate 180 counterclockwise
							$this->rotate(-180);
							break;
						case 4:	// vertical flip
							$this->flip('y');
							break;
						case 5:	// Rotate 90 clockwise and flip vertically
							$this->flip('y');
							$this->rotate(90);
							break;
						case 6:	// Rotate 90 clockwise
							$this->rotate(90);
							break;
						case 7:	// Rotate 90 clockwise and flip horizontally
							$this->flip('x');
							$this->rotate(90);
							break;
						case 8:	// Rotate 90 counterclockwise
							$this->rotate(-90);
							break;
					}
					return $this;
				}
				/**
				 * Resize an image to the specified dimensions
				 *
				 * @param int	$width
				 * @param int	$height
				 *
				 * @return SimpleImage
				 */
				function resize ($width, $height) {
					$new			= imagecreatetruecolor($width, $height);
					imagealphablending($new, false);
					imagesavealpha($new, true);
					imagecopyresampled($new, $this->image, 0, 0, 0, 0, $width, $height, $this->width, $this->height);
					$this->width	= $width;
					$this->height	= $height;
					$this->image	= $new;
					return $this;
				}
				/**
				 * Adaptive resize
				 *
				 * This function attempts to get the image to as close to the provided dimensions as possible, and then crops the
				 * remaining overflow (from the center) to get the image to be the size specified
				 *
				 * @param int			$width
				 * @param int|null		$height	If omitted - assumed equal to $width
				 *
				 * @return SimpleImage
				 */
				function adaptive_resize ($width, $height = null) {
					$height					= $height ? $height : $width;
					$current_aspect_ratio	= $this->height / $this->width;
					$new_aspect_ratio		= $height / $width;
					if ($new_aspect_ratio > $current_aspect_ratio) {
						$this->fit_to_height($height);
					} else {
						$this->fit_to_width($width);
					}
					$left					= ($this->width / 2) - ($width / 2);
					$top					= ($this->height / 2) - ($height / 2);
					return $this->crop($left, $top, $width + $left, $height + $top);
				}
				/**
				 * Fit to width (proportionally resize to specified width)
				 *
				 * @param int			$width
				 *
				 * @return SimpleImage
				 */
				function fit_to_width ($width) {
					$aspect_ratio	= $this->height / $this->width;
					$height			= $width * $aspect_ratio;
					return $this->resize($width, $height);
				}
				/**
				 * Fit to height (proportionally resize to specified height)
				 *
				 * @param int			$height
				 *
				 * @return SimpleImage
				 */
				function fit_to_height ($height) {
					$aspect_ratio	= $this->height / $this->width;
					$width			= $height / $aspect_ratio;
					return $this->resize($width, $height);
				}
				/**
				 * Best fit (proportionally resize to fit in specified width/height)
				 *
				 * Shrink the image proportionally to fit inside a $width x $height box
				 *
				 * @param int			$max_width
				 * @param int			$max_height
				 *
				 * @return	SimpleImage
				 */
				function best_fit ($max_width, $max_height) {
					// If it already fits, there's nothing to do
					if ($this->width <= $max_width && $this->height <= $max_height) {
						return $this;
					}
					// Determine aspect ratio
					$aspect_ratio	= $this->height / $this->width;
					// Make width fit into new dimensions
					if ($this->width > $max_width) {
						$width	= $max_width;
						$height	= $width * $aspect_ratio;
					} else {
						$width	= $this->width;
						$height	= $this->height;
					}
					// Make height fit into new dimensions
					if ($height > $max_height) {
						$height	= $max_height;
						$width	= $height / $aspect_ratio;
					}
					return $this->resize($width, $height);
				}
	/**
	 * Crop an image
	 *
	 * @param int			$x1	Left
	 * @param int			$y1	Top
	 * @param int			$x2	Right
	 * @param int			$y2	Bottom
	 *
	 * @return SimpleImage
	 */
	function crop ($x1, $y1, $x2, $y2) {
		// Determine crop size
		if ($x2 < $x1) {
			list($x1, $x2) = array($x2, $x1);
		}
		if ($y2 < $y1) {
			list($y1, $y2) = array($y2, $y1);
		}
		$crop_width		= $x2 - $x1;
		$crop_height	= $y2 - $y1;

		$new = imagecreatetruecolor($crop_width, $crop_height);

		imagecolortransparent($new, imagecolorallocatealpha($new, 255, 255, 255, 127));
		imagealphablending($new, false);
		imagesavealpha($new, true);

		imagecopyresampled($new, $this->image, 0, 0, $x1, $y1, $crop_width, $crop_height, $crop_width, $crop_height);

		$this->width	= $crop_width;
		$this->height	= $crop_height;
		$this->image	= $new;
		return $this;
	}
	/**
	 * Desaturate (grayscale)
	 *
	 * @return SimpleImage
	 */
	function desaturate () {
		imagefilter($this->image, IMG_FILTER_GRAYSCALE);
		return $this;
	}
	/**
	 * Invert
	 *
	 * @return SimpleImage
	 */
	function invert () {
		imagefilter($this->image, IMG_FILTER_NEGATE);
		return $this;
	}
	/**
	 * Brightness
	 *
	 * @param int			$level	Darkest = -255, lightest = 255
	 *
	 * @return SimpleImage
	 */
	function brightness ($level) {
		imagefilter($this->image, IMG_FILTER_BRIGHTNESS, $this->keep_within($level, -255, 255));
		return $this;
	}
	/**
	 * Contrast
	 *
	 * @param int			$level	Min = -100, max = 100
	 *
	 * @return SimpleImage
	 *
	 */
	function contrast ($level) {
		imagefilter($this->image, IMG_FILTER_CONTRAST, $this->keep_within($level, -100, 100));
		return $this;
	}
	/**
	 * Colorize
	 *
	 * @param string		$color		Hex color string, array(red, green, blue) or array(red, green, blue, alpha).
	 * 									Where red, green, blue - integers 0-255, alpha - integer 0-127
	 * @param float|int		$opacity	0-1
	 *
	 * @return SimpleImage
	 */
	function colorize ($color, $opacity) {
		$rgba   = $this->normalize_color($color);
		$alpha = $this->keep_within(127 - (127 * $opacity), 0, 127);
		imagefilter($this->image, IMG_FILTER_COLORIZE, $this->keep_within($rgba['r'], 0, 255), $this->keep_within($rgba['g'], 0, 255), $this->keep_within($rgba['b'], 0, 255), $alpha);
		return $this;
	}
	/**
	 * Colorize
	 *
	 * @param string		$color		Hex color string, array(red, green, blue) or array(red, green, blue, alpha).
	 * 									Where red, green, blue - integers 0-255, alpha - integer 0-127
	 * @param float|int		$opacity	0-1
	 *
	 * @return SimpleImage
	 */
	function colorTransparency($color){

		$rgba = $this->normalize_color($color);

		// Get the width and height.
		$xSize = imagesx($this->image);
		$ySize = imagesy($this->image);

		// Create a backColor background, the same size as the original.
		$new_image = imagecreatetruecolor($xSize, $ySize);
		$backColor = imagecolorallocate($new_image, $rgba['r'],$rgba['g'],$rgba['b']);
		imagefill($new_image, 0, 0, $backColor);

		$transparencyIndex = imagecolortransparent($this->image);
		$transparencyColor = array('red' => $rgba['r'], 'green' => $rgba['g'], 'blue' => $rgba['b']);
      if ($transparencyIndex >= 0) {
          $transparencyColor = imagecolorsforindex($this->image, $transparencyIndex);
      }

		$transparencyIndex    = imagecolorallocate($new_image, $transparencyColor['red'], $transparencyColor['green'], $transparencyColor['blue']);
		imagefill($new_image, 0, 0, $transparencyIndex);
		imagecolortransparent($new_image, $transparencyIndex);
		//imagecopyresampled($new_image, $image_source, 0, 0, 0, 0, $new_width, $new_height, $old_width, $old_height);

		imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $xSize, $ySize, imagesx($this->image), imagesy($this->image));


		/*
		$cBack = imagecolorallocate($this->image,$rgba['r'],$rgba['g'],$rgba['b']);
		imagecolortransparent($this->image, $cBack);*/
		return $this;
	}

	/**
	 * Edge Detect
	 *
	 * @return SimpleImage
	 */
	function edges () {
		imagefilter($this->image, IMG_FILTER_EDGEDETECT);
		return $this;
	}
	/**
	 * Emboss
	 *
	 * @return SimpleImage
	 */
	function emboss () {
		imagefilter($this->image, IMG_FILTER_EMBOSS);
		return $this;
	}

	/**
	 * Mean Remove
	 *
	 * @return SimpleImage
	 */
	function mean_remove () {
		imagefilter($this->image, IMG_FILTER_MEAN_REMOVAL);
		return $this;
	}

	/**
	 * Blur
	 *
	 * @param string		$type	selective|gaussian
	 * @param int			$passes	Number of times to apply the filter
	 *
	 * @return SimpleImage
	 */
	function blur ($type = 'selective', $passes = 1) {
		switch (strtolower($type)) {
			case 'gaussian':
				$type = IMG_FILTER_GAUSSIAN_BLUR;
				break;
			default:
				$type = IMG_FILTER_SELECTIVE_BLUR;
				break;
		}
		for ($i = 0; $i < $passes; $i++) {
			imagefilter($this->image, $type);
		}
		return $this;
	}
	/**
	 * Sketch
	 *
	 * @return SimpleImage
	 */
	function sketch () {
		imagefilter($this->image, IMG_FILTER_MEAN_REMOVAL);
		return $this;
	}
	/**
	 * Smooth
	 *
	 * @param int			$level	Min = -10, max = 10
	 *
	 * @return SimpleImage
	 */
	function smooth ($level) {
		imagefilter($this->image, IMG_FILTER_SMOOTH, $this->keep_within($level, -10, 10));
		return $this;
	}
	/**
	 * Pixelate
	 *
	 * @param int			$block_size	Size in pixels of each resulting block
	 *
	 * @return SimpleImage
	 */
	function pixelate ($block_size = 10) {
		imagefilter($this->image, IMG_FILTER_PIXELATE, $block_size, true);
		return $this;
	}
	/**
	 * Sepia
	 *
	 * @return SimpleImage
	 */
	function sepia () {
		imagefilter($this->image, IMG_FILTER_GRAYSCALE);
		imagefilter($this->image, IMG_FILTER_COLORIZE, 100, 50, 0);
		return $this;
	}

	/**
	 * multiplyColor
	 *
	 * @param array R,G,B
	 *
	 * @return SimpleImage
	 */
	function multiplyColor($color=array(255, 0, 0))
	{
   	//get opposite color
   	$opposite = array(255 - $color[0], 255 - $color[1], 255 - $color[2]);
  		 //now we subtract the opposite color from the image
   	imagefilter($this->image, IMG_FILTER_COLORIZE, -$opposite[0], -$opposite[1], -$opposite[2]);
		return $this;
	}
	/**
	 * Overlay
	 *
	 * Overlay an image on top of another, works with 24-bit PNG alpha-transparency
	 *
	 * @param string		$overlay_file
	 * @param string		$position		center|top|left|bottom|right|top left|top right|bottom left|bottom right
	 * @param float|int		$opacity		Overlay opacity 0-1
	 * @param int			$x_offset		Horizontal offset in pixels
	 * @param int			$y_offset		Vertical offset in pixels
	 *
	 * @return SimpleImage
	 */
	function overlay ($overlay_file, $position = 'center', $opacity = 1, $x_offset = 0, $y_offset = 0) {
		// Load overlay image
		$overlay	= new SimpleImage($overlay_file);
		// Convert opacity
		$opacity	= $opacity * 100;
		// Determine position
		switch (strtolower($position)) {
			case 'top left':
				$x	= 0 + $x_offset;
				$y	= 0 + $y_offset;
				break;
			case 'top right':
				$x	= $this->width - $overlay->width + $x_offset;
				$y	= 0 + $y_offset;
				break;
			case 'top':
				$x	= ($this->width / 2) - ($overlay->width / 2) + $x_offset;
				$y	= 0 + $y_offset;
				break;
			case 'bottom left':
				$x	= 0 + $x_offset;
				$y	= $this->height - $overlay->height + $y_offset;
				break;
			case 'bottom right':
				$x	= $this->width - $overlay->width + $x_offset;
				$y	= $this->height - $overlay->height + $y_offset;
				break;
			case 'bottom':
				$x	= ($this->width / 2) - ($overlay->width / 2) + $x_offset;
				$y	= $this->height - $overlay->height + $y_offset;
				break;
			case 'left':
				$x	= 0 + $x_offset;
				$y	= ($this->height / 2) - ($overlay->height / 2) + $y_offset;
				break;
			case 'right':
				$x	= $this->width - $overlay->width + $x_offset;
				$y	= ($this->height / 2) - ($overlay->height / 2) + $y_offset;
				break;
			case 'center':
			default:
				$x	= ($this->width / 2) - ($overlay->width / 2) + $x_offset;
				$y	= ($this->height / 2) - ($overlay->height / 2) + $y_offset;
				break;
		}
		$this->imagecopymerge_alpha($this->image, $overlay->image, $x, $y, 0, 0, $overlay->width, $overlay->height, $opacity);
		return $this;
	}
	/**
	 * multiply
	 *
	 * multiply an image on top of another, works with 24-bit PNG alpha-transparency
	 *
	 * @param string		$multiply_file
	 * @param string		$position		center|top|left|bottom|right|top left|top right|bottom left|bottom right
	 * @param float|int		$opacity		multiply opacity 0-1
	 * @param int			$x_offset		Horizontal offset in pixels
	 * @param int			$y_offset		Vertical offset in pixels
	 *
	 * @return SimpleImage
	 */
	function multiply($multiply_file, $position = 'center', $opacity = 1, $x_offset = 0, $y_offset = 0) {
		// Load overlay image
		$multiply	= new SimpleImage($multiply_file);
		// Convert opacity
		$opacity	= $opacity * 100;
		// Determine position
		switch (strtolower($position)) {
			case 'top left':
				$x	= 0 + $x_offset;
				$y	= 0 + $y_offset;
				break;
			case 'top right':
				$x	= $this->width - $multiply->width + $x_offset;
				$y	= 0 + $y_offset;
				break;
			case 'top':
				$x	= ($this->width / 2) - ($multiply->width / 2) + $x_offset;
				$y	= 0 + $y_offset;
				break;
			case 'bottom left':
				$x	= 0 + $x_offset;
				$y	= $this->height - $multiply->height + $y_offset;
				break;
			case 'bottom right':
				$x	= $this->width - $multiply->width + $x_offset;
				$y	= $this->height - $multiply->height + $y_offset;
				break;
			case 'bottom':
				$x	= ($this->width / 2) - ($multiply->width / 2) + $x_offset;
				$y	= $this->height - $multiply->height + $y_offset;
				break;
			case 'left':
				$x	= 0 + $x_offset;
				$y	= ($this->height / 2) - ($multiply->height / 2) + $y_offset;
				break;
			case 'right':
				$x	= $this->width - $multiply->width + $x_offset;
				$y	= ($this->height / 2) - ($multiply->height / 2) + $y_offset;
				break;
			case 'center':
			default:
				$x	= ($this->width / 2) - ($multiply->width / 2) + $x_offset;
				$y	= ($this->height / 2) - ($multiply->height / 2) + $y_offset;
				break;
		}
		$this->multiply_alpha($this->image, $multiply->image, $x, $y, 0, 0, $multiply->width, $multiply->height, $opacity);
		return $this;
	}
	/**
	 * multiply
	 *
	 * multiply an image on top of another, works with 24-bit PNG alpha-transparency
	 *
	 * @param string		$multiply_file
	 * @param string		$position		center|top|left|bottom|right|top left|top right|bottom left|bottom right
	 * @param float|int		$opacity		multiply opacity 0-1
	 * @param int			$x_offset		Horizontal offset in pixels
	 * @param int			$y_offset		Vertical offset in pixels
	 *
	 * @return SimpleImage
	 */
	function mask($mask_file, $position = 'center', $opacity = 1, $x_offset = 0, $y_offset = 0) {/*, $position = 'center', $opacity = 1, $x_offset = 0, $y_offset = 0*/
		// Load mask image
		$mask	= new SimpleImage($mask_file);
		// Convert opacity
		$opacity	= $opacity * 100;
		// Determine position
		switch (strtolower($position)) {
			case 'top left':
				$x	= 0 + $x_offset;
				$y	= 0 + $y_offset;
				break;
			case 'top right':
				$x	= $this->width - $mask->width + $x_offset;
				$y	= 0 + $y_offset;
				break;
			case 'top':
				$x	= ($this->width / 2) - ($mask->width / 2) + $x_offset;
				$y	= 0 + $y_offset;
				break;
			case 'bottom left':
				$x	= 0 + $x_offset;
				$y	= $this->height - $mask->height + $y_offset;
				break;
			case 'bottom right':
				$x	= $this->width - $mask->width + $x_offset;
				$y	= $this->height - $mask->height + $y_offset;
				break;
			case 'bottom':
				$x	= ($this->width / 2) - ($mask->width / 2) + $x_offset;
				$y	= $this->height - $mask->height + $y_offset;
				break;
			case 'left':
				$x	= 0 + $x_offset;
				$y	= ($this->height / 2) - ($mask->height / 2) + $y_offset;
				break;
			case 'right':
				$x	= $this->width - $mask->width + $x_offset;
				$y	= ($this->height / 2) - ($mask->height / 2) + $y_offset;
				break;
			case 'center':
			default:
				$x	= ($this->width / 2) - ($mask->width / 2) + $x_offset;
				$y	= ($this->height / 2) - ($mask->height / 2) + $y_offset;
				break;
		}
		$this->imagealphamask($this->image, $mask->image, $x, $y, 0, 0, $mask->width, $mask->height, $opacity);
		return $this;
	}



	function imagickDistortPerspective($img_original,$img_final,$coordinates_original,$coordinates_final) {
		
		$co = $coordinates_original;
		$cf = $coordinates_final;

		//0,0 77,299   0,750 319,561   750,750 674,346   750,0 403,158
		try {
			//Try to get ImageMagick "convert" program version number.
			exec("convert -version", $out, $rcode);
			//Print the return code: 0 if OK, nonzero if error.
			if ($rcode!=0) {
				throw new Exception("Não foi identificado o recurso: ImageMagick", 1);
			}
		} catch (Exception $e) {
			throw $e;
		}

		try {
			$arguments = "-matte -virtual-pixel transparent -distort Perspective '".escapeshellcmd($co['xya'])." ".escapeshellcmd($cf['xya'])."   ".escapeshellcmd($co['xyb'])." ".escapeshellcmd($cf['xyb'])."   ".escapeshellcmd($co['xyc'])." ".escapeshellcmd($cf['xyc'])."   ".escapeshellcmd($co['xyd'])." ".escapeshellcmd($cf['xyd'])."' ";
			$command = "convert ".$img_original." ".$arguments."  ".$img_final;
			exec($command ,$op);
		} catch (Exception $e) {
			throw $e;
		}

	}


	/**
	 * Add text to an image
	 *
	 * @param string		$text
	 * @param string		$font_file
	 * @param float|int		$font_size
	 * @param string		$color
	 * @param string		$position
	 * @param int			$x_offset
	 * @param int			$y_offset
	 *
	 * @return SimpleImage
	 * @throws Exception
	 */
	function text ($text, $font_file, $font_size = 12, $color = '#000000', $position = 'center', $x_offset = 0, $y_offset = 0) {
		// todo - this method could be improved to support the text angle
		$angle		= 0;
		$rgba		= $this->normalize_color($color);
		$color		= imagecolorallocatealpha($this->image, $rgba['r'], $rgba['g'], $rgba['b'], $rgba['a']);
		// Determine textbox size
		$box		= imagettfbbox($font_size, $angle, $font_file, $text);
		if (!$box) {
			throw new Exception('Unable to load font: '.$font_file);
		}
		$box_width	= abs($box[6] - $box[2]);
		$box_height	= abs($box[7] - $box[1]);
		// Determine position
		switch (strtolower($position)) {
			case 'top left':
				$x	= 0 + $x_offset;
				$y	= 0 + $y_offset + $box_height;
				break;
			case 'top right':
				$x	= $this->width - $box_width + $x_offset;
				$y	= 0 + $y_offset + $box_height;
				break;
			case 'top':
				$x	= ($this->width / 2) - ($box_width / 2) + $x_offset;
				$y	= 0 + $y_offset + $box_height;
				break;
			case 'bottom left':
				$x	= 0 + $x_offset;
				$y	= $this->height - $box_height + $y_offset + $box_height;
				break;
			case 'bottom right':
				$x	= $this->width - $box_width + $x_offset;
				$y	= $this->height - $box_height + $y_offset + $box_height;
				break;
			case 'bottom':
				$x	= ($this->width / 2) - ($box_width / 2) + $x_offset;
				$y	= $this->height - $box_height + $y_offset + $box_height;
				break;
			case 'left':
				$x	= 0 + $x_offset;
				$y	= ($this->height / 2) - (($box_height / 2) - $box_height) + $y_offset;
				break;
			case 'right';
				$x	= $this->width - $box_width + $x_offset;
				$y	= ($this->height / 2) - (($box_height / 2) - $box_height) + $y_offset;
				break;
			case 'center':
			default:
				$x	= ($this->width / 2) - ($box_width / 2) + $x_offset;
				$y	= ($this->height / 2) - (($box_height / 2) - $box_height) + $y_offset;
				break;
		}
		imagettftext($this->image, $font_size, $angle, $x, $y, $color, $font_file, $text);
		return $this;
	}
	/**
	 * Outputs image without saving
	 *
	 * @param null|string	$format		If omitted or null - format of original file will be used, may be gif|jpg|png
	 * @param int|null		$quality	Output image quality in percents 0-100
	 *
	 * @throws Exception
	 */
	function output ($format = null, $quality = null) {
		$quality	= $quality ? $quality : $this->quality;
		imageinterlace($this->image, true);
		switch (strtolower($format)) {
			case 'gif':
				$mimetype	= 'image/gif';
				break;
			case 'jpeg':
			case 'jpg':
				$mimetype	= 'image/jpeg';
				break;
			case 'png':
				$mimetype	= 'image/png';
				break;
			default:
				$info		= getimagesize($this->filename);
				$mimetype	= $info['mime'];
				unset($info);
				break;
		}
		// Output the image
		header('Content-Type: '.$mimetype);
		switch ($mimetype) {
			case 'image/gif':
				imagegif($this->image);
				break;
			case 'image/jpeg':
				imagejpeg($this->image, null, round($quality));
				break;
			case 'image/png':
				imagepng($this->image, null, round(9 * $quality / 100));
				break;
			default:
				throw new Exception('Unsupported image format: '.$this->filename);
				break;
		}
		// Since no more output can be sent, call the destructor to free up memory
		$this->__destruct();
	}
	/**
	 * Outputs image as data base64 to use as img src
	 *
	 * @param null|string	$format		If omitted or null - format of original file will be used, may be gif|jpg|png
	 * @param int|null		$quality	Output image quality in percents 0-100
	 *
	 * @return string
	 * @throws Exception
	 */
	function output_base64 ($format = null, $quality = null) {
		$quality	= $quality ? $quality : $this->quality;
		imageinterlace($this->image, true);
		switch (strtolower($format)) {
			case 'gif':
				$mimetype	= 'image/gif';
				break;
			case 'jpeg':
			case 'jpg':
				$mimetype	= 'image/jpeg';
				break;
			case 'png':
				$mimetype	= 'image/png';
				break;
			default:
				$info		= getimagesize($this->filename);
				$mimetype	= $info['mime'];
				unset($info);
				break;
		}
		ob_start();
		// Output the image
		switch ($mimetype) {
			case 'image/gif':
				imagegif($this->image);
				break;
			case 'image/jpeg':
				imagejpeg($this->image, null, round($quality));
				break;
			case 'image/png':
				imagepng($this->image, null, round(9 * $quality / 100));
				break;
			default:
				throw new Exception('Unsupported image format: '.$this->filename);
				break;
		}
		$image_data	= ob_get_contents();
		ob_end_clean();
		// Returns formatted string for img src
		return 'data:'.$mimetype.';base64,'.base64_encode($image_data);
	}

	//Aplica uma mascara de uma imagem em outra.
	function imagealphamask($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct) {
			// Get sizes and set up new picture
			$xSize = imagesx($dst_im);
			$ySize = imagesy($dst_im);
			/*$newPicture = imagecreatetruecolor($xSize, $ySize);
			imagesavealpha($newPicture, true);
			imagefill($newPicture, 0, 0, imagecolorallocatealpha($newPicture, 255, 255, 255, 127));*/
			//Resize mask if necessary
			if ($xSize != imagesx($src_im) || $ySize != imagesy($src_im)) {
				$tempPic = imagecreatetruecolor($xSize, $ySize);
				imagecopyresampled($tempPic, $src_im, 0, 0, 0, 0, $xSize, $ySize, imagesx($src_im), imagesy($src_im));
				imagedestroy($src_im);
				$src_im = $tempPic;
			}
			//Perform pixel-based alpha map application
			for ($x = 0; $x < $xSize; $x++) {
			   for ($y = 0; $y < $ySize; $y++) {
			       $alpha = imagecolorsforindex($src_im, imagecolorat($src_im, $x, $y));
			       $alpha = 127 - floor($alpha['red'] / 2);
			       $color = imagecolorsforindex($dst_im, imagecolorat($dst_im, $x, $y));
			       imagesetpixel($dst_im, $x, $y, imagecolorallocatealpha($dst_im, $color['red'], $color['green'], $color['blue'], $alpha));
			   }
			}
			//$dst_im = $dst_im;
	}


	/**
	 * Same as PHP's imagecopymerge() function, except preserves alpha-transparency in 24-bit PNGs
	 *
	 * @param $dst_im
	 * @param $src_im
	 * @param $dst_x
	 * @param $dst_y
	 * @param $src_x
	 * @param $src_y
	 * @param $src_w
	 * @param $src_h
	 * @param $pct
	 *
	 * @link http://www.php.net/manual/en/function.imagecopymerge.php#88456
	 */
	protected function imagecopymerge_alpha ($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct) {
		$pct		/= 100;
		// Get image width and height
		$w			= imagesx($src_im);
		$h			= imagesy($src_im);
		// Turn alpha blending off
		imagealphablending($src_im, false);
		// Find the most opaque pixel in the image (the one with the smallest alpha value)
		$minalpha	= 127;
		for ($x = 0; $x < $w; $x++) {
			for ($y = 0; $y < $h; $y++) {
				$alpha	= (imagecolorat($src_im, $x, $y) >> 24) & 0xFF;
				if ($alpha < $minalpha) {
					$minalpha	= $alpha;
				}
			}
		}
		// Loop through image pixels and modify alpha for each
		for ($x = 0; $x < $w; $x++) {
			for ($y = 0; $y < $h; $y++) {
				// Get current alpha value (represents the TANSPARENCY!)
				$colorxy		= imagecolorat($src_im, $x, $y);
				$alpha			= ($colorxy >> 24) & 0xFF;
				// Calculate new alpha
				if ($minalpha !== 127) {
					$alpha	= 127 + 127 * $pct * ($alpha - 127) / (127 - $minalpha);
				} else {
					$alpha	+= 127 * $pct;
				}
				// Get the color index with new alpha
				$alphacolorxy	= imagecolorallocatealpha($src_im, ($colorxy >> 16) & 0xFF, ($colorxy >> 8) & 0xFF, $colorxy & 0xFF, $alpha);
				// Set pixel with the new color + opacity
				if (!imagesetpixel($src_im, $x, $y, $alphacolorxy)) {
					return;
				}
			}
		}
		imagesavealpha($dst_im, true);
		imagealphablending($dst_im, true);
		imagesavealpha($src_im, true);
		imagealphablending($src_im, true);
		imagecopy($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h);
	}
		/**
	 * Same as PHP's imagecopymerge() function, except preserves alpha-transparency in 24-bit PNGs
	 *
	 * @param $dst_im
	 * @param $src_im
	 * @param $dst_x
	 * @param $dst_y
	 * @param $src_x
	 * @param $src_y
	 * @param $src_w
	 * @param $src_h
	 * @param $pct
	 *
	 * @link http://www.php.net/manual/en/function.imagecopymerge.php#88456
	 */
	protected function multiply_alpha ($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct) {
		imagealphablending($src_im, true);
		$imagex = imagesx($dst_im);
		$imagey = imagesy($dst_im);
		for ($x = 0; $x <$imagex; ++$x) {
			for ($y = 0; $y <$imagey; ++$y) {
				$rgb = imagecolorat($dst_im, $x, $y);
				$TabColors=imagecolorsforindex ( $dst_im , $rgb );

				$rgb_src = imagecolorat($src_im, $x, $y);
				$TabColorsSrc = imagecolorsforindex ( $src_im , $rgb_src );

				$color_r=floor($TabColors['red']*$TabColorsSrc['red']/255);
				$color_g=floor($TabColors['green']*$TabColorsSrc['green']/255);
				$color_b=floor($TabColors['blue']*$TabColorsSrc['blue']/255);

				$newcol = imagecolorallocate($dst_im, $color_r,$color_g,$color_b);
				imagesetpixel($dst_im, $x, $y, $newcol);
			}
		}

	}
	/**
	 * Ensures $value is always within $min and $max range.
	 *
	 * If lower, $min is returned. If higher, $max is returned.
	 *
	 * @param int|float		$value
	 * @param int|float		$min
	 * @param int|float		$max
	 *
	 * @return int|float
	 */
	protected function keep_within ($value, $min, $max) {
		if ($value < $min) {
			return $min;
		}
		if ($value > $max) {
			return $max;
		}
		return $value;
	}
	/**
	 * Returns the file extension of the specified file
	 *
	 * @param string	$filename
	 *
	 * @return string
	 */
	protected function file_ext ($filename) {
		if (!preg_match('/\./', $filename)) {
			return '';
		}
		return preg_replace('/^.*\./', '', $filename);
	}
	/**
	 * Converts a hex color value to its RGB equivalent
	 *
	 * @param string		$color	Hex color string, array(red, green, blue) or array(red, green, blue, alpha).
	 * 								Where red, green, blue - integers 0-255, alpha - integer 0-127
	 *
	 * @return array|bool
	 */
	protected function normalize_color ($color) {
		if (is_string($color)) {
			$color	= trim($color, '#');
			if (strlen($color) == 6) {
				list($r, $g, $b) = array(
					$color[0].$color[1],
					$color[2].$color[3],
					$color[4].$color[5]
				);
			} elseif (strlen($color) == 3) {
				list($r, $g, $b) = array(
					$color[0].$color[0],
					$color[1].$color[1],
					$color[2].$color[2]
				);
			} else {
				return false;
			}
			return array(
				'r'	=> hexdec($r),
				'g'	=> hexdec($g),
				'b'	=> hexdec($b),
				'a'	=> 0
			);
		} elseif (is_array($color) && (count($color) == 3 || count($color) == 4)) {
			if (isset($color['r'], $color['g'], $color['b'])) {
				return array(
					'r'	=> $this->keep_within($color['r'], 0, 255),
					'g'	=> $this->keep_within($color['g'], 0, 255),
					'b'	=> $this->keep_within($color['b'], 0, 255),
					'a'	=> $this->keep_within(isset($color['a']) ? $color['a'] : 0, 0, 127)
				);
			} elseif (isset($color[0], $color[1], $color[2])) {
				return array(
					'r'	=> $this->keep_within($color[0], 0, 255),
					'g'	=> $this->keep_within($color[1], 0, 255),
					'b'	=> $this->keep_within($color[2], 0, 255),
					'a'	=> $this->keep_within(isset($color[3]) ? $color[3] : 0, 0, 127)
				);
			}
		}
		return false;
	}


























	/**
		* Demo Function : displays the image in a 3/4 view
		* @author nchourrout
		* @version 0.1
		*/
		public function demo(){
			
			$x0 = 77;
			$y0 = 300;

			$x1 = 320;
			$y1 = 560;

			$x2 = 674;
			$y2 = 346;

			$x3 = 403;
			$y3 = 158;
			
			$this->createPerspective($x0,$y0,$x1,$y1,$x2,$y2,$x3,$y3);
		}
	/**
	* Create a perspective view of the original image as if it has been rotated in 3D
	* @author nchourrout
	* @version 0.1
	* @param long $rx Rotation angle around X axis
	* @param long $ry Rotation angle around Y axis
	* @param long $rz Rotation angle around Z axis
	*/
	public function rotate3D($rx,$ry,$rz){
		$points = $this->getApexes($rx,$ry,$rz);
		//On doit mieux gérer le fait que l'image résultat ne peut pas être agrandie sous peine d'avoir des zones blanches manquantes
		$ratio = 2;
		if ($rx!=0 || $ry!=0 || $rz!=0)
			for($i=0;$i<count($points);$i++)
				$points[$i]=array($points[$i][0]/$ratio,$points[$i][1]/$ratio);
				
				
		list($x0,$y0) = $points[1];
		list($x1,$y1) = $points[0];
		list($x2,$y2) = $points[3];
		list($x3,$y3) = $points[2];
		$this->createPerspective($x0,$y0,$x1,$y1,$x2,$y2,$x3,$y3);
	}

	private function getApexes($rx,$ry,$rz){
		$cx = cos($rx);
		$sx = sin($rx);
		$cy = cos($ry);
		$sy = sin($ry);
		$cz = cos($rz);
		$sz = sin($rz);
	  
		$ex = $this->width/2;
		$ey = $this->height/2;
		$ez = max($this->height,$this->width)/2;  
	  
		$cam = array($this->width/2,$this->height/2,max($this->height,$this->width)/2);
		$apexes = array(array(0,$this->height,0), array($this->width, $this->height, 0), array($this->width, 0, 0), array(0,0,0));
		$points = array();
		
		$i=0;
		foreach($apexes as $pt) {
			$ax = $pt[0];
			$ay = $pt[1];
			$az = $pt[2];
			
			$dx = $cy*($sz*($ax-$cam[1])+$cz*($ax-$cam[0])) - $sy*($az-$cam[2]);
			$dy = $sx*($cy*($az-$cam[2])+$sy*($sz*($ay-$cam[1])+$cz*($ax-$cam[0])))+$cx*($cz*($ay-$cam[1])-$sz*($ax-$cam[0]));
			$dz = $cx*($cy*($az-$cam[2])+$sy*($sz*($ay-$cam[1])+$cz*($ax-$cam[0])))-$sx*($cz*($ay-$cam[1])-$sz*($ax-$cam[0]));
			
			$points[$i] = array(round(($dx-$ex)/($ez/$dz)),round(($dy-$ey)/($ez/$dz)));
			$i++;
		}
		return $points;
	}
	private function createPerspective($x0,$y0,$x1,$y1,$x2,$y2,$x3,$y3){
		$SX = max($x0,$x1,$x2,$x3);
		$SY = max($y0,$y1,$y2,$y3);
		$newImage = imagecreatetruecolor($SX, $SY);
		$bg_color=ImageColorAllocateAlpha($newImage,255,255,255,0); 
		imagefill($newImage, 0, 0, $bg_color);
		for ($y = 0; $y < $this->height; $y++) {
			for ($x = 0; $x < $this->width; $x++) {
				list($dst_x,$dst_y) = $this->corPix($x0,$y0,$x1,$y1,$x2,$y2,$x3,$y3,$x,$y,$this->width,$this->height);
				imagecopy($newImage,$this->image,$dst_x,$dst_y,$x,$y,1,1);
			}
		}
		$this->image = $newImage;
	}
	private function corPix($x0,$y0,$x1,$y1,$x2,$y2,$x3,$y3,$x,$y,$SX,$SY) {
		return $this->intersectLines(
			(($SY-$y)*$x0 + ($y)*$x3)/$SY, (($SY-$y)*$y0 + $y*$y3)/$SY,
			(($SY-$y)*$x1 + ($y)*$x2)/$SY, (($SY-$y)*$y1 + $y*$y2)/$SY,
			(($SX-$x)*$x0 + ($x)*$x1)/$SX, (($SX-$x)*$y0 + $x*$y1)/$SX,
			(($SX-$x)*$x3 + ($x)*$x2)/$SX, (($SX-$x)*$y3 + $x*$y2)/$SX);
	}
	private function det($a,$b,$c,$d) {
		return $a*$d-$b*$c;
	}
	private function intersectLines($x1,$y1,$x2,$y2,$x3,$y3,$x4,$y4) {
		$d = $this->det($x1-$x2,$y1-$y2,$x3-$x4,$y3-$y4);

		if ($d==0) $d = 1;

		$px = $this->det($this->det($x1,$y1,$x2,$y2),$x1-$x2,$this->det($x3,$y3,$x4,$y4),$x3-$x4)/$d;
		$py = $this->det($this->det($x1,$y1,$x2,$y2),$y1-$y2,$this->det($x3,$y3,$x4,$y4),$y3-$y4)/$d;
		return array($px,$py);
	}













}
