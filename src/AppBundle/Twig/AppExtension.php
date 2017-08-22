<?php

// src/AppBundle/Twig/AppExtension.php
namespace AppBundle\Twig;

use AppBundle\Service\Locales;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Translation\TranslatorInterface;

class AppExtension extends \Twig_Extension
{

	private $translator;
	private $router;
	public function __construct(TranslatorInterface $translator, $router) {
		$this->translator = $translator;
		$this->router = $router;
	}


	public function getFilters()
	{
		return array(
			new \Twig_SimpleFilter('translateUrl', array($this, 'translateUrl')),
			new \Twig_SimpleFilter('translateMonth', array($this, 'translateMonth')),
			new \Twig_SimpleFilter('contentImages', array($this, 'contentImages')),
			new \Twig_SimpleFilter('transliteNames', array($this, 'transliteNames')),
		);
	}

	public function getFunctions() {
		return array(
			new \Twig_SimpleFunction('get_class_methods', array($this, 'getClassMethods')),
			new \Twig_SimpleFunction('print_r', array($this, 'getPrintR')),
			new \Twig_SimpleFunction('exculde_path', array($this, 'exculdePath')),
			new \Twig_SimpleFunction('translate', array($this, 'translateField')),
			new \Twig_SimpleFunction('preg_match', array($this, 'pregMatch')),
			new \Twig_SimpleFunction('admin_sort_link', array($this, 'adminSortLink'))
		);
	}

	public function getClassMethods($class)
	{
		echo '<pre>';
		print_r(get_class_methods($class));
		echo '</pre>';
		die();
		return null;
	}
	public function getPrintR($arr)
	{
		echo '<pre>';
		print_r($arr);
		echo '</pre>';
		die();
		return null;
	}
	public function exculdePath($path,$id,$type)
	{
		if (empty($id)) {
			$path = preg_replace('/\/'.$type.'\/.*/','','/'.$path);
		} else {
			$path = preg_replace('/\/'.$type.'\/'.$id.'/','','/'.$path);
		}

		if (mb_substr($path,0,1,"UTF-8") == '/') {
			$path = mb_substr($path,1);
		}
		return $path;
	}

	public function pregMatch($string,$match)
	{
		if (preg_match('#'.$string.'#iUs',$match)) {
			return true;
		}
		return false;
	}

	public function translateField($entity,$field,$locale,$force = true)
	{
		$methodGet = $this->generateMethodName('get',$field);
		$methodIs = $this->generateMethodName('is',$field);

		if (method_exists($entity,$field)) {
			return $entity->$field();
		} elseif (method_exists($entity,$methodGet)) {
			return $entity->$methodGet();
		} else if (method_exists($entity,$methodIs)) {
			return $entity->$methodIs();
		} else {
			$translated = $entity->translate($locale,$force);
			if (method_exists($translated,$methodGet)) {
				return $translated->$methodGet();
			} elseif (method_exists($translated,$methodIs)) {
				return $translated->$methodIs();
			}
		}

		throw new MethodNotAllowedHttpException(get_class_methods($entity),'Method not found!');

	}


	private function generateMethodName($type = 'get',$field) {
		$name = ucwords(str_replace('_',' ',$field));
		$name = str_replace(' ','',$name);
		return $type.$name;
	}

	public function translateUrl($str){
		if (preg_match_all('/{{(.*)}}/iUs',$str,$m)) {
			if (!empty($m[0])) {
				foreach($m[0] as $key => $match) {
					$str = str_replace($match, $this->translator->trans( trim($m[1][$key]) ),$str);
				}
			}
		}
		return $str;
	}

	public function transliteNames($s){
		$replace = array(
			'ъ'=>'-', 'Ь'=>'-', 'Ъ'=>'-', 'ь'=>'-',
			'Ă'=>'A', 'Ą'=>'A', 'À'=>'A', 'Ã'=>'A', 'Á'=>'A', 'Æ'=>'A', 'Â'=>'A', 'Å'=>'A', 'Ä'=>'Ae',
			'Þ'=>'B',
			'Ć'=>'C', 'ץ'=>'C', 'Ç'=>'C',
			'È'=>'E', 'Ę'=>'E', 'É'=>'E', 'Ë'=>'E', 'Ê'=>'E',
			'Ğ'=>'G',
			'İ'=>'I', 'Ï'=>'I', 'Î'=>'I', 'Í'=>'I', 'Ì'=>'I',
			'Ł'=>'L',
			'Ñ'=>'N', 'Ń'=>'N',
			'Ø'=>'O', 'Ó'=>'O', 'Ò'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'Oe',
			'Ş'=>'S', 'Ś'=>'S', 'Ș'=>'S', 'Š'=>'S',
			'Ț'=>'T',
			'Ù'=>'U', 'Û'=>'U', 'Ú'=>'U', 'Ü'=>'Ue',
			'Ý'=>'Y',
			'Ź'=>'Z', 'Ž'=>'Z', 'Ż'=>'Z',
			'â'=>'a', 'ǎ'=>'a', 'ą'=>'a', 'á'=>'a', 'ă'=>'a', 'ã'=>'a', 'Ǎ'=>'a', 'а'=>'a', 'А'=>'a', 'å'=>'a', 'à'=>'a', 'א'=>'a', 'Ǻ'=>'a', 'Ā'=>'a', 'ǻ'=>'a', 'ā'=>'a', 'ä'=>'ae', 'æ'=>'ae', 'Ǽ'=>'ae', 'ǽ'=>'ae',
			'б'=>'b', 'ב'=>'b', 'Б'=>'b', 'þ'=>'b',
			'ĉ'=>'c', 'Ĉ'=>'c', 'Ċ'=>'c', 'ć'=>'c', 'ç'=>'c', 'ц'=>'c', 'צ'=>'c', 'ċ'=>'c', 'Ц'=>'c', 'Č'=>'c', 'č'=>'c', 'Ч'=>'ch', 'ч'=>'ch',
			'ד'=>'d', 'ď'=>'d', 'Đ'=>'d', 'Ď'=>'d', 'đ'=>'d', 'д'=>'d', 'Д'=>'D', 'ð'=>'d',
			'є'=>'e', 'ע'=>'e', 'е'=>'e', 'Е'=>'e', 'Ə'=>'e', 'ę'=>'e', 'ĕ'=>'e', 'ē'=>'e', 'Ē'=>'e', 'Ė'=>'e', 'ė'=>'e', 'ě'=>'e', 'Ě'=>'e', 'Є'=>'e', 'Ĕ'=>'e', 'ê'=>'e', 'ə'=>'e', 'è'=>'e', 'ë'=>'e', 'é'=>'e',
			'ф'=>'f', 'ƒ'=>'f', 'Ф'=>'f',
			'ġ'=>'g', 'Ģ'=>'g', 'Ġ'=>'g', 'Ĝ'=>'g', 'Г'=>'g', 'г'=>'g', 'ĝ'=>'g', 'ğ'=>'g', 'ג'=>'g', 'Ґ'=>'g', 'ґ'=>'g', 'ģ'=>'g',
			'ח'=>'h', 'ħ'=>'h', 'Х'=>'h', 'Ħ'=>'h', 'Ĥ'=>'h', 'ĥ'=>'h', 'х'=>'h', 'ה'=>'h',
			'î'=>'i', 'ï'=>'i', 'í'=>'i', 'ì'=>'i', 'į'=>'i', 'ĭ'=>'i', 'ı'=>'i', 'Ĭ'=>'i', 'И'=>'i', 'ĩ'=>'i', 'ǐ'=>'i', 'Ĩ'=>'i', 'Ǐ'=>'i', 'и'=>'i', 'Į'=>'i', 'י'=>'i', 'Ї'=>'i', 'Ī'=>'i', 'І'=>'i', 'ї'=>'i', 'і'=>'i', 'ī'=>'i', 'ĳ'=>'ij', 'Ĳ'=>'ij',
			'й'=>'j', 'Й'=>'j', 'Ĵ'=>'j', 'ĵ'=>'j', 'я'=>'ja', 'Я'=>'ja', 'Э'=>'je', 'э'=>'je', 'ё'=>'jo', 'Ё'=>'jo', 'ю'=>'ju', 'Ю'=>'ju',
			'ĸ'=>'k', 'כ'=>'k', 'Ķ'=>'k', 'К'=>'k', 'к'=>'k', 'ķ'=>'k', 'ך'=>'k',
			'Ŀ'=>'l', 'ŀ'=>'l', 'Л'=>'l', 'ł'=>'l', 'ļ'=>'l', 'ĺ'=>'l', 'Ĺ'=>'l', 'Ļ'=>'l', 'л'=>'l', 'Ľ'=>'l', 'ľ'=>'l', 'ל'=>'l',
			'מ'=>'m', 'М'=>'m', 'ם'=>'m', 'м'=>'m',
			'ñ'=>'n', 'н'=>'n', 'Ņ'=>'n', 'ן'=>'n', 'ŋ'=>'n', 'נ'=>'n', 'Н'=>'n', 'ń'=>'n', 'Ŋ'=>'n', 'ņ'=>'n', 'ŉ'=>'n', 'Ň'=>'n', 'ň'=>'n',
			'о'=>'o', 'О'=>'o', 'ő'=>'o', 'õ'=>'o', 'ô'=>'o', 'Ő'=>'o', 'ŏ'=>'o', 'Ŏ'=>'o', 'Ō'=>'o', 'ō'=>'o', 'ø'=>'o', 'ǿ'=>'o', 'ǒ'=>'o', 'ò'=>'o', 'Ǿ'=>'o', 'Ǒ'=>'o', 'ơ'=>'o', 'ó'=>'o', 'Ơ'=>'o', 'œ'=>'oe', 'Œ'=>'oe', 'ö'=>'oe',
			'פ'=>'p', 'ף'=>'p', 'п'=>'p', 'П'=>'p',
			'ק'=>'q',
			'ŕ'=>'r', 'ř'=>'r', 'Ř'=>'r', 'ŗ'=>'r', 'Ŗ'=>'r', 'ר'=>'r', 'Ŕ'=>'r', 'Р'=>'r', 'р'=>'r',
			'ș'=>'s', 'с'=>'s', 'Ŝ'=>'s', 'š'=>'s', 'ś'=>'s', 'ס'=>'s', 'ş'=>'s', 'С'=>'s', 'ŝ'=>'s', 'Щ'=>'sch', 'щ'=>'sch', 'ш'=>'sh', 'Ш'=>'sh', 'ß'=>'ss',
			'т'=>'t', 'ט'=>'t', 'ŧ'=>'t', 'ת'=>'t', 'ť'=>'t', 'ţ'=>'t', 'Ţ'=>'t', 'Т'=>'t', 'ț'=>'t', 'Ŧ'=>'t', 'Ť'=>'t', '™'=>'tm',
			'ū'=>'u', 'у'=>'u', 'Ũ'=>'u', 'ũ'=>'u', 'Ư'=>'u', 'ư'=>'u', 'Ū'=>'u', 'Ǔ'=>'u', 'ų'=>'u', 'Ų'=>'u', 'ŭ'=>'u', 'Ŭ'=>'u', 'Ů'=>'u', 'ů'=>'u', 'ű'=>'u', 'Ű'=>'u', 'Ǖ'=>'u', 'ǔ'=>'u', 'Ǜ'=>'u', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'У'=>'u', 'ǚ'=>'u', 'ǜ'=>'u', 'Ǚ'=>'u', 'Ǘ'=>'u', 'ǖ'=>'u', 'ǘ'=>'u', 'ü'=>'ue',
			'в'=>'v', 'ו'=>'v', 'В'=>'v',
			'ש'=>'w', 'ŵ'=>'w', 'Ŵ'=>'w',
			'ы'=>'y', 'ŷ'=>'y', 'ý'=>'y', 'ÿ'=>'y', 'Ÿ'=>'y', 'Ŷ'=>'y',
			'Ы'=>'y', 'ž'=>'z', 'З'=>'z', 'з'=>'z', 'ź'=>'z', 'ז'=>'z', 'ż'=>'z', 'ſ'=>'z', 'Ж'=>'zh', 'ж'=>'zh'
		);
		return strtr($s, $replace);
	}

	public function translateMonth($month,$short = false){
		$month = explode('-',$month);
		return $this->translator->trans('front.month.'.$month[1].($short?'.short':'')).' '.$month[0];
	}

	public function contentImages($str){
		$search = preg_match_all('/\[\[.*\]\]/iUs',$str,$m1);
		if (!empty($m1[0])) {
			foreach($m1[0] as  $file) {
				$fid = json_decode(html_entity_decode($file),true);
				if (!empty($fid[0][0]['fid'])) {
					$fid = $fid[0][0]['fid'];
					global $kernel;
					$em = $kernel->getContainer()->get('doctrine')->getEntityManager();
					$image = $em->getRepository('AppBundle\Entity\Files')->findOneBy(array('id'=>$fid));
					if (!empty($image)) {
						$newPath = '<img src="'.$kernel->getContainer('parameters')->getParameter('upload.web').$image->getUrl().'">';
						$str = str_replace($file,$newPath,$str);
					}
				}
			}
		}
		return $str;
	}

	public function adminSortLink($str,$route = '',$params = array()){

		$allowOrder = array('id','name');
		$allowCheck = array('active_domain','active_lang');

		$order = '';
		if (!empty($_GET['order'])) {
			$order = trim($_GET['order']);
		}

		if (!empty($_GET['search'])) {
			$search = strtolower(trim($_GET['search']));
			$params['search'] = $search;
		}

		if (!empty($_GET['active_lang'])) {
			foreach($_GET['active_lang'] as $value) {
				$params['active_lang['.$value.']'] = $value;
			}
		}

		$arrow = '<i class="fa fa-minus"></i>';
		$arrowDown = '<i class="fa fa-arrow-down"></i>';
		$arrowUp = '<i class="fa fa-arrow-up"></i>';

		if (in_array($str,$allowOrder)) {
			if (empty($order)) {
				if ($str == 'id') {
					$arrow = $arrowDown;
					$params['order'] = 'id'.($str == 'id' ? '|desc' : '|asc');
				} else {
					$params['order'] = $str.($str == 'id' ? '|desc' : '|asc');
				}
			} else {
				$order = explode('|',$order);
				$param = $order[0];
				$direction = $order[1];

				if ($str == $param) {
					if ($direction == 'asc') {
						$arrow = $arrowUp;
						$params['order'] = $str.'|desc';
					} else {
						$arrow = $arrowDown;
						$params['order'] = $str.'|asc';
					}
				} else {
					$params['order'] = $str.($str == 'id' ? '|desc' : '|asc');
				}
			}

			$url = $this->router->generate($route,$params);

			$html = '<a href="'.$url.'">';
			$html .= $arrow . ' '.$this->translator->trans('adm.field.'.$str);
			$html .= '</a>';

			$str = $html;

		} elseif (in_array($str,$allowCheck)) {
			$strAll = '<form method="get" action="'.$this->router->generate($route,$params).'"><div class="form-group">';
			if (!empty($search)) {
				$strAll .= '<input type="hidden" name="search" value="'.$search.'">';
			}
			if ($str == 'active_lang') {
				$locales = new Locales();
				$locales = $locales->getLocales();
				foreach($locales as $l) {
					$checked = false;
					if (!empty($_GET['active_lang']) && in_array($l,array_values($_GET['active_lang']))) {
						$checked = true;
					}
					$strAll .= '<div style="display: inline-block; text-align: center; margin: 0 2px;"><label style="margin-bottom: 0;"><div style="text-transform: capitalize">'.$l.'</div><input type="checkbox" name="active_lang['.$l.']" value="'.$l.'" '.($checked ? 'checked' : '').' onChange=" this.form.submit(); "></label></div>';
				}
			}
			$strAll .= '</div></form>';
			$str = $this->translator->trans('adm.field.'.$str).'<br>'.$strAll;
		} else {
			$str = $this->translator->trans('adm.field.'.$str);
		}

		return $str;
	}
}