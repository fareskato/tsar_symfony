<?php

namespace AppBundle\Controller;


use AppBundle\Entity\BookDomain;
use AppBundle\Entity\Date;
use AppBundle\Service\Forms;
use AppBundle\Service\Locales;
use DateTime;
use Gedmo\Mapping\Annotation\Locale;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Intl\Intl;


class AgendaController extends Controller
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

		$events = $em->getRepository('AppBundle\Entity\Event')->findAllEvents($em,$request);

		$filtered_services = array();
		$filtered_months = array();
		$filtered_dates = array();

		if ($filters) {
			$filtersArray = explode('/',$filters);
			$key = null;
			foreach($filtersArray as $k => $f) {
				if ($k % 2 == 0) {
					$key = $f;
				} else {
					if ($key == $this->get('translator')->trans('url.agenda.service')) {
						$filtered_services[$f] = null;
					} else if ($key == $this->get('translator')->trans('url.agenda.month')) {
						$filtered_months[$f] = null;
					} else if ($key == $this->get('translator')->trans('url.agenda.date')) {
						$filtered_dates[$f] = null;
					}
					$key = null;
				}
			}
		}

		$services = array();
		$months = array();
		$dates = array();


		//CITIES TAKEN FROM ALL
		foreach($events as $event) {

			if (!empty($event->getEventType())) {
				if (empty($services[ $event->getEventType()->getId() ]['entity'])) {
					$services[ $event->getEventType()->getId() ]['entity'] = $event->getEventType();
					$services[ $event->getEventType()->getId() ]['count'] = 0;
				}
				$services[ $event->getEventType()->getId() ]['count']++;
				if (in_array($event->getEventType()->getId(),array_keys($filtered_services)) && empty($filtered_services[ $event->getEventType()->getId() ])) {
					$filtered_services[ $event->getEventType()->getId() ] = $event->getEventType();
				}
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

		if (!empty($filtered_months)) {
			foreach($filtered_months as $k => $v) {
				$filtered_months[$k] = $v;
			}
		}

		$data['current_path'] = $filters;

		$data['filtered_services'] = $filtered_services;
		ksort($filtered_months);
		$data['filtered_months'] = $filtered_months;
		ksort($filtered_dates);
		$data['filtered_dates'] = $filtered_dates;

		$events_filtered = $em->getRepository('AppBundle\Entity\Hotel')->findAllEvents($em,$request,$data);

		usort($services, function($a, $b) {
			return $b['count'] - $a['count'];
		});
		$data['filter_services'] = $services;

		ksort($months);
		$data['filter_months'] = $months;

		ksort($dates);
		$data['filter_dates'] = $dates;


		$page = !empty($request->query->get('page')) ? intval($request->query->get('page')) : 1;
		$paginator = array(
			'currentFilters' => array(
				'filters' => $data['current_path']
			),
			'currentPage' => $page,
			'paginationPath' => 'home_agenda_filter_'.$request->getLocale(),
			'showAlwaysFirstAndLast' => true,
			'lastPage' => ceil(count($events_filtered) / $this->_itemsOnPage),
		);

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

		$data['events_count_total'] = count($events_filtered);
		$data['filters_active_count'] = count($filtered_services) + count($filtered_months) + count($filtered_dates);

		$data['events'] = array_slice($events_filtered, ($page * $this->_itemsOnPage - $this->_itemsOnPage), $this->_itemsOnPage);
		$data = array_merge($data,$paginator);


//echo'<pre>'; print_r(get_class_methods($data['hotels'][0]->getHotelStars()->toArray())); exit;
		return $this->render('default/event.html.twig', $data);
    }



	public function eventAction(Request $request, $slug = '')
	{

		$data = array();
		$em = $this->getDoctrine()->getManager();

		$event = $em->getRepository('AppBundle\Entity\Event')->findOneEventBySlug($em,$request,$slug);

		if (empty($slug) or empty($event)) {
			throw new NotFoundHttpException('404 not found');
		}
		$data['event'] = $event;

		$breadcrumbs = array();
		$breadcrumbs[$this->get('translator')->trans('front.homepage')] = $this->generateUrl('homepage');
		$breadcrumbs[$this->get('translator')->trans('front.agenda')] = $this->generateUrl('home_agenda_'.$request->getLocale());
		//$breadcrumbs[$extension->translate()->getName()] = '';
		$data['breadcrumbs'] = $breadcrumbs;

		$bookingForm = new Forms($em,$this->get('translator'),$request->getLocale(),$this->getUser());
		$include = array('number','precisions');
		$data['event_form'] = $bookingForm->getBookingForm($event,$include);

		return $this->render('default/event_detail.html.twig', $data);

	}

}
