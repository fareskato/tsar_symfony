<?php
// src/AppBundle/Routing/ExtraLoader.php
namespace AppBundle\Routing;

use AppBundle\Service\Locales;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Router;
use Symfony\Component\Translation\TranslatorInterface;

class ExtraLoader extends Loader
{
	private $loaded = false;

	private $_locales;
	private $_defaultLocale;

	private $_translator;

	public function __construct(TranslatorInterface $translator, $container)
	{

		//We need locales everywhere in code
		//Проставляем локали
		$loc = new Locales();
		$this->_locales = $loc->getLocales();
		$this->_defaultLocale = $loc->getDefaultLocale();

		$this->_translator = $translator;

	}

	public function load($resource, $type = null)
	{
		if (true === $this->loaded) {
			throw new \RuntimeException('Do not add the "extra" loader twice');
		}

		$routes = new RouteCollection();

		$this->addStaticRoutes($routes);

		$this->addHotelRoutes($routes);

		$this->addExtensionRoutes($routes);

		$this->addVoyageRoutes($routes);

		$this->addVisitsRoutes($routes);

		$this->addAgendaRoutes($routes);

		$this->addDestinationRoutes($routes);



		$this->loaded = true;

		return $routes;
	}

	public function supports($resource, $type = null)
	{
		return 'extra' === $type;
	}

	private function addHotelRoutes($routes){
		foreach ($this->_locales as $locale) {
			//HOTEL LIST
			$path = '/{_locale}/'.$this->_translator->trans('url.hotel',array(),null,$locale);
			$defaults = array(
				'_controller' => 'AppBundle:Hebergement:index',
			);
			$requirements = array();
			$route = new Route($path, $defaults, $requirements);
			$routeName = 'home_hebergement_'.$locale;
			$routes->add($routeName, $route);

			//HOTEL LIST FILTERS
			$path = '/{_locale}/'.$this->_translator->trans('url.hotel',array(),null,$locale).'/{filters}';
			$defaults = array(
				'_controller' => 'AppBundle:Hebergement:index',
				'filters' => ''
			);
			$requirements = array(
				"filters" => ".*\/.*"
			);
			$route = new Route($path, $defaults, $requirements);
			$routeName = 'home_hebergement_filter_'.$locale;
			$routes->add($routeName, $route);

			//HOTEL DETAIL
			$path = '/{_locale}/'.$this->_translator->trans('url.hotel',array(),null,$locale).'/{slug}';
			$defaults = array(
				'_controller' => 'AppBundle:Hebergement:hebergement',
				"filters" => ""
			);
			$requirements = array(
				"filters" => ".*\-\D*"
			);
			$route = new Route($path, $defaults, $requirements);
			$routeName = 'home_hebergement_detail_'.$locale;

			$routes->add($routeName, $route);
		}
	}

	private function addExtensionRoutes($routes){
		foreach ($this->_locales as $locale) {
			//Extension LIST
			$path = '/{_locale}/'.$this->_translator->trans('url.extension',array(),null,$locale);
			$defaults = array(
				'_controller' => 'AppBundle:Extension:index',
			);
			$requirements = array();
			$route = new Route($path, $defaults, $requirements);
			$routeName = 'home_extension_'.$locale;
			$routes->add($routeName, $route);

			//Extension LIST FILTERS
			$path = '/{_locale}/'.$this->_translator->trans('url.extension',array(),null,$locale).'/{filters}';
			$defaults = array(
				'_controller' => 'AppBundle:Extension:index',
				'filters' => ''
			);
			$requirements = array(
				"filters" => ".*\/.*"
			);
			$route = new Route($path, $defaults, $requirements);
			$routeName = 'home_extension_filter_'.$locale;
			$routes->add($routeName, $route);

			//Extension DETAIL
			$path = '/{_locale}/'.$this->_translator->trans('url.extension',array(),null,$locale).'/{slug}';
			$defaults = array(
				'_controller' => 'AppBundle:Extension:extension',
				"filters" => ""
			);
			$requirements = array(
				"filters" => ".*\-\D*"
			);
			$route = new Route($path, $defaults, $requirements);
			$routeName = 'home_extension_detail_'.$locale;

			$routes->add($routeName, $route);
		}
	}

	private function addVoyageRoutes($routes){
		foreach ($this->_locales as $locale) {
			//Extension LIST
			$path = '/{_locale}/'.$this->_translator->trans('url.voyage',array(),null,$locale);
			$defaults = array(
				'_controller' => 'AppBundle:Voyage:index',
			);
			$requirements = array();
			$route = new Route($path, $defaults, $requirements);
			$routeName = 'home_voyage_'.$locale;
			$routes->add($routeName, $route);

			//Extension LIST FILTERS
			$path = '/{_locale}/'.$this->_translator->trans('url.voyage',array(),null,$locale).'/{filters}';
			$defaults = array(
				'_controller' => 'AppBundle:Voyage:index',
				'filters' => ''
			);
			$requirements = array(
				"filters" => ".*\/.*"
			);
			$route = new Route($path, $defaults, $requirements);
			$routeName = 'home_voyage_filter_'.$locale;
			$routes->add($routeName, $route);

			//Extension DETAIL
			$path = '/{_locale}/'.$this->_translator->trans('url.voyage',array(),null,$locale).'/{slug}';
			$defaults = array(
				'_controller' => 'AppBundle:Voyage:voyage',
				"filters" => ""
			);
			$requirements = array(
				"filters" => ".*\-\D*"
			);
			$route = new Route($path, $defaults, $requirements);
			$routeName = 'home_voyage_detail_'.$locale;

			$routes->add($routeName, $route);
		}
	}

	private function addVisitsRoutes($routes){
		foreach ($this->_locales as $locale) {
			//Extension LIST
			$path = '/{_locale}/'.$this->_translator->trans('url.visit',array(),null,$locale);
			$defaults = array(
				'_controller' => 'AppBundle:Visits:index',
			);
			$requirements = array();
			$route = new Route($path, $defaults, $requirements);
			$routeName = 'home_visit_'.$locale;
			$routes->add($routeName, $route);

			//Extension LIST FILTERS
			$path = '/{_locale}/'.$this->_translator->trans('url.visit',array(),null,$locale).'/{filters}';
			$defaults = array(
				'_controller' => 'AppBundle:Visits:index',
				'filters' => ''
			);
			$requirements = array(
				"filters" => ".*\/.*"
			);
			$route = new Route($path, $defaults, $requirements);
			$routeName = 'home_visit_filter_'.$locale;
			$routes->add($routeName, $route);

			//Extension DETAIL
			$path = '/{_locale}/'.$this->_translator->trans('url.visit',array(),null,$locale).'/{slug}';
			$defaults = array(
				'_controller' => 'AppBundle:Visits:visit',
				"filters" => ""
			);
			$requirements = array(
				"filters" => ".*\-\D*"
			);
			$route = new Route($path, $defaults, $requirements);
			$routeName = 'home_visit_detail_'.$locale;

			$routes->add($routeName, $route);
		}
	}

	private function addAgendaRoutes($routes){
		foreach ($this->_locales as $locale) {

			//Extension LIST
			$path = '/{_locale}/'.$this->_translator->trans('url.agenda',array(),null,$locale);
			$defaults = array(
				'_controller' => 'AppBundle:Agenda:index',
			);
			$requirements = array();
			$route = new Route($path, $defaults, $requirements);
			$routeName = 'home_agenda_'.$locale;
			$routes->add($routeName, $route);

			//Extension LIST FILTERS
			$path = '/{_locale}/'.$this->_translator->trans('url.agenda',array(),null,$locale).'/{filters}';
			$defaults = array(
				'_controller' => 'AppBundle:Agenda:index',
				'filters' => ''
			);
			$requirements = array(
				"filters" => ".*\/.*"
			);
			$route = new Route($path, $defaults, $requirements);
			$routeName = 'home_agenda_filter_'.$locale;
			$routes->add($routeName, $route);

			//Extension DETAIL
			$path = '/{_locale}/'.$this->_translator->trans('url.agenda_detail',array(),null,$locale).'/{slug}';
			$defaults = array(
				'_controller' => 'AppBundle:Agenda:event',
				"filters" => ""
			);
			$requirements = array(
				"filters" => ".*\-\D*"
			);
			$route = new Route($path, $defaults, $requirements);
			$routeName = 'home_agenda_detail_'.$locale;

			$routes->add($routeName, $route);
		}
	}

	private function addDestinationRoutes($routes){
		foreach ($this->_locales as $locale) {
			//Extension LIST
			$path = '/{_locale}/'.$this->_translator->trans('url.destination',array(),null,$locale);
			$defaults = array(
				'_controller' => 'AppBundle:Destination:index',
			);
			$requirements = array();
			$route = new Route($path, $defaults, $requirements);
			$routeName = 'home_destination_'.$locale;
			$routes->add($routeName, $route);

			//Extension DETAIL
			$path = '/{_locale}/'.$this->_translator->trans('url.destination',array(),null,$locale).'/{slug}';
			$defaults = array(
				'_controller' => 'AppBundle:Destination:destination',
				"filters" => ""
			);
			$requirements = array(
				"filters" => ".*\-\D*"
			);
			$route = new Route($path, $defaults, $requirements);
			$routeName = 'home_destination_detail_'.$locale;

			$routes->add($routeName, $route);
		}
	}

	private function addStaticRoutes($routes){
		foreach ($this->_locales as $locale) {
			$path = '/{_locale}/'.$this->_translator->trans('url.static',array(),null,$locale).'-{slug}';
			$defaults = array(
				'_controller' => 'AppBundle:Default:article',
			);
			$requirements = array();
			$route = new Route($path, $defaults, $requirements);
			$routeName = 'home_static_'.$locale;
			$routes->add($routeName, $route);


			$path = '/{_locale}/'.$this->_translator->trans('url.international',array(),null,$locale);
			$defaults = array(
				'_controller' => 'AppBundle:Default:indexInternational',
			);
			$requirements = array();
			$route = new Route($path, $defaults, $requirements);
			$routeName = 'switch_int_'.$locale;
			$routes->add($routeName, $route);

			$path = '/{_locale}/'.$this->_translator->trans('url.vivre-en-russie',array(),null,$locale);
			$defaults = array(
				'_controller' => 'AppBundle:Default:indexRussian',
			);
			$requirements = array();
			$route = new Route($path, $defaults, $requirements);
			$routeName = 'switch_ru_'.$locale;
			$routes->add($routeName, $route);


			$path = '/{_locale}/'.$this->_translator->trans('url.search',array(),null,$locale).'/{filters}';
			$defaults = array(
				'_controller' => 'AppBundle:Default:search',
				'filters' => ''
			);
			$requirements = array(
				"filters" => ".*\/.*"
			);
			$route = new Route($path, $defaults, $requirements);
			$routeName = 'home_search_'.$locale;
			$routes->add($routeName, $route);
		}
	}
}