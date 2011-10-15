<?php

/** This file is part of KCFinder project
  *
  *      @desc ImageMagick image driver class
  *   @package KCFinder
  *   @version 2.52-dev
  *    @author Pavel Tzonkov <pavelc@users.sourceforge.net>
  * @copyright 2010, 2011 KCFinder Project
  *   @license http://www.opensource.org/licenses/gpl-2.0.php GPLv2
  *   @license http://www.opensource.org/licenses/lgpl-2.1.php LGPLv2
  *      @link http://kcfinder.sunhater.com
  */

class image_imagick extends image {

    static $MIMES = array(
        //'tif' => "image/tiff"
    );


    // ABSTRACT PUBLIC METHODS

    public function resize($width, $height) {//
        if (!$width) $width = 1;
        if (!$height) $height = 1;
        if (!$this->image->scaleImage($width, $height))
            return false;
        $this->width = $width;
        $this->height = $height;
        return true;
    }

    public function resizeFit($width, $height, $background=false) {//
        if (!$width) $width = 1;
        if (!$height) $height = 1;

        if (!$this->image->scaleImage($width, $height, true))
            return false;
        $size = $this->image->getImageGeometry();

        if ($background === false) {
            $this->width = $size['width'];
            $this->height = $size['height'];
            return true;

        } else {
            if (!$this->image->setImageBackgroundColor($background))
                return false;
            $x = -round(($width - $size['width']) / 2);
            $y = -round(($height - $size['height']) / 2);
            if (!$this->image->extentImage($width, $height, $x, $y))
                return false;
            $this->width = $width;
            $this->height = $height;
        }
    }

    public function resizeCrop($width, $height, $offset=false) {
        if (!$width) $width = 1;
        if (!$height) $height = 1;

        if (($this->width / $this->height) > ($width / $height)) {
            $h = $height;
            $w = ($this->width * $h) / $this->height;
            $y = 0;
            if ($offset !== false) {
                if ($offset > 0)
                    $offset = -$offset;
                if (($w + $offset) <= $width)
                    $offset = $width - $w;
                $x = $offset;
            } else
                $x = ($width - $w) / 2;

        } else {
            $w = $width;
            $h = ($this->height * $h) / $this->width;
            $x = 0;
            if ($offset !== false) {
                if ($offset > 0)
                    $offset = -$offset;
                if (($h + $offset) <= $height)
                    $offset = $height - $h;
                $y = $offset;
            } else
                $y = ($height - $h) / 2;
        }

        $x = round($x);
        $y = round($y);
        $w = round($w);
        $h = round($h);
        if (!$w) $w = 1;
        if (!$h) $h = 1;

        if (!$this->image->setImageCompressionQuality(100) ||
            !$this->image->scaleImage($w, $h) ||
            !$this->image->cropImage($width, $height, -$x, -$y)
        )
            return false;

        $this->width = $width;
        $this->height = $height;
        return true;
    }

    public function watermark($file, $top=false, $left=false) {
        $wm = new Imagick($file);
        $size = $wm->getImageGeometry();
        $w = $size['width'];
        $h = $size['height'];
        $x = $left ? 0 : ($this->width - $w);
        $y = $top ? 0 : ($this->height - $h);
        return (
            $this->image->compositeImage($wm, Imagick::COMPOSITE_DEFAULT, $x, $y)
        );
    }


    // ABSTRACT PROTECTED METHODS

    protected function getBlankImage($width, $height) {
        $img = new Imagick();
        $return = $img->newImage($width, $height, "none") ? $img : false;
        if ($return !== false)
            $img->setImageCompressionQuality(100);
        return $return;
    }

    protected function getImage($image, &$width, &$height) {

        if (is_object($image) && ($image instanceof image_imagick)) {
            $image->image->setImageCompressionQuality(100);
            $width = $image->width;
            $height = $image->height;
            return $image->image;

        } elseif (is_object($image) && ($image instanceof Imagick)) {
            $image->setImageCompressionQuality(100);
            $size = $image->getImageGeometry();
            $width = $size['width'];
            $height = $size['height'];
            return $image;

        } elseif (is_string($image)) {
            try {
                $image = new Imagick($image);
            } catch (Exception $e) {
                return false;
            }
            $image->setImageCompressionQuality(100);
            $size = $image->getImageGeometry();
            $width = $size['width'];
            $height = $size['height'];
            return $image;

        } else
            return false;
    }


    // PSEUDO-ABSTRACT STATIC METHODS

    static function available() {
        return class_exists("Imagick");
    }

    static function checkImage($file) {
        try {
            $img = new Imagic($file);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }


    // INHERIT METHODS

    public function output($type="jpeg", array $options=array()) {
        $type = strtolower($type);
        if (!$this->image->setImageFormat($type))
            return false;
        $method = "optimize_$type";
        if (method_exists($this, $method) && !$this->$method($options))
            return false;

        if (!isset($options['file'])) {
            if (!headers_sent()) {
                $mime = isset(self::$MIMES[$type]) ? self::$MIMES[$type] : "image/$type";
                header("Content-Type: $mime");
            }
            echo $this->image;

        } else {
            $file = $options['file'] . ".$type";
            if (!$this->image->writeImage($file) ||
                !@rename($file, $options['file'])
            ) {
                @unlink($file);
                return false;
            }
        }

        return true;
    }


    // OWN METHODS

    protected function optimize_jpeg(array $options=array()) {
        $quality = isset($options['quality']) ? $options['quality'] : self::DEFAULT_JPEG_QUALITY;
        return (
            $this->image->setImageCompression(Imagick::COMPRESSION_JPEG) &&
            $this->image->setImageCompressionQuality($quality)
        );
    }

}

?>