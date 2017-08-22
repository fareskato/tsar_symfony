<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\Location;
use AppBundle\Service\Locales;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LocationController extends Controller
{

	private $_itemsOnPage = 20;

	private $_locales;
	private $_defaultLocale;

	public function __construct()
	{
		$loc = new Locales();
		$this->_locales = $loc->getLocales();
		$this->_defaultLocale = $loc->getDefaultLocale();
	}

	/**
	 * @Route("/location", name="admin_location")
	 */
	public function indexAction(Request $request)
	{

		$em = $this->getDoctrine()->getManager();


//		$data['data_list'] = $em->getRepository('AppBundle\Entity\Location')->findBy(array(), array('id' => 'DESC'));
		$data['data_list'] = $em->getRepository('AppBundle\Entity\Location')->findAllForAdminList($em,'AppBundle\Entity\Location',$request);


		$data['data_fields'] = array('id', 'name', 'country', 'city', 'street');
		$data['data_title'] = 'adm.location';
		$data['data_book_name'] = '';

		$link = $this->generateUrl('admin_location_add', array(), UrlGeneratorInterface::ABSOLUTE_PATH);

		$data['data_buttons'] = array(
			array(
				'name' => 'add',
				'link' => $link,
				'class' => 'primary'
			)
		);

		$data['data_actions'] = array(
			array(
				'name' => 'edit',
				'link' => 'admin_location_edit',
				'class' => '',
				'confirm' => false
			),
			/*array(
			  'name' => 'activate',
			  'link' => 'admin_location_activate',
			  'class' => 'info',
			  'confirm' => false
			),*/
			array(
				'name' => 'delete',
				'link' => 'admin_location_delete',
				'class' => 'danger',
				'confirm' => 'adm.action.delete.confirm'
			),
		);

		$page = !empty($request->query->get('page')) ? intval($request->query->get('page')) : 1;
		$paginator = array(
			'currentFilters' => $request->query->all(),
			'currentPage' => $page,
			'paginationPath' => 'admin_location',
			'showAlwaysFirstAndLast' => true,
			'lastPage' => ceil(count($data['data_list']) / $this->_itemsOnPage),
		);

		$data['data_list'] = array_slice($data['data_list'], ($page * $this->_itemsOnPage - $this->_itemsOnPage), $this->_itemsOnPage);
		$data = array_merge($data, $paginator);

		return $this->render('AdminBundle:Default:list.html.twig', $data);
	}

	/**
	 * @Route("/location/add", name="admin_location_add")
	 * @Route("/location/edit/{id}", defaults={"id" = 0}, name="admin_location_edit")
	 */
	public function editAction($id = 0, Request $request)
	{

		$em = $this->getDoctrine()->getManager();

		$entity = new Location();
		$entity->setDefaultLocale($this->_defaultLocale);
		if (!empty($id)) {
			$entityRecieved = $em->getRepository('AppBundle\Entity\Location')->findOneBy(array('id' => $id));
			if (!empty($entityRecieved)) {
				$entity = $entityRecieved;
			}
		}
		$data['entity'] = $entity;
		$data['data_title'] = (!empty($data['entity']->translate()->getName()) ? $data['entity']->translate()->getName() : $this->get('translator')->trans('adm.action.new'));

		$data['data_type'] = 'adm.location';

		$data['data_buttons'] = array(
			array(
				'name' => 'save',
				'link' => 'admin_location_edit',
				'class' => 'primary',
				'button' => true,
				'button_type' => 'submit'
			),
			array(
				'name' => 'cancel',
				'link' => !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->generateUrl('admin_location', array(), UrlGeneratorInterface::ABSOLUTE_PATH),
				'class' => 'default',
			)
		);

		if ($request->isMethod('POST')) {

			$data = $request->request->all();

			$this->saveEntity($data, $entity, $em);
			return $this->redirectToRoute('admin_location', array());
		}

		$data['form'] = $this->createEntityForm($entity, $id, $em);


		return $this->render('AdminBundle:Default:form.html.twig', $data);

	}

	/**
	 * @Route("/location/delete/{id}", name="admin_location_delete")
	 */
	public function deleteAction($id)
	{
		$em = $this->getDoctrine()->getManager();

		$data = $em->getRepository('AppBundle\Entity\Location')->findOneBy(array('id' => intval($id)));
		if ($data) {
			$em->remove($data);
			$em->flush();
		}

		return !empty($_SERVER['HTTP_REFERER']) ? $this->redirect($_SERVER['HTTP_REFERER']) : $this->generateUrl('admin_location', array(), UrlGeneratorInterface::ABSOLUTE_PATH);
	}

	/**
	 * @Route("/location/activate/{id}", name="admin_location_activate")
	 */
	public function activateAction($id)
	{
		$em = $this->getDoctrine()->getManager();

		$data = $em->getRepository('AppBundle\Entity\Location')->findOneBy(array('id' => intval($id)));
		if ($data) {
			if ($data->isActive()) {
				$data->setActive(0);
			} else {
				$data->setActive(1);
			}
			$em->persist($data);
			$em->flush();
		}

		return !empty($_SERVER['HTTP_REFERER']) ? $this->redirect($_SERVER['HTTP_REFERER']) : $this->generateUrl('admin_location', array(), UrlGeneratorInterface::ABSOLUTE_PATH);

	}


	private function createEntityForm($entity, $id = 0, $em)
	{

		if (!empty($id)) {
			$form['action'] = $this->generateUrl('admin_location_edit', array('id' => $id), UrlGeneratorInterface::ABSOLUTE_PATH);
		} else {
			$form['action'] = $this->generateUrl('admin_location_add', array(), UrlGeneratorInterface::ABSOLUTE_PATH);
		}

		$form['id'] = $id;
		$form['form_id'] = 'form_' . md5('location');

		$countries = array();
		$countriesAll = Intl::getRegionBundle()->getCountryNames();
		foreach ($countriesAll as $code => $name) {
			$countries[strtolower($code)] = $name;
		}

		foreach ($this->_locales as $lng) {


			$fields = array(
				'name' => array(
					'label' => 'adm.field.name',
					'type' => 'text',
					'name' => $lng . '[name]',
					'required' => ($lng != $this->_defaultLocale ? false : true),
					'value' => $entity ? $entity->translate($lng, ($lng == $this->_defaultLocale ? true : false))->getName() : '',
					'translate' => true
				),
				'street' => array(
					'label' => 'adm.field.street',
					'type' => 'text',
					'name' => $lng . '[street]',
					'required' => false,
					'value' => $entity ? $entity->translate($lng, ($lng == $this->_defaultLocale ? true : false))->getStreet() : '',
					'translate' => true
				),
				'city' => array(
					'label' => 'adm.field.city',
					'type' => 'text',
					'name' => $lng . '[city]',
					'required' => false,
					'value' => $entity ? $entity->translate($lng, ($lng == $this->_defaultLocale ? true : false))->getCity() : '',
					'translate' => true
				),
				'additional' => array(
					'label' => 'adm.field.additional',
					'type' => 'text',
					'name' => $lng . '[additional]',
					'required' => false,
					'value' => $entity ? $entity->translate($lng, ($lng == $this->_defaultLocale ? true : false))->getAdditional() : '',
					'translate' => true
				),
				'country' => array(
					'label' => 'adm.field.country',
					'type' => 'select',
					'name' => $lng . '[country]',
					'required' => true,
					'value' => $entity->getCountry() ? $entity->getCountry() : null,
					'value_default' => 'adm.field.select.country',
					'value_zero' => true,
					'values' => $countries,
					'translate' => false
				),
				'map' => array(
					'label' => 'adm.field.map',
					'label_latitude' => 'adm.field.map.latitude',
					'label_longitude' => 'adm.field.map.longitude',
					'type' => 'map',
					'name_latitude' => $lng . '[latitude]',
					'name_longitude' => $lng . '[longitude]',
					'required' => false,
					'value_latitude' => $entity->getLatitude() ? $entity->getLatitude() : null,
					'value_longitude' => $entity->getlongitude() ? $entity->getlongitude() : null,
					'translate' => false
				),
			);


			$fields = array_merge_recursive($fields);

			$fieldset = 'translate';
			if ($lng == $this->_defaultLocale) {
				$fieldset = 'default';
			}
			$form[$fieldset][$lng] = $fields;
		}
		return $form;
	}

	private function saveEntity($data, $entity, $em)
	{

		foreach ($data as $localeName => $locale) {
			if (in_array($localeName, $this->_locales)) {

				/* BLOCK FOR TRANSLABLE VALUES*/

				if (!empty($data[$localeName]['name'])) {
					$entity->translate($localeName, false)->setName(trim($data[$localeName]['name']));
				}
				if (!empty($data[$localeName]['street'])) {
					$entity->translate($localeName, false)->setStreet(trim($data[$localeName]['street']));
				}
				if (!empty($data[$localeName]['additional'])) {
					$entity->translate($localeName, false)->setAdditional(trim($data[$localeName]['additional']));
				}
				if (!empty($data[$localeName]['city'])) {
					$entity->translate($localeName, false)->setCity(trim($data[$localeName]['city']));
				}

				/* BLOCK FOR NON TRANSLATED VALUES*/
				if ($localeName == $this->_defaultLocale) {


					$entity->setCountry(!empty($data[$localeName]['country']) ? trim($data[$localeName]['country']) : null);
					$entity->setLatitude(!empty($data[$localeName]['latitude']) ? trim($data[$localeName]['latitude']) : null);
					$entity->setLongitude(!empty($data[$localeName]['longitude']) ? trim($data[$localeName]['longitude']) : null);

				}

				$entity->mergeNewTranslations();

			}
		}

		$em->persist($entity);
		$em->flush();

		return true;
	}
}
