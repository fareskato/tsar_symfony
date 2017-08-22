<?php
namespace AppBundle\ResponseListener;

use AppBundle\Service\Locales;
use Exception;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\RedirectResponse;
use \Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class ResponseListener
{
	private $router;
	private $_locales;
	private $_defaultLocale;

	private $sessionLocaleName = 'locale';
	private $sessionDomainName = 'domain';

	private $exclude = array();

	public function __construct(Router $router, $container, $session) {

		$loc = new Locales();
		$this->_locales = $loc->getLocales();
		$this->_defaultLocale = $loc->getDefaultLocale();

		$this->router = $router;
		$this->container = $container;
		$this->session = $session;

		global $kernel;
		$exclude = $kernel->getContainer('parameters')->getParameter('clean_url');
		$this->exclude = explode('|',$exclude);
	}

	public function onKernelController(GetResponseEvent  $event)
	{
		$request = $event->getRequest();

		$getRequestUri = explode('?',$request->getRequestUri());

		$requestUrl = explode('/',$getRequestUri[0]);
		$requestedLocale = $requestUrl[1];

		$requestGet = '';
		if (!empty($getRequestUri[1])) {
			$requestGet = $getRequestUri[1];
		}

		if ( !in_array($requestedLocale, $this->exclude) && (empty($requestedLocale) || !in_array($requestedLocale, $this->_locales ))) {
			$locale = $this->_defaultLocale;
			$sessionLang = $this->session->get($this->sessionLocaleName,'');
			if (empty($sessionLang)) {
				if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
					$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
					if (in_array($lang,$this->_locales)) {
						$locale = $lang;
					}
				}
				$this->session->set($this->sessionLocaleName,$locale);
			} else {
				$locale = $sessionLang;
			}

			foreach($requestUrl as $key => $value) {
				if (empty($value)) {
					unset($requestUrl[$key]);
				}
			}

			$newRoute = '/'.$locale. (!empty($requestUrl) ? '/'.implode('/',$requestUrl) : '').( !empty($requestGet) ? '?'.$requestGet : '');
			$event->setResponse(new RedirectResponse($newRoute));
		} else {
			$this->session->set($this->sessionLocaleName,$request->getLocale());
		}

		// SAVE SESSION DOMAIN TO VARIABLE
		$this->sessionsToDomain($request);

	}

	private function sessionsToDomain() {
		global $kernel;

		$curDomain = $this->session->get($this->sessionDomainName,'');
		if (empty($curDomain)) {
			$curLocation = $this->getUserLocation();
			$this->session->set($kernel->getContainer('parameters')->getParameter('domain.variable.name'),$kernel->getContainer('parameters')->getParameter( ($curLocation == 'RU' ? 'domain.russian' : 'domain.international') ));
		}
	}


	private function getRealIp(){
		$client  = @$_SERVER['HTTP_CLIENT_IP'];
		$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
		$remote  = $_SERVER['REMOTE_ADDR'];

		if(filter_var($client, FILTER_VALIDATE_IP))
		{
			$ip = $client;
		}
		elseif(filter_var($forward, FILTER_VALIDATE_IP))
		{
			$ip = $forward;
		}
		else
		{
			$ip = $remote;
		}
		return $ip;
	}

	public function getIpInteger($ip = ''){
		if (empty($ip)) {
			$ip = $this->getRealIp();
		}
		$intIp = ip2long($ip);
		return $intIp;
	}

	public function getUserLocation($ip = ''){
		if (empty($ip)) {
			$ip = $this->getIpInteger();
		} else {
			$ip = $this->getIpInteger($ip);
		}
		$return = '';

		global $kernel;
		$em = $kernel->getContainer()->get('doctrine')->getEntityManager();

		$country = $em->createQueryBuilder()
			->select('u.country')
			->from('AppBundle\Entity\LocationIp', 'u')
			->where('u.ip_start <= :ip1')
			->andWhere('u.ip_end >= :ip2')
			->setParameters( array(
				'ip1' => $ip,
				'ip2' => $ip
			))
			->setMaxResults(1)
			->getQuery()
			->getOneOrNullResult();

		if (!empty($country['country'])) {
			$return = $country['country'];
		}
		return $return;
	}



}