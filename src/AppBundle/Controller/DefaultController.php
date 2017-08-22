<?php

namespace AppBundle\Controller;


use AppBundle\Entity\BookDomain;
use AppBundle\Service\Forms;
use AppBundle\Service\Locales;
use AppBundle\Service\Search;
use DateTime;
use Gedmo\Mapping\Annotation\Locale;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Intl\Intl;


class DefaultController extends Controller
{

	private $_locales;
	private $_defaultLocale;

	private $_eventsOnHomepage1 = 3;
	private $_eventsOnHomepage2 = 6;

	private $_itemsOnPage = 10;

	public function __construct()
	{
		//We need locales everywhere in code
		//Проставляем локали
		$loc = new Locales();
		$this->_locales = $loc->getLocales();
		$this->_defaultLocale = $loc->getDefaultLocale();
	}

    /**
     * @Route("/", name="homepage_old")
     */
    public function indexOldAction(Request $request)
    {

    }

	/**
	 * @Route("/{_locale}", requirements={"_locale" = "%app.locales%"}, name="homepage")
	 */
	public function indexAction(Request $request){
		$data = array();
		$em = $this->getDoctrine()->getManager();

		if ($request->getSession()->get($this->getParameter('domain.variable.name')) == $this->getParameter('domain.russian')) {
			return $this->redirectToRoute('switch_ru_'.$request->getLocale(), array());
		}

		$data['heart_slider'] = $em->getRepository('AppBundle\Entity\Voyage')->findAllVoyagesOnFrontPage($em,$request);
		$data['minigroup_slider'] = $em->getRepository('AppBundle\Entity\Voyage')->findAllVoyagesOnFrontMinigroup($em,$request);

		// replace this example code with whatever you need
		return $this->render('default/index.html.twig',$data);
	}


	public function indexInternationalAction(Request $request){
		$request->getSession()->set(
			$this->getParameter('domain.variable.name'), $this->getParameter('domain.international')
		);

		return $this->redirectToRoute('homepage', array());
	}

	public function indexRussianAction(Request $request){
		$request->getSession()->set(
			$this->getParameter('domain.variable.name'), $this->getParameter('domain.russian')
		);


		$data = array();
		$em = $this->getDoctrine()->getManager();

		$events = $em->getRepository('AppBundle\Entity\Event')->findAllEvents($em,$request);

		$services = array();
		$months = array();
		$dates = array();

		foreach($events as $event) {

			if (!empty($event->getEventType())) {
				if (empty($services[ $event->getEventType()->getId() ]['entity'])) {
					$services[ $event->getEventType()->getId() ]['entity'] = $event->getEventType();
					$services[ $event->getEventType()->getId() ]['count'] = 0;
				}
				$services[ $event->getEventType()->getId() ]['count']++;
			}

			foreach($event->getEventSchedule() as $s) {
				if (empty($months[$s->getMonthStart()])) {
					$months[$s->getMonthStart()] = 0;
				}
				$months[$s->getMonthStart()]++;
				if (empty($months[$s->getMonthStart(true)])) {
					$dates[$s->getMonthStart(true)]['types'][] = $event->getEventType()->translate($this->_defaultLocale)->getName();
					$dates[$s->getMonthStart(true)]['count'] = 0;
				}
				$dates[$s->getMonthStart(true)]['count']++;
			}

		}

		$data['current_path'] = '';
		$data['filter_services'] = $services;

		ksort($months);
		$data['filter_months'] = $months;

		ksort($dates);
		$data['filter_dates'] = $dates;


		$askedMonth = $request->query->get($this->get('translator')->trans('url.agenda.calendar'),'');
		if (!empty($askedMonth)) {
			$askedMonth = explode('-',$askedMonth);
			$year = $askedMonth[0];
			$month = $askedMonth[1];
		} else {
			$year = date('Y');
			$month = date('m');
		}


		$selectedMonth = new DateTime();
		$selectedMonth->setDate($year,$month,1 );
		$data['selected_month'] = $selectedMonth;
		$data['today_month'] = new DateTime();

		$today = new DateTime();

		$calendarDateStart = new DateTime();
		$calendarDateStart->setDate($year,$month,1 );
		$daysInMonth = date("t",$calendarDateStart->getTimestamp());
		$calendarDateStart->modify('monday this week');

		$calendarDateEnd = new DateTime();
		$calendarDateEnd->setDate($year,$month,$daysInMonth );
		$calendarDateEnd->modify('sunday this week');

		$data['calendar_month_days'] = array();
		$proceed = true;

		while ($proceed) {
			$sDate = new DateTime();
			$sDate->setDate($calendarDateStart->format('Y'),$calendarDateStart->format('m'),$calendarDateStart->format('d') );
			$data['calendar_month_days'][] = array(
				'day' => $calendarDateStart->format('j'),
				'month' => $calendarDateStart->format('m'),
				'year' => $calendarDateStart->format('Y'),
				'date' => $sDate,
				'active' => ($calendarDateStart->format('m') == $month) ? true : false,
				'hasEvent' => in_array($calendarDateStart->format('Y-m-d'),array_keys($dates)) ? true : false,
				'hasEventTypes' => in_array($calendarDateStart->format('Y-m-d'),array_keys($dates)) ? array_unique($dates[$calendarDateStart->format('Y-m-d')]['types']) : array(),
				'today' => $calendarDateStart->format('Y-m-d') == $today->format('Y-m-d') ? true : false
			);

			$calendarDateStart->modify('+1 day');
			if ($calendarDateStart > $calendarDateEnd) {
				$proceed = false;
			}
		}

		$data['events_count_total'] = count($events);
		$data['filters_active_count'] = 0;

		$data['filtered_services'] = array();
		$data['filtered_months'] = array();
		$data['filtered_dates'] = array();


		$data['events1'] = array_slice($events, 0, $this->_eventsOnHomepage1);
		$data['events2'] = array_slice($events, 0, $this->_eventsOnHomepage2);

		$data['heart_slider'] = $em->getRepository('AppBundle\Entity\Voyage')->findAllVoyagesOnFrontPage($em,$request);

		$subscribeForm = new Forms($em,$this->get('translator'),$request->getLocale(),$this->getUser());
		$data['subscribe_form'] = $subscribeForm->getSubscribeForm();


		return $this->render('default/index_event.html.twig', $data);
	}


	public function articleAction(Request $request, $slug = ''){
		$em = $this->getDoctrine()->getManager();
		$slug = $this->get('translator')->trans('url.static').'-'.$slug;

		if ($slug) {
			$entity = $em->getRepository('AppBundle\Entity\Article')->findOneStaticArticle($em,$request,$slug);
		}

		if (empty($entity)) {
			throw new NotFoundHttpException('404 not found');
		}

		$data = array();

		$data['article'] = $entity;

		$breadcrumbs = array();
		$breadcrumbs[$this->get('translator')->trans('front.homepage')] = $this->generateUrl('homepage');
		$breadcrumbs[$entity->translate()->getName()] = '';
		$data['breadcrumbs'] = $breadcrumbs;

		$data['showShareBlock'] = 0;

		return $this->render('default/article.html.twig', $data);

	}

	public function searchAction(Request $request,$filters = ''){
		$em = $this->getDoctrine()->getManager();
		$searchString = trim($request->query->get('search',''));
		if (empty($searchString)) {
			throw new NotFoundHttpException('404 not found');
		}
		$data = array();

		$sphinx = new Search($this->get('iakumai.sphinxsearch.search'),$em,$this->get('translator'));
		$searchResult = $sphinx->searchAll($searchString);
		$searchResultFiltered = $searchResult;

		$filtered_types = array();

		if ($filters) {
			$filtersArray = explode('/',$filters);
			$key = null;
			foreach($filtersArray as $k => $f) {
				if ($k % 2 == 0) {
					$key = $f;
				} else {
					if ($key == $this->get('translator')->trans('url.search.type')) {
						$filtered_types[$f] = null;
					}
					$key = null;
				}
			}
		}

		$types = array();

		foreach($searchResult as $key => $entity) {
			if (empty($types[$entity['type']])) {
				$types[$entity['type']] = 0;
			}
			$types[$entity['type']]++;


			if(!empty($filtered_types)) {
				if (!in_array($entity['type'],array_keys($filtered_types))) {
					unset($searchResultFiltered[$key]);
				}
			}
		}



		$data['searchString'] = $searchString;
		$data['filters_active_count'] = count($filtered_types);

		$data['search_count_total'] = count($searchResultFiltered);

		$data['current_path'] = $filters;

		$data['filter_types'] = $types;

		$data['filtered_types'] = $filtered_types;

		$page = !empty($request->query->get('page')) ? intval($request->query->get('page')) : 1;

		$routerRequest = $request->query->all();
		unset($routerRequest['page']);

		$paginator = array(
			'currentFilters' => array_merge($routerRequest,array('filters' => $data['current_path'])),
			'currentPage' => $page,
			'paginationPath' => 'home_search_'.$request->getLocale(),
			'showAlwaysFirstAndLast' => true,
			'lastPage' => ceil(count($searchResultFiltered) / $this->_itemsOnPage),
		);

		$data['search'] = array_slice($searchResultFiltered, ($page * $this->_itemsOnPage - $this->_itemsOnPage), $this->_itemsOnPage);
		$data = array_merge($data,$paginator);

		return $this->render('default/search.html.twig', $data);

	}

	public function errorAction(Request $request){
		$data = array();
		$breadcrumbs = array();
		$breadcrumbs[$this->get('translator')->trans('front.homepage')] = $this->generateUrl('homepage');
		$breadcrumbs[$this->get('translator')->trans('front.errorpage')] = '';
		$data['breadcrumbs'] = $breadcrumbs;
		$data['showShareBlock'] = 0;
		$data['showImageBlock'] = 0;
		$data['showPrintBlock'] = 0;
		$data['error_url'] = $request->getUri();

		return $this->render('default/error.html.twig', $data);
	}
}