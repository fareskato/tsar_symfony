<?php

namespace AppBundle\Controller;


use AppBundle\Entity\BookDomain;
use AppBundle\Service\Forms;
use AppBundle\Service\Locales;
use Gedmo\Mapping\Annotation\Locale;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Intl\Intl;


class ExtensionController extends Controller
{

	private $_locales;
	private $_defaultLocale;


	private $_itemsOnPage = 10;

	public function __construct()
	{
		//We need locales everywhere in code
		//Проставляем локали
		$loc = new Locales();
		$this->_locales = $loc->getLocales();
		$this->_defaultLocale = $loc->getDefaultLocale();
	}


    public function indexAction(Request $request, $filters = '')
    {

		$data = array();
		$em = $this->getDoctrine()->getManager();

		$extensions = $em->getRepository('AppBundle\Entity\Hotel')->findAllExtensions($em,$request);

		$filtered_destinations = array();
		$filtered_categories = array();
		$filtered_recreations = array();
		$filtered_seasons = array();
		$filtered_days = array();
		$filtered_destinations_from = array();

		if ($filters) {
			$filtersArray = explode('/',$filters);
			$key = null;
			foreach($filtersArray as $k => $f) {
				if ($k % 2 == 0) {
					$key = $f;
				} else {
					if ($key == $this->get('translator')->trans('url.extension.destination')) {
						$filtered_destinations[$f] = null;
					} else if ($key == $this->get('translator')->trans('url.extension.category')) {
						$filtered_categories[$f] = null;
					} else if ($key == $this->get('translator')->trans('url.extension.recreation')) {
						$filtered_recreations[$f] = null;
					} else if ($key == $this->get('translator')->trans('url.extension.season')) {
						$filtered_seasons[$f] = null;
					} else if ($key == $this->get('translator')->trans('url.extension.days')) {
						$filtered_days[$f] = null;
					} else if ($key == $this->get('translator')->trans('url.extension.from')) {
						$filtered_destinations_from[$f] = null;
					}
					$key = null;
				}
			}
		}

		$destinations = array();
		$categories = array(
			0 => array(
				'name' => 'individual',
				'count' => 0
			),
			1 => array(
				'name' => 'mini_groupe',
				'count' => 0
			)
		);
		$recreations = array();
		$seasons = array();
		$days = array();
		$destinations_from = array();


		//CITIES TAKEN FROM ALL
		foreach($extensions as $extension) {

			foreach($extension->getRelatedContent() as $d) {
				if (empty($destinations[ $d->getId() ]['entity'])) {
					$destinations[ $d->getId() ]['entity'] = $d;
					$destinations[ $d->getId() ]['count'] = 0;
				}
				$destinations[ $d->getId() ]['count']++;
				if (in_array($d->getId(),array_keys($filtered_destinations)) && empty($filtered_destinations[ $d->getId() ])) {
					$filtered_destinations[ $d->getId() ] = $d;
				}
			}

			if (!empty($extension->isMiniGroupe())) {
				$categories[1]['count']++;
			} else {
				$categories[0]['count']++;
			}


			foreach($extension->getExtensionRecreation() as $s) {
				if (empty($recreations[ $s->getId() ]['entity'])) {
					$recreations[ $s->getId() ]['entity'] = $s;
					$recreations[ $s->getId() ]['count'] = 0;
				}
				$recreations[ $s->getId() ]['count']++;
				if (in_array($s->getId(),array_keys($filtered_recreations)) && empty($filtered_recreations[ $s->getId() ])) {
					$filtered_recreations[ $s->getId() ] = $s;
				}
			}

			foreach($extension->getSeason() as $s) {
				if (empty($seasons[ $s->getId() ]['entity'])) {
					$seasons[ $s->getId() ]['entity'] = $s;
					$seasons[ $s->getId() ]['count'] = 0;
				}
				$seasons[ $s->getId() ]['count']++;
				if (in_array($s->getId(),array_keys($filtered_seasons)) && empty($filtered_seasons[ $s->getId() ])) {
					$filtered_seasons[ $s->getId() ] = $s;
				}
			}

			if (!empty($extension->getAmountDays())) {
				if (empty($days[$extension->getAmountDays()])) {
					$days[$extension->getAmountDays()] = 0;
				}
				$days[$extension->getAmountDays()]++;
			}

			if (!empty($extension->getStartingPoint())) {
				if (empty($destinations_from[ $extension->getStartingPoint()->getId() ]['entity'])) {
					$destinations_from[ $extension->getStartingPoint()->getId() ]['entity'] = $extension->getStartingPoint();
					$destinations_from[ $extension->getStartingPoint()->getId() ]['count'] = 0;
				}
				$destinations_from[ $extension->getStartingPoint()->getId() ]['count']++;
				if (in_array($extension->getStartingPoint()->getId(),array_keys($filtered_destinations_from)) && empty($filtered_destinations_from[ $extension->getStartingPoint()->getId() ])) {
					$filtered_destinations_from[ $extension->getStartingPoint()->getId() ] = $extension->getStartingPoint();
				}
			}

		}

		if (!empty($filtered_categories)) {
			foreach($filtered_categories as $k => $v) {
				$filtered_categories[$k] = $categories[$k];
			}
		}

		if (!empty($filtered_days)) {
			foreach($filtered_days as $k => $v) {
				$filtered_days[$k] = $v;
			}
		}

		$data['current_path'] = $filters;


		$data['filtered_destinations'] = $filtered_destinations;
		$data['filtered_categories'] = $filtered_categories;
		$data['filtered_recreations'] = $filtered_recreations;
		$data['filtered_seasons'] = $filtered_seasons;
		$data['filtered_days'] = $filtered_days;
		$data['filtered_destinations_from'] = $filtered_destinations_from;

		$extensions_filtered = $em->getRepository('AppBundle\Entity\Hotel')->findAllExtensions($em,$request,$data);

		//CITIES TAKEN FROM ALL
		/*foreach($extensions_filtered as $extension) {

			if (!empty($extension->isMiniGroupe())) {
				$categories[1]['count']++;
			} else {
				$categories[0]['count']++;
			}

			foreach($extension->getExtensionRecreation() as $s) {
				if (empty($recreations[ $s->getId() ]['entity'])) {
					$recreations[ $s->getId() ]['entity'] = $s;
					$recreations[ $s->getId() ]['count'] = 0;
				}
				$recreations[ $s->getId() ]['count']++;
			}

			foreach($extension->getSeason() as $s) {
				if (empty($seasons[ $s->getId() ]['entity'])) {
					$seasons[ $s->getId() ]['entity'] = $s;
					$seasons[ $s->getId() ]['count'] = 0;
				}
				$seasons[ $s->getId() ]['count']++;
			}

			if (!empty($extension->getAmountDays())) {
				if (empty($days[$extension->getAmountDays()])) {
					$days[$extension->getAmountDays()] = 0;
				}
				$days[$extension->getAmountDays()]++;
			}

			if (!empty($extension->getStartingPoint())) {
				if (empty($destinations_from[ $extension->getStartingPoint()->getId() ]['entity'])) {
					$destinations_from[ $extension->getStartingPoint()->getId() ]['entity'] = $extension->getStartingPoint();
					$destinations_from[ $extension->getStartingPoint()->getId() ]['count'] = 0;
				}
				$destinations_from[ $extension->getStartingPoint()->getId() ]['count']++;
			}
		}*/

		usort($destinations, function($a, $b) {
			return $b['count'] - $a['count'];
		});
		$data['filter_destinations'] = $destinations;
		$data['filter_categories'] = $categories;
		usort($recreations, function($a, $b) {
			return $b['count'] - $a['count'];
		});
		$data['filter_recreations'] = $recreations;
		usort($seasons, function($a, $b) {
			return $b['count'] - $a['count'];
		});
		$data['filter_seasons'] = $seasons;
		ksort($days);
		$data['filter_days'] = $days;
		usort($destinations_from, function($a, $b) {
			return $b['count'] - $a['count'];
		});
		$data['filter_destinations_from'] = $destinations_from;




		$page = !empty($request->query->get('page')) ? intval($request->query->get('page')) : 1;
		$paginator = array(
			'currentFilters' => array(
				'filters' => $data['current_path']
			),
			'currentPage' => $page,
			'paginationPath' => 'home_extension_filter_'.$request->getLocale(),
			'showAlwaysFirstAndLast' => true,
			'lastPage' => ceil(count($extensions_filtered) / $this->_itemsOnPage),
		);


		$data['extensions_count_total'] = count($extensions_filtered);
		$data['filters_active_count'] = count($filtered_destinations) + count($filtered_categories) + count($filtered_recreations) + count($filtered_seasons) + count($filtered_days) + count($filtered_destinations_from);

		$data['extensions'] = array_slice($extensions_filtered, ($page * $this->_itemsOnPage - $this->_itemsOnPage), $this->_itemsOnPage);
		$data = array_merge($data,$paginator);


//echo'<pre>'; print_r(get_class_methods($data['hotels'][0]->getHotelStars()->toArray())); exit;
		return $this->render('default/extension.html.twig', $data);
    }



	public function extensionAction(Request $request, $slug = '')
	{

		$data = array();
		$em = $this->getDoctrine()->getManager();

		$extension = $em->getRepository('AppBundle\Entity\Extension')->findOneExtensionBySlug($em,$request,$slug);


		if (empty($slug) or empty($extension)) {
			throw new NotFoundHttpException('404 not found');
		}
		$data['extension'] = $extension;

		$breadcrumbs = array();
		$breadcrumbs[$this->get('translator')->trans('front.homepage')] = $this->generateUrl('homepage');
		$breadcrumbs[$this->get('translator')->trans('front.extension')] = $this->generateUrl('home_extension_'.$request->getLocale());
		//$breadcrumbs[$extension->translate()->getName()] = '';
		$data['breadcrumbs'] = $breadcrumbs;

		$bookingForm = new Forms($em,$this->get('translator'),$request->getLocale(),$this->getUser());
		$include = array('date_apart','hebergement','combien','supplement','precisions');
		$data['extension_form'] = $bookingForm->getBookingForm($extension,$include);

		return $this->render('default/extension_detail.html.twig', $data);

	}

}
