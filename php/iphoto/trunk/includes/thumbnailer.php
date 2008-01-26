<?php

// -----------------------------------------------------------------------
//
// Description:
//   tnimg.lib.php - ver. 1.0.1
//   The PHP and GD based class for dealing with thumnail images.
//
//   IMPORTANT !!!
//   Do not write any spaces or other characters before starting
//   php tag, as  the methods of this class may send headers.
//
// Author:
//   Vagharshak Tozalakyan <vagh@armdex.com>,
//   Copyright (c)2005.
//   This module was written by author on its leasure time.
//
//   http://vagh.armdex.com/tnimg
//
// License:
//   This code is released under the GNU/LGPL license. It is free as stated in
//   the the README file which should have been included with source code.
//
// Warning:
//   This library is non commercial, non professional work. It should not have
//   unexpected results. However, if any damage is caused by this software the
//   author can not be responsible. The use of this software is at the risk of
//   the user.
//
// Requirements:
//   PHP >= 4.0.6, GD2
//
// -----------------------------------------------------------------------


// ****************************************************************************
// CONSTANTS
// ****************************************************************************

// Maximum size of images in bytes, used only when loading images by url in
// early versions of PHP ( < 4.3.x )
define ( 'MAX_IMG_SIZE', 100000 );

// Supported image types
define ( 'THUMB_JPEG', 'image/jpeg' );
define ( 'THUMB_PNG', 'image/png' );
define ( 'THUMB_GIF', 'image/gif' );

// Interlacing modes
define ( 'INTERLACE_OFF', 0 );
define ( 'INTERLACE_ON', 1 );

// Output modes
define ( 'STDOUT', '' );

// Empty constants
define ( 'NO_LOGO', '' );
define ( 'NO_LABEL', '' );

// Logo and label positioning
define ( 'POS_LEFT', 0 );
define ( 'POS_RIGHT', 1 );
define ( 'POS_CENTER', 2 );
define ( 'POS_TOP', 3 );
define ( 'POS_BOTTOM', 4 );

// Error messages
define ( 'E_001', 'File <b>%s</b> do not exist' );
define ( 'E_002', 'Failed reading image data from <b>%s</b>' );
define ( 'E_003', 'Cannot create the copy of <b>%s</b>' );
define ( 'E_004', 'Cannot copy the logo image' );
define ( 'E_005', 'Cannot create final image' );


// ****************************************************************************
// CLASS DEFINITION
// ****************************************************************************

class ThumbnailImage
{


// ****************************************************************************
// PUBLIC PROPERTIES
// ****************************************************************************

  var $src_file;  // source image file
  var $dest_file; // destination image file
  var $dest_type;  // destination image type

  var $interlace; // destination image interlacing mode
  var $jpeg_quality; // quality of resulting JPEG

  var $max_width; // maximal thumbnail width
  var $max_height; // maximal thumbnail height
  var $fit_to_max; // enlarge small images?

  var $logo; // array of logo parameters
  var $label; // array of label parameters


// ****************************************************************************
// CLASS CONSTRUCTOR
// ****************************************************************************

  /*
    Description:
      Defines default values for properties.
    Prototype:
      void ThumbImg ( string src_file = '' )
    Parameters:
      src_file - source image filename
  */
  function ThumbnailImage ( $src_file = '' )
  {

    $this->src_file = $src_file;
    $this->dest_file = STDOUT;
    $this->dest_type = THUMB_JPEG;

    $this->interlace = INTERLACE_OFF;
    $this->jpeg_quality = -1;

    $this->max_width = 100;
    $this->max_height = 90;
    $this->fit_to_max = FALSE;

    $this->logo['file'] = NO_LOGO;
    $this->logo['vert_pos'] = POS_TOP;
    $this->logo['horz_pos'] = POS_LEFT;

    $this->label['text'] = NO_LABEL;
    $this->label['vert_pos'] = POS_BOTTOM;
    $this->label['horz_pos'] = POS_RIGHT;
    $this->label['font'] = '';
    $this->label['size'] = 20;
    $this->label['color'] = '#000000';
    $this->label['angle'] = 0;

  }


// ****************************************************************************
// PRIVATE METHODS
// ****************************************************************************

  /*
    Description:
      Extracts decimal color components from hex color string.
    Prototype:
      array ParseColor ( string hex_color )
    Parameters:
      hex_color - color in '#rrggbb' format
    Return:
      Decimal values for red, green and blue color components.
  */
  function ParseColor ( $hex_color )
  {

    if ( strpos ( $hex_color, '#' ) === 0 )
      $hex_color = substr ( $hex_color, 1 );

    $r = hexdec ( substr ( $hex_color, 0, 2 ) );
    $g = hexdec ( substr ( $hex_color, 2, 2 ) );
    $b = hexdec ( substr ( $hex_color, 4, 2 ) );

    return array ( $r, $g, $b );

  }


  /*
    Description:
      Retrives image data as a string.
      Thanks to Luis Larrateguy for the idea of this function.
    Prototype:
      string GetImageStr ( string image_file )
    Parameters:
      image_file - filename of image
    Return:
      Image file contents string.
  */
  function GetImageStr ( $image_file )
  {

    if ( function_exists ( 'file_get_contents' ) )
    {
      $str = @file_get_contents ( $image_file );
      if ( ! $str )
      {
        $err = sprintf( E_002, $image_file );
        trigger_error( $err, E_USER_ERROR );
      }
      return $str;
    }

    $f = fopen ( $image_file, 'rb' );
    if ( ! $f )
    {
      $err = sprintf( E_002, $image_file );
      trigger_error( $err, E_USER_ERROR );
    }
    $fsz = @filesize ( $image_file );
    if ( ! $fsz )
      $fsz = MAX_IMG_SIZE;
    $str = fread ( $f, $fsz );
    fclose ( $f );

    return $str;

  }


  /*
    Description:
      Loads image from file.
    Prototype:
      resource LoadImage ( string image_file, int &image_width, int &image_height )
    Parameters:
      image_file - filename of image
      image_width - width of loaded image
      image_height - height of loaded image
    Return:
      Image identifier representing the image obtained from the given file.
  */
  function LoadImage ( $image_file, &$image_width, &$image_height )
  {

    $image_width = 0;
    $image_height = 0;
print $image_file;
    $image_data = $this->GetImageStr( $image_file );

    $image = imagecreatefromstring ( $image_data );
    if ( ! $image )
    {
      $err = sprintf( E_003, $image_file );
      trigger_error( $err, E_USER_ERROR );
    }

    $image_width = imagesx ( $image );
    $image_height = imagesy ( $image );

    return $image;

  }


  /*
    Description:
      Calculates thumbnail image sizes from source image width and height.
    Prototype:
      array GetThumbSize ( int src_width, int src_height )
    Parameters:
      src_width - width of source image
      src_height - height of source image
    Return:
      An array with 2 elements. Index 0 contains the width of thumbnail image
      and index 1 contains the height.
  */
  function GetThumbSize ( $src_width, $src_height )
  {

    $max_width = $this->max_width;
    $max_height = $this->max_height;

    $x_ratio = $max_width / $src_width;
    $y_ratio = $max_height / $src_height;

    $is_small = ( $src_width <= $max_width && $src_height <= $max_height );

    if ( ! $this->fit_to_max && $is_small )
    {
      $dest_width = $src_width;
      $dest_height = $src_height;
    }
    elseif ( $x_ratio * $src_height < $max_height )
    {
      $dest_width = $max_width;
      $dest_height = ceil ( $x_ratio * $src_height );
    }
    else
    {
      $dest_width = ceil ( $y_ratio * $src_width );
      $dest_height = $max_height;
    }

    return array ( $dest_width, $dest_height );

  }


  /*
    Description:
      Adds logo image to thumbnail.
    Prototype:
      void AddLogo ( int thumb_width, int thumb_height, resource &thumb_img )
    Parameters:
      thumb_width - width of thumbnail image
      thumb_height - height of thumbnail image
      thumb_img - thumbnail image identifier
  */
  function AddLogo ( $thumb_width, $thumb_height, &$thumb_img )
  {

    extract ( $this->logo );

    $logo_image = $this->LoadImage ( $file, $logo_width, $logo_height );

    if ( $vert_pos == POS_CENTER )
      $y_pos = ceil ( $thumb_height / 2 - $logo_height / 2 );
    elseif ($vert_pos == POS_BOTTOM)
      $y_pos = $thumb_height - $logo_height;
    else
      $y_pos = 0;

    if ( $horz_pos == POS_CENTER )
      $x_pos = ceil ( $thumb_width / 2 - $logo_width / 2 );
    elseif ( $horz_pos == POS_RIGHT )
      $x_pos = $thumb_width - $logo_width;
    else
      $x_pos = 0;

    if ( ! imagecopy ( $thumb_img, $logo_image, $x_pos, $y_pos, 0, 0,
      $logo_width, $logo_height ) )
      trigger_error( E_004, E_USER_ERROR );

  }


  /*
    Description:
      Adds label text to thumbnail.
    Prototype:
      void AddLabel ( int thumb_width, int thumb_height, resource &thumb_img )
    Parameters:
      thumb_width - width of thumbnail image
      thumb_height - height of thumbnail image
      thumb_img - thumbnail image identifier
  */
  function AddLabel ( $thumb_width, $thumb_height, &$thumb_img )
  {

    extract ( $this->label );

    list( $r, $g, $b ) = $this->ParseColor ( $color );
    $color_id = imagecolorallocate ( $thumb_img, $r, $g, $b );

    $text_box = imagettfbbox ( $size, $angle, $font, $text );
    $text_width = $text_box [ 2 ] - $text_box [ 0 ];
    $text_height = abs ( $text_box [ 1 ] - $text_box [ 7 ] );

    if ( $vert_pos == POS_TOP )
      $y_pos = 5 + $text_height;
    elseif ( $vert_pos == POS_CENTER )
      $y_pos = ceil( $thumb_height / 2 - $text_height / 2 );
    elseif ( $vert_pos == POS_BOTTOM )
      $y_pos = $thumb_height - $text_height;

    if ( $horz_pos == POS_LEFT )
      $x_pos = 5;
    elseif ( $horz_pos == POS_CENTER )
      $x_pos = ceil( $thumb_width / 2 - $text_width / 2 );
    elseif ( $horz_pos == POS_RIGHT )
      $x_pos = $thumb_width - $text_width -5;

    imagettftext ( $thumb_img, $size, $angle, $x_pos, $y_pos,
      $color_id, $font, $text );

  }


  /*
    Description:
      Output thumbnail image into the browser.
    Prototype:
      void OutputThumbImage ( resource dest_image )
    Parameters:
      dest_img - thumbnail image identifier
  */
  function OutputThumbImage ( $dest_image )
  {

    imageinterlace ( $dest_image, $this->interlace );

    header ( 'Content-type: ' . $this->dest_type );

    if ( $this->dest_type == THUMB_JPEG )
      imagejpeg ( $dest_image, '', $this->jpeg_quality );
    elseif ( $this->dest_type == THUMB_GIF )
      imagegif($dest_image);
    elseif ( $this->dest_type == THUMB_PNG )
      imagepng ( $dest_image );

  }

  /*
    Description:
      Save thumbnail image into the disc file.
    Prototype:
      void SaveThumbImage ( string image_file, resource dest_image )
    Parameters:
      image_file - destination file name
      dest_img - thumbnail image identifier
  */
  function SaveThumbImage ( $image_file, $dest_image )
  {

    imageinterlace ( $dest_image, $this->interlace );

    if ( $this->dest_type == THUMB_JPEG )
      imagejpeg ( $dest_image, $this->dest_file, $this->jpeg_quality );
    elseif ( $this->dest_type == THUMB_GIF )
      imagegif ( $dest_image, $this->dest_file );
    elseif ( $this->dest_type == THUMB_PNG )
      imagepng ( $dest_image, $this->dest_file );

  }

// ****************************************************************************
// PUBLIC METHODS
// ****************************************************************************


  /*
    Description:
      Output thumbnail image into the browser or disc file according to the
      values of parameters.
    Prototype:
      void Output ( )
  */
  function Output()
  {

    $src_image = $this->LoadImage($this->src_file, $src_width, $src_height);

    $dest_size = $this->GetThumbSize($src_width, $src_height);

    $dest_width=$dest_size[0];
    $dest_height=$dest_size[1];

    $dest_image=imagecreatetruecolor($dest_width, $dest_height);
    if (!$dest_image)
      trigger_error(E_005, E_USER_ERROR);

    imagecopyresampled( $dest_image, $src_image, 0, 0, 0, 0,
      $dest_width, $dest_height, $src_width, $src_height );

    if ($this->logo['file'] != NO_LOGO)
      $this->AddLogo($dest_width, $dest_height, $dest_image);

    if ($this->label['text'] != NO_LABEL)
      $this->AddLabel($dest_width, $dest_height, $dest_image);

    if ($this->dest_file == STDOUT)
      $this->OutputThumbImage ( $dest_image );
    else
      $this->SaveThumbImage ( $this->dest_file, $dest_image );

    imagedestroy ( $src_image );
    imagedestroy ( $dest_image );

  }

} // End of class definition

?>
