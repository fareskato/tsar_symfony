<?php

namespace AppBundle\Controller;


use AppBundle\Entity\BookDomain;
use AppBundle\Entity\Files;
use AppBundle\Service\Locales;
use Gedmo\Mapping\Annotation\Locale;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Intl\Intl;


class FileController extends Controller
{

	private $_locales;
	private $_defaultLocale;

	public function __construct()
	{
		//We need locales everywhere in code
		//Проставляем локали
		$loc = new Locales();
		$this->_locales = $loc->getLocales();
		$this->_defaultLocale = $loc->getDefaultLocale();
	}


    /**
     * @Route("/file/upload", name="file_upload")
     */
    public function indexAction(Request $request, $id = 0, $file = null, $uploadDir = '', $originalName = 0, $user = null, $em = null)
    {

		$return = array();

		if (!$em) {
			$em = $this->getDoctrine()->getManager();
		}

		if ($request->isMethod('POST') || $id > 0 || $file) {
			$data = $request->request->all();

			$file = $request->files->get('file');
			$fileOriginalName = $file->getClientOriginalName();
			$fileName = md5(uniqid().time()).'.'.$file->guessExtension();
			$fileMime = $file->getMimeType();
			$fileType = explode('/',$fileMime);

			if (preg_match('/image/iUs',$fileMime)) {
				$this->checkExif($file->getPathname(),$fileMime);
			}

			if (!$uploadDir) {
				$uploadDir = $this->getParameter('upload.dir');
			}
			$where = $uploadDir.(!empty($data['type']) ? '/'.$data['type'] : '');

			$file->move(
				$where,
				$fileName
			);

			if ($id > 0) {
				$uFile = $em->getRepository('AppBundle\Entity\files')->findOneBy(array('id' => intval($id)));
			} else {
				$uFile = new Files();
			}

			if (!$user) {
				$user = $this->getUser();
			}


			if ($user) {
				$uFile->setUserId($user);
			} else {
				$uFile->setUserId(null);
			}



			$uFile->setUrl((!empty($data['type']) ? '/'.$data['type'] : '').'/'.$fileName);
			$uFile->setActive(1);
			$uFile->setMime($fileMime);


			$uFile->setType($fileType[0]);


			if ($originalName) {
				$uFile->translate($this->_defaultLocale,false)->setName($fileOriginalName);
				$uFile->mergeNewTranslations();
			}

			$uFile->setFileName($fileOriginalName);

			$em->persist($uFile);
			$em->flush();

			if ($id > 0) {
				return true;
			}


			$imagineCacheManager = $this->get('liip_imagine.cache.manager');

			$return['id'] = $uFile->getId();
			$return['path'] = $uFile->getUrl();
			$return['full_path'] = $this->getParameter('upload.web').$uFile->getUrl();
			$return['link'] = $this->getParameter('upload.web').$uFile->getUrl();
			$return['thumbnail_100'] = $imagineCacheManager->getBrowserPath($this->getParameter('upload.web').$uFile->getUrl(), 'thumb100x100');
		}

		echo json_encode($return);
		die();
    }

	private function checkExif($filename,$fileMime) {
		$image = imagecreatefromstring(file_get_contents($filename));

		if (function_exists('exif_read_data') && preg_match('/(jpg|jpeg)/',strtolower($fileMime))) {
			//$exif = exif_read_data($filename);
            /*
             * Простой вариант на некоторых фото (возможно зависит от сервера) возвращал ошибку -
             * Warning: exif_read_data(phpB5E5.tmp): Illegal IFD size
             * Переписано через исключение 06.07.2017
             */
            try {
                $exif = @exif_read_data($filename);
            } catch (Exception $exp) {
                $exif = false;
            }
			if(!empty($exif['Orientation'])) {
				switch($exif['Orientation']) {
					case 8:
						$image = imagerotate($image,90,0);
                        imagejpeg($image, null, 100);
						break;
					case 3:
						$image = imagerotate($image,180,0);
                        imagejpeg($image, null, 100);
						break;
					case 6:
						$image = imagerotate($image,-90,0);
                        imagejpeg($image, null, 100);
						break;
				}
			}
		}

	}
}
