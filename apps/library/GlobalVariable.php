<?php

use Phalcon\Mvc\User\Component;

class GlobalVariable extends Component
{
    public $timeZone;
    public $curTime;
    public $localTime;

    public $type_info;
    public $type_about;
    public $type_product;
    public $type_faq;
    public $type_news;
    public $contentTypeImages;
    public $site_key;
    public $serect_key;

	public $acceptUploadTypes;

	public function __construct()
	{
        date_default_timezone_set('UTC');//default for Application - NOT ONLY for current script
        $this->timeZone = 7*3600;
        $this->curTime = time();
        $this->localTime = time() + $this->timeZone;
        //Type article
        $this->type_info = 1;
        $this->type_about = 2;
        $this->type_product = 3;
        $this->type_faq = 4;
        $this->type_news = 5;
		//accept upload file types
		$this->acceptUploadTypes = array(
			"image/jpeg" => array("type" => "image", "ext" => ".jpg"),
			"image/pjpeg" => array("type" => "image", "ext" => ".jpg"),
			"image/png" => array("type" => "image", "ext" => ".png"),
			"image/bmp" => array("type" => "image", "ext" => ".bmp"),
			"image/x-windows-bmp" => array("type" => "image", "ext" => ".bmp"),
			"image/x-icon" => array("type" => "image", "ext" => ".ico"),
			"image/ico" => array("type" => "image", "ext" => ".ico"),
			"image/gif" => array("type" => "image", "ext" => ".gif"),
			"text/plain" => array("type" => "file", "ext" => ".txt"),
			"application/msword" => array("type" => "file", "ext" => ".doc"),
			"application/vnd.openxmlformats-officedocument.wordprocessingml.document" => array("type" => "file", "ext" => ".docx"),
			"application/vnd.ms-excel" => array("type" => "file", "ext" => ".xls"),
			"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" => array("type" => "file", "ext" => ".xlsx"),
			"application/pdf" => array("type" => "file", "ext" => ".pdf"),
		);
        //accept upload contactus
        $this->acceptUploadContactUs = array(
            "image/jpeg" => array("type" => "image", "ext" => ".jpg"),
            "image/pjpeg" => array("type" => "image", "ext" => ".jpg"),
            "image/png" => array("type" => "image", "ext" => ".png"),
            "image/bmp" => array("type" => "image", "ext" => ".bmp"),
            "image/x-windows-bmp" => array("type" => "image", "ext" => ".bmp"),
            "image/gif" => array("type" => "image", "ext" => ".gif"),
            "application/pdf" => array("type" => "file", "ext" => ".pdf"),
            "text/plain" => array("type" => "file", "ext" => ".txt"),
            "application/msword" => array("type" => "file", "ext" => ".doc"),
            "application/vnd.openxmlformats-officedocument.wordprocessingml.document" => array("type" => "file", "ext" => ".docx"),
            "application/vnd.ms-excel" => array("type" => "file", "ext" => ".xls"),
            "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" => array("type" => "file", "ext" => ".xlsx")
        );
        //Google captcha
        $this->site_key = "6LfIkUgUAAAAAKQeChpWlOD_H5p1X3JBoiR5Dzur";
        $this->serect_key ="6LfIkUgUAAAAAGfPfuULUqIbgMsSpKr8DIGhO-mb";
        $this->contentTypeImages = array(
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',
            'txt' => 'text/plain',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'pdf' => 'application/pdf',
        );
		
	}
}