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


class HebergementController extends Controller
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

		$hotels = $em->getRepository('AppBundle\Entity\Hotel')->findAllHotels($em,$request);

		$filtered_destinations = array();
		$filtered_stars = array();

		if ($filters) {
			$filtersArray = explode('/',$filters);
			$key = null;
			foreach($filtersArray as $k => $f) {
				if ($k % 2 == 0) {
					$key = $f;
				} else {
					if ($key == $this->get('translator')->trans('url.hotel.destination')) {
						$filtered_destinations[$f] = null;
					} else if ($key == $this->get('translator')->trans('url.hotel.stars')) {
						$filtered_stars[$f] = null;
					}
					$key = null;
				}
			}
		}

		$destinations = array();
		$stars = array();

		foreach($hotels as $hotel) {
			foreach($hotel->getDestination() as $d) {
				if (empty($destinations[ $d->getId() ]['entity'])) {
					$destinations[ $d->getId() ]['entity'] = $d;
					$destinations[ $d->getId() ]['count'] = 0;
				}
				$destinations[ $d->getId() ]['count']++;
				if (in_array($d->getId(),array_keys($filtered_destinations)) && empty($filtered_destinations[ $d->getId() ])) {
					$filtered_destinations[ $d->getId() ] = $d;
				}
			}
			foreach($hotel->getHotelStars() as $s) {
				if (empty($stars[ $s->getId() ]['entity'])) {
					$stars[ $s->getId() ]['entity'] = $s;
					$stars[ $s->getId() ]['count'] = 0;
				}
				$stars[ $s->getId() ]['count']++;
				if (in_array($s->getId(),array_keys($filtered_stars)) && empty($filtered_stars[ $s->getId() ])) {
					$filtered_stars[ $s->getId() ] = $s;
				}
			}
		}


		usort($destinations, function($a, $b) {
			return $b['count'] - $a['count'];
		});

		$data['current_path'] = $filters;

		$data['filtered_destinations'] = $filtered_destinations;
		$data['filtered_stars'] = $filtered_stars;

		$hotel_filtered = $em->getRepository('AppBundle\Entity\Hotel')->findAllHotels($em,$request,$data);

		foreach($hotel_filtered as $hotel) {

			/*foreach($hotel->getHotelStars() as $s) {
				if (empty($stars[ $s->getId() ]['entity'])) {
					$stars[ $s->getId() ]['entity'] = $s;
					$stars[ $s->getId() ]['count'] = 0;
				}
				$stars[ $s->getId() ]['count']++;
			}*/
		}

		$data['filter_destinations'] = $destinations;

		ksort($stars);
		$data['filter_stars'] = $stars;

		$data['hotels_count_total'] = count($hotel_filtered);

		$data['filters_active_count'] = count($filtered_destinations) + count($filtered_stars) ;

		$page = !empty($request->query->get('page')) ? intval($request->query->get('page')) : 1;
		$paginator = array(
			'currentFilters' => array(
				'filters' => $data['current_path']
			),
			'currentPage' => $page,
			'paginationPath' => 'home_hebergement_filter_'.$request->getLocale(),
			'showAlwaysFirstAndLast' => true,
			'lastPage' => ceil(count($hotel_filtered) / $this->_itemsOnPage),
		);

		$data['hotels'] = array_slice($hotel_filtered, ($page * $this->_itemsOnPage - $this->_itemsOnPage), $this->_itemsOnPage);
		$data = array_merge($data,$paginator);

		return $this->render('default/hebergement.html.twig', $data);
    }



	public function hebergementAction(Request $request, $slug = '')
	{

		$data = array();
		$em = $this->getDoctrine()->getManager();

		$hotel = $em->getRepository('AppBundle\Entity\Hotel')->findOneHotelBySlug($em,$request,$slug);



		if (empty($slug) or empty($hotel)) {
			throw new NotFoundHttpException('404 not found');
		}
		$data['hotel'] = $hotel;

		$breadcrumbs = array();
		$breadcrumbs[$this->get('translator')->trans('front.homepage')] = $this->generateUrl('homepage');
		$breadcrumbs[$this->get('translator')->trans('front.hebergement')] = $this->generateUrl('home_hebergement_'.$request->getLocale());
		if (count($hotel->getDestination()) > 0) {
			$breadcrumbs[$hotel->getDestination()[0]->translate()->getName()] = $this->generateUrl('home_hebergement_filter_'.$request->getLocale(), array('filters'=>$this->get('translator')->trans('url.hotel.destination').'/'.$hotel->getDestination()[0]->getId()));

			if (count($hotel->getHotelStars())>0) {
				$breadcrumbs[$hotel->getHotelStars()[0]->translate()->getName()] = $this->generateUrl('home_hebergement_filter_'.$request->getLocale(), array('filters'=>$this->get('translator')->trans('url.hotel.destination').'/'.$hotel->getDestination()[0]->getId().'/'.$this->get('translator')->trans('url.hotel.stars').'/'.$hotel->getHotelStars()[0]->getId()));
			}
		}
		//$breadcrumbs[$hotel->translate()->getName()] = '';
		$data['breadcrumbs'] = $breadcrumbs;

		$bookingForm = new Forms($em,$this->get('translator'),$request->getLocale(),$this->getUser());
		$include = array('date_apart','nights','combien','rooms','supplement','precisions');
		$data['hotel_form'] = $bookingForm->getBookingForm($hotel,$include);

		return $this->render('default/hebergement_detail.html.twig', $data);

	}

}
