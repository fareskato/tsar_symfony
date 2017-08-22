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


class VisitsController extends Controller
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

		$visits = $em->getRepository('AppBundle\Entity\Visit')->findAllVisits($em,$request);

		$filtered_destinations = array();
		$filtered_visit_types = array();
		$filtered_seasons = array();
		$filtered_hours = array();


		if ($filters) {
			$filtersArray = explode('/',$filters);
			$key = null;
			foreach($filtersArray as $k => $f) {
				if ($k % 2 == 0) {
					$key = $f;
				} else {
					if ($key == $this->get('translator')->trans('url.visit.destination')) {
						$filtered_destinations[$f] = null;
					} else if ($key == $this->get('translator')->trans('url.visit.season')) {
						$filtered_seasons[$f] = null;
					} else if ($key == $this->get('translator')->trans('url.visit.hours')) {
						$filtered_hours[$f] = null;
					} else if ($key == $this->get('translator')->trans('url.visit.type')) {
						$filtered_visit_types[$f] = null;
					}
					$key = null;
				}
			}
		}

		$destinations = array();
		$seasons = array();
		$days = array();
		$visit_types = array();


		//CITIES TAKEN FROM ALL
		foreach($visits as $visit) {

			foreach($visit->getTravelPoints() as $d) {
				if (empty($destinations[ $d->getId() ]['entity'])) {
					$destinations[ $d->getId() ]['entity'] = $d;
					$destinations[ $d->getId() ]['count'] = 0;
				}
				$destinations[ $d->getId() ]['count']++;
				if (in_array($d->getId(),array_keys($filtered_destinations)) && empty($filtered_destinations[ $d->getId() ])) {
					$filtered_destinations[ $d->getId() ] = $d;
				}
			}

			if (!empty($visit->getVisitDuration())) {
				if (empty($visit_types[ $visit->getVisitDuration()->getId() ]['entity'])) {
					$visit_types[ $visit->getVisitDuration()->getId() ]['entity'] = $visit->getVisitDuration();
					$visit_types[ $visit->getVisitDuration()->getId() ]['count'] = 0;
				}
				$visit_types[ $visit->getVisitDuration()->getId() ]['count']++;
				if (in_array($visit->getVisitDuration()->getId(),array_keys($filtered_visit_types)) && empty($filtered_visit_types[ $visit->getVisitDuration()->getId() ])) {
					$filtered_visit_types[ $visit->getVisitDuration()->getId() ] = $visit->getVisitDuration();
				}
			}

			foreach($visit->getSeason() as $s) {
				if (empty($seasons[ $s->getId() ]['entity'])) {
					$seasons[ $s->getId() ]['entity'] = $s;
					$seasons[ $s->getId() ]['count'] = 0;
				}
				$seasons[ $s->getId() ]['count']++;
				if (in_array($s->getId(),array_keys($filtered_seasons)) && empty($filtered_seasons[ $s->getId() ])) {
					$filtered_seasons[ $s->getId() ] = $s;
				}
			}

			if (!empty($visit->getNumberHoursVisit())) {
				if (empty($days[$visit->getNumberHoursVisit()])) {
					$days[$visit->getNumberHoursVisit()] = 0;
				}
				$days[$visit->getNumberHoursVisit()]++;
			}

		}

		if (!empty($filtered_hours)) {
			foreach($filtered_hours as $k => $v) {
				$filtered_hours[$k] = $v;
			}
		}

		$data['current_path'] = $filters;


		$data['filtered_destinations'] = $filtered_destinations;
		$data['filtered_seasons'] = $filtered_seasons;
		$data['filtered_hours'] = $filtered_hours;
		$data['filtered_visit_types'] = $filtered_visit_types;

		$visits_filtered = $em->getRepository('AppBundle\Entity\Hotel')->findAllVisits($em,$request,$data);

		//CITIES TAKEN FROM ALL
		/*foreach($voayges_filtered as $voayge) {

			if (!empty($visit->isMiniGroupe())) {
				$categories[1]['count']++;
			} else {
				$categories[0]['count']++;
			}

			foreach($visit->getVoyageRecreation() as $s) {
				if (empty($recreations[ $s->getId() ]['entity'])) {
					$recreations[ $s->getId() ]['entity'] = $s;
					$recreations[ $s->getId() ]['count'] = 0;
				}
				$recreations[ $s->getId() ]['count']++;
			}

			if (!empty($visit->getVisitDuration())) {
				if (empty($visit_types[ $visit->getVisitDuration()->getId() ]['entity'])) {
					$visit_types[ $visit->getVisitDuration()->getId() ]['entity'] = $visit->getVisitDuration();
					$visit_types[ $visit->getVisitDuration()->getId() ]['count'] = 0;
				}
				$visit_types[ $visit->getVisitDuration()->getId() ]['count']++;
			}

			foreach($visit->getSeason() as $s) {
				if (empty($seasons[ $s->getId() ]['entity'])) {
					$seasons[ $s->getId() ]['entity'] = $s;
					$seasons[ $s->getId() ]['count'] = 0;
				}
				$seasons[ $s->getId() ]['count']++;
			}

			if (!empty($visit->getAmountDays())) {
				if (empty($days[$visit->getAmountDays()])) {
					$days[$visit->getAmountDays()] = 0;
				}
				$days[$visit->getAmountDays()]++;
			}

			if (!empty($visit->getStartingPoint())) {
				if (empty($destinations_from[ $visit->getStartingPoint()->getId() ]['entity'])) {
					$destinations_from[ $visit->getStartingPoint()->getId() ]['entity'] = $visit->getStartingPoint();
					$destinations_from[ $visit->getStartingPoint()->getId() ]['count'] = 0;
				}
				$destinations_from[ $visit->getStartingPoint()->getId() ]['count']++;
			}
		}*/

		usort($destinations, function($a, $b) {
			return $b['count'] - $a['count'];
		});
		$data['filter_destinations'] = $destinations;

		usort($seasons, function($a, $b) {
			return $b['count'] - $a['count'];
		});
		$data['filter_seasons'] = $seasons;
		ksort($days);
		$data['filter_hours'] = $days;

		usort($visit_types, function($a, $b) {
			return $b['count'] - $a['count'];
		});
		$data['filter_visit_types'] = $visit_types;




		$page = !empty($request->query->get('page')) ? intval($request->query->get('page')) : 1;
		$paginator = array(
			'currentFilters' => array(
				'filters' => $data['current_path']
			),
			'currentPage' => $page,
			'paginationPath' => 'home_visit_filter_'.$request->getLocale(),
			'showAlwaysFirstAndLast' => true,
			'lastPage' => ceil(count($visits_filtered) / $this->_itemsOnPage),
		);


		$data['visits_count_total'] = count($visits_filtered);
		$data['filters_active_count'] = count($filtered_destinations) + count($filtered_seasons) + count($filtered_hours) + count($filtered_visit_types);

		$data['visits'] = array_slice($visits_filtered, ($page * $this->_itemsOnPage - $this->_itemsOnPage), $this->_itemsOnPage);
		$data = array_merge($data,$paginator);


//echo'<pre>'; print_r(get_class_methods($data['hotels'][0]->getHotelStars()->toArray())); exit;
		return $this->render('default/visit.html.twig', $data);
    }



	public function visitAction(Request $request, $slug = '')
	{

		$data = array();
		$em = $this->getDoctrine()->getManager();

		$visit = $em->getRepository('AppBundle\Entity\Visit')->findOneVisitBySlug($em,$request,$slug);


		if (empty($slug) or empty($visit)) {
			throw new NotFoundHttpException('404 not found');
		}
		$data['visit'] = $visit;

		$breadcrumbs = array();
		$breadcrumbs[$this->get('translator')->trans('front.homepage')] = $this->generateUrl('homepage');
		$breadcrumbs[$this->get('translator')->trans('front.visit')] = $this->generateUrl('home_visit_'.$request->getLocale());
		if ($visit->getTravelPoints()) {
			$breadcrumbs[$visit->getTravelPoints()[0]->translate()->getName()] = $this->generateUrl('home_visit_filter_'.$request->getLocale(),
				array(
					'filters' => $this->get('translator')->trans('url.visit.destination') .'/'. $visit->getTravelPoints()[0]->translate()->getId()
				)
			);
		}
		$data['breadcrumbs'] = $breadcrumbs;

		$bookingForm = new Forms($em,$this->get('translator'),$request->getLocale(),$this->getUser());
		$include = array('date_apart','combien','precisions');
		$data['visit_form'] = $bookingForm->getBookingForm($visit,$include);

		return $this->render('default/visit_detail.html.twig', $data);

	}

}
