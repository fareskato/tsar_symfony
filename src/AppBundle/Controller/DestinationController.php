<?php

namespace AppBundle\Controller;


use AppBundle\Entity\BookDomain;
use AppBundle\Service\Locales;
use Gedmo\Mapping\Annotation\Locale;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Intl\Intl;


class DestinationController extends Controller
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
		$destinations = $em->getRepository('AppBundle\Entity\Destination')->findAllDestinations($em,$request);

		$data['destinations'] = $destinations;

		return $this->render('default/destination.html.twig', $data);

	}



	public function destinationAction(Request $request, $slug = '')
	{

		$data = array();
		$em = $this->getDoctrine()->getManager();

		$destination = $em->getRepository('AppBundle\Entity\Destination')->findOneDestinationBySlug($em,$request,$slug);

		if (empty($slug) or empty($destination)) {
			throw new NotFoundHttpException('404 not found');
		}
		$data['destination'] = $destination;

		$data['destination_extensions'] = $em->getRepository('AppBundle\Entity\Destination')->findDestinationExtensions($em,$request,$destination);
		$data['destination_events'] = $em->getRepository('AppBundle\Entity\Destination')->findDestinationEvents($em,$request,$destination);


		$breadcrumbs = array();
		$breadcrumbs[$this->get('translator')->trans('front.homepage')] = $this->generateUrl('homepage');
		$breadcrumbs[$destination->translate()->getName()] = '';
		$data['breadcrumbs'] = $breadcrumbs;

		return $this->render('default/destination_detail.html.twig', $data);

	}

}
