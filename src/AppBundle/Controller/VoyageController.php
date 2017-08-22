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


class VoyageController extends Controller
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

		$voayges = $em->getRepository('AppBundle\Entity\Voyage')->findAllVoyages($em,$request);

		$filtered_destinations = array();
		$filtered_categories = array();
		$filtered_recreations = array();
		$filtered_seasons = array();
		$filtered_days = array();
		$filtered_destinations_from = array();
		$filtered_voyage_types = array();

		if ($filters) {
			$filtersArray = explode('/',$filters);
			$key = null;
			foreach($filtersArray as $k => $f) {
				if ($k % 2 == 0) {
					$key = $f;
				} else {
					if ($key == $this->get('translator')->trans('url.voyage.destination')) {
						$filtered_destinations[$f] = null;
					} else if ($key == $this->get('translator')->trans('url.voyage.category')) {
						$filtered_categories[$f] = null;
					} else if ($key == $this->get('translator')->trans('url.voyage.recreation')) {
						$filtered_recreations[$f] = null;
					} else if ($key == $this->get('translator')->trans('url.voyage.season')) {
						$filtered_seasons[$f] = null;
					} else if ($key == $this->get('translator')->trans('url.voyage.days')) {
						$filtered_days[$f] = null;
					} else if ($key == $this->get('translator')->trans('url.voyage.from')) {
						$filtered_destinations_from[$f] = null;
					} else if ($key == $this->get('translator')->trans('url.voyage.type')) {
						$filtered_voyage_types[$f] = null;
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
		$voayge_types = array();


		//CITIES TAKEN FROM ALL
		foreach($voayges as $voayge) {

			foreach($voayge->getRelatedContent() as $d) {
				if (empty($destinations[ $d->getId() ]['entity'])) {
					$destinations[ $d->getId() ]['entity'] = $d;
					$destinations[ $d->getId() ]['count'] = 0;
				}
				$destinations[ $d->getId() ]['count']++;
				if (in_array($d->getId(),array_keys($filtered_destinations)) && empty($filtered_destinations[ $d->getId() ])) {
					$filtered_destinations[ $d->getId() ] = $d;
				}
			}

			if (!empty($voayge->isMiniGroupe())) {
				$categories[1]['count']++;
			} else {
				$categories[0]['count']++;
			}

			foreach($voayge->getVoyageRecreation() as $s) {
				if (empty($recreations[ $s->getId() ]['entity'])) {
					$recreations[ $s->getId() ]['entity'] = $s;
					$recreations[ $s->getId() ]['count'] = 0;
				}
				$recreations[ $s->getId() ]['count']++;
				if (in_array($s->getId(),array_keys($filtered_recreations)) && empty($filtered_recreations[ $s->getId() ])) {
					$filtered_recreations[ $s->getId() ] = $s;
				}
			}

			if (!empty($voayge->getTypeVoyage())) {
				if (empty($voayge_types[ $voayge->getTypeVoyage()->getId() ]['entity'])) {
					$voayge_types[ $voayge->getTypeVoyage()->getId() ]['entity'] = $voayge->getTypeVoyage();
					$voayge_types[ $voayge->getTypeVoyage()->getId() ]['count'] = 0;
				}
				$voayge_types[ $voayge->getTypeVoyage()->getId() ]['count']++;
				if (in_array($voayge->getTypeVoyage()->getId(),array_keys($filtered_voyage_types)) && empty($filtered_voyage_types[ $voayge->getTypeVoyage()->getId() ])) {
					$filtered_voyage_types[ $voayge->getTypeVoyage()->getId() ] = $voayge->getTypeVoyage();
				}
			}

			foreach($voayge->getSeason() as $s) {
				if (empty($seasons[ $s->getId() ]['entity'])) {
					$seasons[ $s->getId() ]['entity'] = $s;
					$seasons[ $s->getId() ]['count'] = 0;
				}
				$seasons[ $s->getId() ]['count']++;
				if (in_array($s->getId(),array_keys($filtered_seasons)) && empty($filtered_seasons[ $s->getId() ])) {
					$filtered_seasons[ $s->getId() ] = $s;
				}
			}

			if (!empty($voayge->getAmountDays())) {
				if (empty($days[$voayge->getAmountDays()])) {
					$days[$voayge->getAmountDays()] = 0;
				}
				$days[$voayge->getAmountDays()]++;
			}

			if (!empty($voayge->getStartingPoint())) {
				if (empty($destinations_from[ $voayge->getStartingPoint()->getId() ]['entity'])) {
					$destinations_from[ $voayge->getStartingPoint()->getId() ]['entity'] = $voayge->getStartingPoint();
					$destinations_from[ $voayge->getStartingPoint()->getId() ]['count'] = 0;
				}
				$destinations_from[ $voayge->getStartingPoint()->getId() ]['count']++;
				if (in_array($voayge->getStartingPoint()->getId(),array_keys($filtered_destinations_from)) && empty($filtered_destinations_from[ $voayge->getStartingPoint()->getId() ])) {
					$filtered_destinations_from[ $voayge->getStartingPoint()->getId() ] = $voayge->getStartingPoint();
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
		$data['filtered_voyage_types'] = $filtered_voyage_types;

		$voayges_filtered = $em->getRepository('AppBundle\Entity\Hotel')->findAllVoyages($em,$request,$data);

		//CITIES TAKEN FROM ALL
		/*foreach($voayges_filtered as $voayge) {

			if (!empty($voayge->isMiniGroupe())) {
				$categories[1]['count']++;
			} else {
				$categories[0]['count']++;
			}

			foreach($voayge->getVoyageRecreation() as $s) {
				if (empty($recreations[ $s->getId() ]['entity'])) {
					$recreations[ $s->getId() ]['entity'] = $s;
					$recreations[ $s->getId() ]['count'] = 0;
				}
				$recreations[ $s->getId() ]['count']++;
			}

			if (!empty($voayge->getTypeVoyage())) {
				if (empty($voayge_types[ $voayge->getTypeVoyage()->getId() ]['entity'])) {
					$voayge_types[ $voayge->getTypeVoyage()->getId() ]['entity'] = $voayge->getTypeVoyage();
					$voayge_types[ $voayge->getTypeVoyage()->getId() ]['count'] = 0;
				}
				$voayge_types[ $voayge->getTypeVoyage()->getId() ]['count']++;
			}

			foreach($voayge->getSeason() as $s) {
				if (empty($seasons[ $s->getId() ]['entity'])) {
					$seasons[ $s->getId() ]['entity'] = $s;
					$seasons[ $s->getId() ]['count'] = 0;
				}
				$seasons[ $s->getId() ]['count']++;
			}

			if (!empty($voayge->getAmountDays())) {
				if (empty($days[$voayge->getAmountDays()])) {
					$days[$voayge->getAmountDays()] = 0;
				}
				$days[$voayge->getAmountDays()]++;
			}

			if (!empty($voayge->getStartingPoint())) {
				if (empty($destinations_from[ $voayge->getStartingPoint()->getId() ]['entity'])) {
					$destinations_from[ $voayge->getStartingPoint()->getId() ]['entity'] = $voayge->getStartingPoint();
					$destinations_from[ $voayge->getStartingPoint()->getId() ]['count'] = 0;
				}
				$destinations_from[ $voayge->getStartingPoint()->getId() ]['count']++;
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
		usort($voayge_types, function($a, $b) {
			return $b['count'] - $a['count'];
		});
		$data['filter_voyage_types'] = $voayge_types;




		$page = !empty($request->query->get('page')) ? intval($request->query->get('page')) : 1;
		$paginator = array(
			'currentFilters' => array(
				'filters' => $data['current_path']
			),
			'currentPage' => $page,
			'paginationPath' => 'home_voyage_filter_'.$request->getLocale(),
			'showAlwaysFirstAndLast' => true,
			'lastPage' => ceil(count($voayges_filtered) / $this->_itemsOnPage),
		);


		$data['voyages_count_total'] = count($voayges_filtered);
		$data['filters_active_count'] = count($filtered_destinations) + count($filtered_categories) + count($filtered_recreations) + count($filtered_seasons) + count($filtered_days) + count($filtered_destinations_from) + count($filtered_voyage_types);

		$data['voyages'] = array_slice($voayges_filtered, ($page * $this->_itemsOnPage - $this->_itemsOnPage), $this->_itemsOnPage);
		$data = array_merge($data,$paginator);


//echo'<pre>'; print_r(get_class_methods($data['hotels'][0]->getHotelStars()->toArray())); exit;
		return $this->render('default/voyage.html.twig', $data);
    }



	public function voyageAction(Request $request, $slug = '')
	{

		$data = array();
		$em = $this->getDoctrine()->getManager();

		$voyage = $em->getRepository('AppBundle\Entity\Voyage')->findOneVoyageBySlug($em,$request,$slug);


		if (empty($slug) or empty($voyage)) {
			throw new NotFoundHttpException('404 not found');
		}
		$data['voyage'] = $voyage;

		$breadcrumbs = array();
		$breadcrumbs[$this->get('translator')->trans('front.homepage')] = $this->generateUrl('homepage');
		$breadcrumbs[$this->get('translator')->trans('front.voyage')] = $this->generateUrl('home_voyage_'.$request->getLocale());
		//$breadcrumbs[$extension->translate()->getName()] = '';
		$data['breadcrumbs'] = $breadcrumbs;


		$bookingForm = new Forms($em,$this->get('translator'),$request->getLocale(),$this->getUser());
		$include = array('date_apart','hebergement','combien','supplement','visa','services','flight','precisions');
		$data['voyage_form'] = $bookingForm->getBookingForm($voyage,$include);

		return $this->render('default/voyage_detail.html.twig', $data);

	}

}
