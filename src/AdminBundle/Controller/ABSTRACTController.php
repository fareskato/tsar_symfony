<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\Menu;
use AppBundle\Service\Locales;
use Doctrine\Common\Annotations\AnnotationReader;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ABSTRACTController extends Controller
{
	
	private $_locales;
	private $_defaultLocale;

	public function __construct()
	{
		//We need locales everywhere in code
		//Проставляем локали
		$loc = new Locales();
		$this->_locales = $loc->getLocales();
		$this->_defaultLocale = $loc->getDefaultLocale();
	}

	/**
     * @Route("/you_route", name="admin_you_route")
     */
    public function indexAction(Request $request)
    {

		$em = $this->getDoctrine()->getManager();

		// Get list of all entities
		$data['data_list'] = $em->getRepository('ENTITY')->findAll();

		//Get field to display
		$data['data_fields'] = array('id','name','slug');

		//Tranlate in AdminBundle/Resources/translations/messages.en.yml
		$data['data_title'] = 'adm.menu';

		//Buttons in top and bottom
		$data['data_buttons'] = array(
			array(
				'name' => 'add',
				'link' => $this->generateUrl('admin_menu_add', array(), UrlGeneratorInterface::ABSOLUTE_PATH),
				'class' => 'primary'
			)
		);

		//Buttons of action on each entity
		$data['data_actions'] = array(
				array(
					'name' => 'edit',
					'link' => 'admin_menu_edit',
					'class' => '',
					'confirm' => false
				),
				array(
					'name' => 'activate',
					'link' => 'admin_menu_activate',
					'class' => 'info',
					'confirm' => false
				),
				array(
					'name' => 'delete',
					'link' => 'admin_menu_delete',
					'class' => 'danger',
					'confirm' => 'adm.action.delete.confirm'
				),
			);

		//RENDER TEMPLATE
        return $this->render('AdminBundle:Default:list.html.twig',$data);
    }

	/**
	 * @Route("/you_route/add", name="admin_you_route_add")
	 * @Route("/you_route/edit/{id}", defaults={"id" = 0}, name="admin_you_route_edit")
	 */
	public function editAction($id = 0, Request $request)
	{

		$em = $this->getDoctrine()->getManager();

		//ALWAYS CREATE NEW ONE
		$entity = new Menu();
		if (!empty($id)) {
			$entity = $em->getRepository('ENTITY')->findOneBy(array('id'=>$id));
		}

		//MOVE ENTITY TO FRONT
		$data['entity'] = $entity;

		//TITLE FOR PAGE
		$data['data_title'] = (!empty($data['entity']->getName()) ? $data['entity']->getName() : $this->get('translator')->trans('adm.action.new'));

		//TYPE FOR PAGE
		$data['data_type'] =  'adm.menu.menu_name';

		//BUTTONS
		$data['data_buttons'] = array(
			array(
				'name' => 'save',
				'link' => 'admin_menu_edit',
				'class' => 'primary',
				'button' => true,
				'button_type' => 'submit'
			),
			array(
				'name' => 'cancel',
				'link' => !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->generateUrl('admin_menu', array(), UrlGeneratorInterface::ABSOLUTE_PATH),
				'class' => 'default',
			)
		);

		//IF SAVE
		if ($request->isMethod('POST')) {
			$data = $request->request->all();

			$this->saveEntity($data,$entity,$em);

			return $this->redirectToRoute('admin_menu', array());
		}

		//FORM CREATION
		$data['form'] = $this->createEntityForm($entity,$id,$em);


		return $this->render('AdminBundle:Default:form.html.twig',$data);

	}

	/**
	 * @Route("/menu/you_route/{id}", name="admin_menu_delete")
	 */
	public function deleteAction($id)
	{
		$em = $this->getDoctrine()->getManager();

		$data = $em->getRepository('ENTITY')->findOneBy(array('id' => intval($id)));
		if ($data) {
			$em->remove($data);
			$em->flush();
		}

		return !empty($_SERVER['HTTP_REFERER']) ? $this->redirect($_SERVER['HTTP_REFERER']) : $this->generateUrl('admin_menu', array(), UrlGeneratorInterface::ABSOLUTE_PATH);
	}

	/**
	 * @Route("/menu/you_route/{id}", name="admin_menu_activate")
	 */
	public function activateAction($id)
	{
		$em = $this->getDoctrine()->getManager();

		$data = $em->getRepository('ENTITY')->findOneBy(array('id' => intval($id)));
		if ($data) {
			if ($data->isActive()) {
				$data->setActive(0);
			} else {
				$data->setActive(1);
			}
			$em->persist($data);
			$em->flush();
		}

		return !empty($_SERVER['HTTP_REFERER']) ? $this->redirect($_SERVER['HTTP_REFERER']) : $this->generateUrl('admin_menu', array(), UrlGeneratorInterface::ABSOLUTE_PATH);

	}

	//CREATE FORM FOR ENTITY
	private function createEntityForm($entity, $id =0, $em) {

		//WHERE TO SAVE
		if (!empty($id)) {
			$form['action'] = $this->generateUrl('admin_you_route_edit', array('id' => $id), UrlGeneratorInterface::ABSOLUTE_PATH);
		} else{
			$form['action'] = $this->generateUrl('admin_you_route_add', array(), UrlGeneratorInterface::ABSOLUTE_PATH);
		}

		//ID OF ENTITY
		$form['id'] = $id;
		$form['form_id'] = 'form_'.md5('type');

		$form['separator'] = false; // true/false;

		foreach($this->_locales as $lng) {

			$fields = array(
				'name' => array(
					'label' => 'adm.field.name',
					'type' => 'text',
					'name' => $lng.'[name]',
					'required' => ($lng != $this->_defaultLocale ? false : true),
					'value' => $entity ? $entity->getTranslation('name',$lng,($lng == $this->_defaultLocale ? true : false)) : '',
					'translate' => true
				),
				'description' => array(
					'label' => 'adm.field.description',
					'type' => 'textarea',
					'name' => $lng.'[description]',
					'required' => false,
					'value' => $entity ? $entity->getTranslation('description',$lng,($lng == $this->_defaultLocale ? true : false)) : '',
					'translate' => true
				),
				'body' => array(
					'label' => 'adm.field.body',
					'type' => 'texteditor',
					'name' => $lng.'[body]',
					'required' => false,
					'value' => $entity ? $entity->getTranslation('body',$lng,($lng == $this->_defaultLocale ? true : false)) : '',
					'translate' => true
				),
				'image' => array(
					'label' => 'adm.field.image',
					'type' => 'image',
					'name' => $lng.'[image]',
					'path' => $entity->getImage() ? $entity->getImage()->getUrl() : false,
					'value' => $entity->getImage() ? $entity->getImage()->getId() : false,
					'required' => false,
					'translate' => false
				),
				'select' => array(
					'label' => 'adm.field.parent',
					'type' => 'select',
					'name' => $lng.'[select]',
					'required' => false,
					'value' => $entity->getParent() ? $entity->getParent()->getId() : null,
					'value_default' => 'adm.field.select.toplevel',
					'value_zero' => true,
					'values' => array( 'key'=>'value' ),
					'translate' => false
				),
				'active' => array(
					'label' => 'adm.field.active',
					'type' => 'checkbox',
					'name' => $lng.'[active]',
					'value' => 1,
					'checked' => $entity->isActive() ? true : false,
					'translate' => false
				),
				'type_domain' => array(
					'label' => 'adm.field.domains',
					'type' => 'checkbox_multiple',
					'name' => $lng.'[type_domain]',
					'values' => array( 'key'=>'value' ),
					'checked_values' => array(1,2,3), //only checked values array
					'translate' => false
				),
				'map' => array(
					'label' => 'adm.field.map',
					'label_latitude' => 'adm.field.map.latitude',
					'label_longitude' => 'adm.field.map.longitude',
					'type' => 'map',
					'name_latitude' => $lng.'[latitude]',
					'name_longitude' => $lng.'[longitude]',
					'required' => false,
					'value_latitude' => $entity->getLatitude() ? $entity->getLatitude() : null,
					'value_longitude' => $entity->getlongitude() ? $entity->getlongitude() : null,
					'translate' => false
				),
				'radio' => array(
					'label' => 'adm.field.radio',
					'type' => 'radio',
					'name' => $lng.'[radio]',
					'values' => array( 'key'=>'value' ),
					'value_zero' => true,
					'checked_value' =>  $entity->getValue() ? $entity->getValue()->getId() : false,
					'translate' => false
				),


				'separator_images' => array(
					'label' => 'adm.field.images',
					'type' => 'separator',
					'translate' => true
				),

				// MANY TO MANY
				'parent' => array(
					'label' => 'adm.field.parent',
					'type' => 'relation_many',
					'autocomplete' => 'parent',
					'autocomplete_path' => 'admin_destination_autocomplete',
					'name' => $lng.'[parent][]',
					'add' => 'admin_destination_add',
					'field_rel' => array('id','name'),
					'values' => $entity->getParent() ? $entity->getParent() : array(),
					'translate' => false
				),

				// MANY TO ONE
				'location' => array(
					'label' => 'adm.field.location',
					'type' => 'relation_one',
					'autocomplete' => 'location',
					'autocomplete_path' => 'admin_destination_autocomplete',
					'name' => $lng.'[location]',
					'add' => 'admin_location_add',
					'field_rel' => array('id','name','street','city','getCountryName'),
					'values' => $entity->getLocation() ? $entity->getLocation() : null,
					'translate' => false,
					'editLink' => array(
						'type' => 'location',
						'path' => $entity->getLocation() ? array('id' => $entity->getLocation()->getId()) : array(),
					),
				),

			);

			//TECHNICAL INFORMATION
			$fieldset = 'translate';
			if ($lng == $this->_defaultLocale) {
				$fieldset = 'default';
			}
			$form[$fieldset][$lng] = $fields;
		}
		return $form;
	}


	//SAVE ENTITY
	private function saveEntity($data,$entity,$em){

		//SAVE ENTITY ACCORDING TO LOCALE
		foreach ($data as $localeName => $locale) {
			if (in_array($localeName,$this->_locales)) {
				$entity->setTranslatableLocale($localeName);

				/* BLOCK FOR TRANSLABLE VALUES*/
				if (!empty($data[$localeName]['name'])) {
					$entity->setName(trim($data[$localeName]['name']));
				}

				/* BLOCK FOR NON TRANSLATED VALUES*/
				if ($localeName == $this->_defaultLocale) {
					// NON TRANSLATED STRING
					$entity->setSlug(!empty($data[$localeName]['slug']) ? trim($data[$localeName]['slug']) : null);

					// ONE TO ONE
					$selectedDomains = array();
					foreach($data[$localeName]['type_domain'] as $domain => $value){
						if (!empty($value)) {
							$selectedDomains[] = $domain;
						}
					}
					$domains = $em->getRepository('AppBundle\Entity\BookDomain')->findBy( array( 'id'=> $selectedDomains) );
					$entity->setTypeDomain($domains);

					// IMAGE
					$image = $em->getRepository('AppBundle\Entity\Files')->findOneBy( array( 'id'=> $data[$localeName]['image']) );
					if ($image) {
						$entity->setImage($image);
					} else {
						$entity->setImage(null);
					}
				}

				$em->persist($entity);
				$em->flush();
			}
		}


		return true;
	}
}
