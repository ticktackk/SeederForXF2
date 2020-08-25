<?php

namespace TickTackk\Seeder\Seed;

use TickTackk\Seeder\Seed\Exception\DownloadUrlDidNotReturnOkResponseException;
use TickTackk\Seeder\Seed\Exception\InvalidDownloadUrlProvidedException;
use XF\Attachment\AbstractHandler as AttachmentHandler;
use XF\Attachment\Manipulator as AttachmentManipulator;
use XF\Entity\Attachment as AttachmentEntity;
use XF\Http\Upload as HttpUpload;
use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Manager as EntityManager;
use XF\Mvc\Entity\Repository;
use XF\App as BaseApp;
use Faker\Generator as FakerGenerator;
use Faker\Factory as FakerFactory;
use XF\Phrase;
use XF\Repository\Attachment as AttachmentRepo;
use XF\Service\AbstractService;
use Bluemmb\Faker\PicsumPhotosProvider as PicsumFakerProvider;
use Faker\Provider\Youtube as YouTubeFakerProvider;
use XF\Util\File as FileUtil;
use XF\Util\Random as RandomUtil;

/**
 * Class AbstractSeed
 *
 * @package TickTackk\Seeder\Seed
 */
abstract class AbstractSeed
{
    /**
     * @var BaseApp
     */
    protected $app;

    /**
     * @var FakerGenerator
     */
    protected $faker;

    public function __construct(BaseApp $app)
    {
        $this->app = $app;

        \XF::$time = \time();
    }

    abstract protected function seed(array $params = []) : bool;

    /**
     * @throws \Exception
     */
    public function insert(?array $params = []) : bool
    {
        /** @var \XF\Entity\User $randomUser */
        $randomUser = $this->finderWithRandomOrder('XF:User')->fetchOne();

        try
        {
            return \XF::asVisitor($randomUser, function () use($params)
            {
                return $this->seed($params);
            });
        }
        finally
        {
            $this->em()->clearEntityCache();
        }
    }

    protected function downloadFile(string $url) : array
    {
        $validator = $this->app()->validator('Url');
        if (!$validator->isValid($url))
        {
            throw new InvalidDownloadUrlProvidedException($url);
        }

        $tempFile = FileUtil::getTempFile(false);
        $response = $this->app()->http()->reader()->getUntrusted($url, [], $tempFile);
        if (!$response)
        {
            throw new DownloadUrlDidNotReturnOkResponseException($url);
        }

        if ($response->getStatusCode() !== 200)
        {
            throw new DownloadUrlDidNotReturnOkResponseException($url, $response->getStatusCode());
        }

        $extension = $this->getExtensionFromMime(\mime_content_type($tempFile));
        return [
            'file' => $tempFile,
            'randomFilename' => RandomUtil::getRandomString(15) . '.' . $extension,
        ];
    }

    /**
     * @throws \Exception
     */
    protected function createHttpUploadFromFile(string $filePath, string $fileName) : HttpUpload
    {
        /** @var HttpUpload $class */
        $class = \XF::extendClass('XF\Http\Upload');

        return new $class($filePath, $fileName, 0);
    }

    /**
     * @throws \Exception
     */
    protected function createHttpUploadFromUrl(string $url) :? HttpUpload
    {
        $downloadedFileData = $this->downloadFile($url);

        return $this->createHttpUploadFromFile($downloadedFileData['file'], $downloadedFileData['randomFilename']);
    }

    /**
     * @throws \Exception
     */
    protected function getAttachmentManipulator(AttachmentHandler $handler, array $context) : AttachmentManipulator
    {
        /** @var AttachmentManipulator $manipulator */
        $class = \XF::extendClass('XF\Attachment\Manipulator');
        return new $class(
            $handler,
            $this->getAttachmentRepo(),
            $context,
            \md5(\microtime(true) . RandomUtil::getRandomString(8))
        );
    }

    protected function insertAttachmentFromHttpUpload(HttpUpload $upload, string $contentType, array $context, Phrase &$error = null) :? AttachmentEntity
    {
        $attachmentHandler = $this->getAttachmentHandler($contentType);
        $attachmentManipulator = $this->getAttachmentManipulator($attachmentHandler, $context);
        return $attachmentManipulator->insertAttachmentFromUpload($upload, $error);
    }

    /**
     * @throws \Exception
     */
    protected function insertAttachmentFromUrl(string $url, string $contentType, array $context, Phrase &$error = null) :? AttachmentEntity
    {
        return $this->insertAttachmentFromHttpUpload($this->createHttpUploadFromUrl($url), $contentType, $context, $error);
    }

    protected function finderWithRandomOrder(string $identifier) : Finder
    {
        return $this->finder($identifier)->order(Finder::ORDER_RANDOM);
    }

    public function faker(): FakerGenerator
    {
        if ($this->faker === null)
        {
            $faker = FakerFactory::create();
            $faker->addProvider(new PicsumFakerProvider($faker));
            $faker->addProvider(new YouTubeFakerProvider($faker));
            $this->faker = $faker;

            return $this->faker();
        }

        return $this->faker;
    }

    /**
     * @see https://gist.github.com/alexcorvi/df8faecb59e86bee93411f6a7967df2c
     *
     * @return string|null
     */
    protected function getExtensionFromMime(string $mime) :? string
    {
        $mimeMap = [
            'video/3gpp2'                                                               => '3g2',
            'video/3gp'                                                                 => '3gp',
            'video/3gpp'                                                                => '3gp',
            'application/x-compressed'                                                  => '7zip',
            'audio/x-acc'                                                               => 'aac',
            'audio/ac3'                                                                 => 'ac3',
            'application/postscript'                                                    => 'ai',
            'audio/x-aiff'                                                              => 'aif',
            'audio/aiff'                                                                => 'aif',
            'audio/x-au'                                                                => 'au',
            'video/x-msvideo'                                                           => 'avi',
            'video/msvideo'                                                             => 'avi',
            'video/avi'                                                                 => 'avi',
            'application/x-troff-msvideo'                                               => 'avi',
            'application/macbinary'                                                     => 'bin',
            'application/mac-binary'                                                    => 'bin',
            'application/x-binary'                                                      => 'bin',
            'application/x-macbinary'                                                   => 'bin',
            'image/bmp'                                                                 => 'bmp',
            'image/x-bmp'                                                               => 'bmp',
            'image/x-bitmap'                                                            => 'bmp',
            'image/x-xbitmap'                                                           => 'bmp',
            'image/x-win-bitmap'                                                        => 'bmp',
            'image/x-windows-bmp'                                                       => 'bmp',
            'image/ms-bmp'                                                              => 'bmp',
            'image/x-ms-bmp'                                                            => 'bmp',
            'application/bmp'                                                           => 'bmp',
            'application/x-bmp'                                                         => 'bmp',
            'application/x-win-bitmap'                                                  => 'bmp',
            'application/cdr'                                                           => 'cdr',
            'application/coreldraw'                                                     => 'cdr',
            'application/x-cdr'                                                         => 'cdr',
            'application/x-coreldraw'                                                   => 'cdr',
            'image/cdr'                                                                 => 'cdr',
            'image/x-cdr'                                                               => 'cdr',
            'zz-application/zz-winassoc-cdr'                                            => 'cdr',
            'application/mac-compactpro'                                                => 'cpt',
            'application/pkix-crl'                                                      => 'crl',
            'application/pkcs-crl'                                                      => 'crl',
            'application/x-x509-ca-cert'                                                => 'crt',
            'application/pkix-cert'                                                     => 'crt',
            'text/css'                                                                  => 'css',
            'text/x-comma-separated-values'                                             => 'csv',
            'text/comma-separated-values'                                               => 'csv',
            'application/vnd.msexcel'                                                   => 'csv',
            'application/x-director'                                                    => 'dcr',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'   => 'docx',
            'application/x-dvi'                                                         => 'dvi',
            'message/rfc822'                                                            => 'eml',
            'application/x-msdownload'                                                  => 'exe',
            'video/x-f4v'                                                               => 'f4v',
            'audio/x-flac'                                                              => 'flac',
            'video/x-flv'                                                               => 'flv',
            'image/gif'                                                                 => 'gif',
            'application/gpg-keys'                                                      => 'gpg',
            'application/x-gtar'                                                        => 'gtar',
            'application/x-gzip'                                                        => 'gzip',
            'application/mac-binhex40'                                                  => 'hqx',
            'application/mac-binhex'                                                    => 'hqx',
            'application/x-binhex40'                                                    => 'hqx',
            'application/x-mac-binhex40'                                                => 'hqx',
            'text/html'                                                                 => 'html',
            'image/x-icon'                                                              => 'ico',
            'image/x-ico'                                                               => 'ico',
            'image/vnd.microsoft.icon'                                                  => 'ico',
            'text/calendar'                                                             => 'ics',
            'application/java-archive'                                                  => 'jar',
            'application/x-java-application'                                            => 'jar',
            'application/x-jar'                                                         => 'jar',
            'image/jp2'                                                                 => 'jp2',
            'video/mj2'                                                                 => 'jp2',
            'image/jpx'                                                                 => 'jp2',
            'image/jpm'                                                                 => 'jp2',
            'image/jpeg'                                                                => 'jpeg',
            'image/pjpeg'                                                               => 'jpeg',
            'application/x-javascript'                                                  => 'js',
            'application/json'                                                          => 'json',
            'text/json'                                                                 => 'json',
            'application/vnd.google-earth.kml+xml'                                      => 'kml',
            'application/vnd.google-earth.kmz'                                          => 'kmz',
            'text/x-log'                                                                => 'log',
            'audio/x-m4a'                                                               => 'm4a',
            'application/vnd.mpegurl'                                                   => 'm4u',
            'audio/midi'                                                                => 'mid',
            'application/vnd.mif'                                                       => 'mif',
            'video/quicktime'                                                           => 'mov',
            'video/x-sgi-movie'                                                         => 'movie',
            'audio/mpeg'                                                                => 'mp3',
            'audio/mpg'                                                                 => 'mp3',
            'audio/mpeg3'                                                               => 'mp3',
            'audio/mp3'                                                                 => 'mp3',
            'video/mp4'                                                                 => 'mp4',
            'video/mpeg'                                                                => 'mpeg',
            'application/oda'                                                           => 'oda',
            'audio/ogg'                                                                 => 'ogg',
            'video/ogg'                                                                 => 'ogg',
            'application/ogg'                                                           => 'ogg',
            'application/x-pkcs10'                                                      => 'p10',
            'application/pkcs10'                                                        => 'p10',
            'application/x-pkcs12'                                                      => 'p12',
            'application/x-pkcs7-signature'                                             => 'p7a',
            'application/pkcs7-mime'                                                    => 'p7c',
            'application/x-pkcs7-mime'                                                  => 'p7c',
            'application/x-pkcs7-certreqresp'                                           => 'p7r',
            'application/pkcs7-signature'                                               => 'p7s',
            'application/pdf'                                                           => 'pdf',
            'application/octet-stream'                                                  => 'pdf',
            'application/x-x509-user-cert'                                              => 'pem',
            'application/x-pem-file'                                                    => 'pem',
            'application/pgp'                                                           => 'pgp',
            'application/x-httpd-php'                                                   => 'php',
            'application/php'                                                           => 'php',
            'application/x-php'                                                         => 'php',
            'text/php'                                                                  => 'php',
            'text/x-php'                                                                => 'php',
            'application/x-httpd-php-source'                                            => 'php',
            'image/png'                                                                 => 'png',
            'image/x-png'                                                               => 'png',
            'application/powerpoint'                                                    => 'ppt',
            'application/vnd.ms-powerpoint'                                             => 'ppt',
            'application/vnd.ms-office'                                                 => 'ppt',
            'application/msword'                                                        => 'doc',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
            'application/x-photoshop'                                                   => 'psd',
            'image/vnd.adobe.photoshop'                                                 => 'psd',
            'audio/x-realaudio'                                                         => 'ra',
            'audio/x-pn-realaudio'                                                      => 'ram',
            'application/x-rar'                                                         => 'rar',
            'application/rar'                                                           => 'rar',
            'application/x-rar-compressed'                                              => 'rar',
            'audio/x-pn-realaudio-plugin'                                               => 'rpm',
            'application/x-pkcs7'                                                       => 'rsa',
            'text/rtf'                                                                  => 'rtf',
            'text/richtext'                                                             => 'rtx',
            'video/vnd.rn-realvideo'                                                    => 'rv',
            'application/x-stuffit'                                                     => 'sit',
            'application/smil'                                                          => 'smil',
            'text/srt'                                                                  => 'srt',
            'image/svg+xml'                                                             => 'svg',
            'application/x-shockwave-flash'                                             => 'swf',
            'application/x-tar'                                                         => 'tar',
            'application/x-gzip-compressed'                                             => 'tgz',
            'image/tiff'                                                                => 'tiff',
            'text/plain'                                                                => 'txt',
            'text/x-vcard'                                                              => 'vcf',
            'application/videolan'                                                      => 'vlc',
            'text/vtt'                                                                  => 'vtt',
            'audio/x-wav'                                                               => 'wav',
            'audio/wave'                                                                => 'wav',
            'audio/wav'                                                                 => 'wav',
            'application/wbxml'                                                         => 'wbxml',
            'video/webm'                                                                => 'webm',
            'audio/x-ms-wma'                                                            => 'wma',
            'application/wmlc'                                                          => 'wmlc',
            'video/x-ms-wmv'                                                            => 'wmv',
            'video/x-ms-asf'                                                            => 'wmv',
            'application/xhtml+xml'                                                     => 'xhtml',
            'application/excel'                                                         => 'xl',
            'application/msexcel'                                                       => 'xls',
            'application/x-msexcel'                                                     => 'xls',
            'application/x-ms-excel'                                                    => 'xls',
            'application/x-excel'                                                       => 'xls',
            'application/x-dos_ms_excel'                                                => 'xls',
            'application/xls'                                                           => 'xls',
            'application/x-xls'                                                         => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'         => 'xlsx',
            'application/vnd.ms-excel'                                                  => 'xlsx',
            'application/xml'                                                           => 'xml',
            'text/xml'                                                                  => 'xml',
            'text/xsl'                                                                  => 'xsl',
            'application/xspf+xml'                                                      => 'xspf',
            'application/x-compress'                                                    => 'z',
            'application/x-zip'                                                         => 'zip',
            'application/zip'                                                           => 'zip',
            'application/x-zip-compressed'                                              => 'zip',
            'application/s-compressed'                                                  => 'zip',
            'multipart/x-zip'                                                           => 'zip',
            'text/x-scriptzsh'                                                          => 'zsh',
        ];

        if (!\array_key_exists($mime, $mimeMap))
        {
            return null;
        }

        return $mimeMap[$mime];
    }

    protected function service(string $class, ...$arguments) : AbstractService
    {
        return $this->app()->service($class, ...$arguments);
    }

    protected function repository(string $identifier): Repository
    {
        return $this->app()->repository($identifier);
    }

    protected function finder(string $identifier): Finder
    {
        return $this->app()->finder($identifier);
    }

    protected function options() : \ArrayObject
    {
        return $this->app()->options();
    }

    /**
     * @return mixed
     */
    protected function config(string $key = null)
    {
        return $this->app()->config($key);
    }

    protected function em() : EntityManager
    {
        return $this->app()->em();
    }

    protected function app() : BaseApp
    {
        return $this->app;
    }

    protected function getAttachmentRepo() : AttachmentRepo
    {
        return $this->repository('XF:Attachment');
    }

    protected function getAttachmentHandler(string $contentType) : AttachmentHandler
    {
        return $this->getAttachmentRepo()->getAttachmentHandler($contentType);
    }
}