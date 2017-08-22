<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\Destination;
use AppBundle\Service\Locales;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Query;
use Gedmo\Translatable\Query\TreeWalker\TranslationWalker;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DestinationController extends Controller
{

	private $_itemsOnPage = 20;

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
     * @Route("/destination", name="admin_destination")
     */
    public function indexAction(Request $request)
    {

		$em = $this->getDoctrine()->getManager();

		// Get list of all entities
//		$data['data_list'] = $em->getRepository('AppBundle\Entity\Destination')->findBy(array(),array('id'=>'desc'));
		$data['data_list'] = $em->getRepository('AppBundle\Entity\Destination')->findAllForAdminList($em,'AppBundle\Entity\Destination',$request);


		//Get field to display
		$data['data_fields'] = array('id','image','name','slug','type_destination','master_destination','active_domain');

		//Tranlate in AdminBundle/Resources/translations/messages.en.yml
		$data['data_title'] = 'adm.destination';

		//Buttons in top and bottom
		$data['data_buttons'] = array(
			array(
				'name' => 'add',
				'link' => $this->generateUrl('admin_destination_add', array(), UrlGeneratorInterface::ABSOLUTE_PATH),
				'class' => 'primary'
			)
		);

		//Buttons of action on each entity
		$data['data_actions'] = array(
				array(
					'name' => 'edit',
					'link' => 'admin_destination_edit',
					'class' => '',
					'confirm' => false
				),
				array(
					'name' => 'delete',
					'link' => 'admin_destination_delete',
					'class' => 'danger',
					'confirm' => 'adm.action.delete.confirm'
				),
			);

		$page = !empty($request->query->get('page')) ? intval($request->query->get('page')) : 1;
		$paginator = array(
			'currentFilters' => $request->query->all(),
			'currentPage' => $page,
			'paginationPath' => 'admin_destination',
			'showAlwaysFirstAndLast' => true,
			'lastPage' => ceil(count($data['data_list']) / $this->_itemsOnPage),
		);

		$data['data_list'] = array_slice($data['data_list'], ($page * $this->_itemsOnPage - $this->_itemsOnPage), $this->_itemsOnPage);
		$data = array_merge($data,$paginator);

		//RENDER TEMPLATE
        return $this->render('AdminBundle:Default:list.html.twig',$data);
    }

	/**
	 * @Route("/destination/add", name="admin_destination_add")
	 * @Route("/destination/edit/{id}", defaults={"id" = 0}, name="admin_destination_edit")
	 */
	public function editAction($id = 0, Request $request)
	{

		$em = $this->getDoctrine()->getManager();

		//ALWAYS CREATE NEW ONE
		$entity = new Destination();
		$entity->setDefaultLocale($this->_defaultLocale);

		if (!empty($id)) {
			$entity = $em->getRepository('AppBundle\Entity\Destination')->findOneBy(array('id'=>$id));
		}

		//MOVE ENTITY TO FRONT
		$data['entity'] = $entity;

		//TITLE FOR PAGE
		$data['data_title'] = (!empty($data['entity']->translate()->getName()) ? $data['entity']->translate()->getName() : $this->get('translator')->trans('adm.action.new'));

		//TYPE FOR PAGE
		$data['data_type'] =  'adm.destination.destination_name';

		//BUTTONS
		$data['data_buttons'] = array(
			array(
				'name' => 'save',
				'link' => 'admin_destination_edit',
				'class' => 'primary',
				'button' => true,
				'button_type' => 'submit'
			),
			array(
				'name' => 'cancel',
				'link' => !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->generateUrl('admin_destination', array(), UrlGeneratorInterface::ABSOLUTE_PATH),
				'class' => 'default',
			)
		);

		//IF SAVE
		if ($request->isMethod('POST')) {
			$data = $request->request->all();

			$this->saveEntity($data,$entity,$em);

			return $this->redirectToRoute('admin_destination', array());
		}

		//FORM CREATION
		$data['form'] = $this->createEntityForm($entity,$id,$em);


		return $this->render('AdminBundle:Default:form.html.twig',$data);

	}

	/**
	 * @Route("/destination/delete/{id}", name="admin_destination_delete")
	 */
	public function deleteAction($id)
	{
		$em = $this->getDoctrine()->getManager();

		$data = $em->getRepository('AppBundle\Entity\Destination')->findOneBy(array('id' => intval($id)));
		if ($data) {
			$em->remove($data);
			$em->flush();
		}

		return !empty($_SERVER['HTTP_REFERER']) ? $this->redirect($_SERVER['HTTP_REFERER']) : $this->generateUrl('admin_destination', array(), UrlGeneratorInterface::ABSOLUTE_PATH);
	}
	//CREATE FORM FOR ENTITY
	private function createEntityForm($entity, $id =0, $em) {

		//WHERE TO SAVE
		if (!empty($id)) {
			$form['action'] = $this->generateUrl('admin_destination_edit', array('id' => $id), UrlGeneratorInterface::ABSOLUTE_PATH);
		} else{
			$form['action'] = $this->generateUrl('admin_destination_add', array(), UrlGeneratorInterface::ABSOLUTE_PATH);
		}

		//ID OF ENTITY
		$form['id'] = $id;
		$form['form_id'] = 'form_'.md5('type');

		$form['separator'] = true; // true/false;

		$typeDestinationValues = array();
		$typeDestination = $em->getRepository('AppBundle\Entity\BookTypeDestination')->findAll();



		foreach($typeDestination as $d) {
			$typeDestinationValues[ $d->getId() ] = $d->translate()->getName();
		}

		$domains = $em->getRepository('AppBundle\Entity\BookDomain')->findAll();
		$domainValues = array();
		foreach ($domains as $domain ) {
            $domainValues[$domain->getId()] = $domain->translate()->getName();
		}
		$checked_values = array();
		if (!empty($entity->getTypeDomain())) {
			foreach($entity->getTypeDomain() as $domain) {
				$checked_values[] = $domain->getId();
			}
		}

		$otherImages = $entity->getImageOther() ? $entity->getImageOther() : array();
		$otherImagesValue = array();
		foreach($otherImages as $i) {
			$otherImagesValue[] = array(
				'path' => $i->getUrl(),
				'value' => $i->getId(),
			);
		}

		$etiquette = $em->getRepository('AppBundle\Entity\BookEtiquette')->findAll();
		$etiquetteValues = array();
		foreach ($etiquette as $i ) {
			$etiquetteValues[$i->getId()] = $i->getName();
		}

		foreach($this->_locales as $lng) {

			$fields = array(
				'type_destination' => array(
					'label' => 'adm.field.type_destination',
					'type' => 'select',
					'name' => $lng.'[type_destination]',
					'required' => false,
					'value' => $entity->getTypeDestination() ? $entity->getTypeDestination()->getId() : null,
					'value_zero' => false,
					'value_default' => '',
					'values' => $typeDestinationValues,
					'translate' => false
				),
				'name' => array(
					'label' => 'adm.field.name',
					'type' => 'text',
					'name' => $lng.'[name]',
					'required' => ($lng != $this->_defaultLocale ? false : true),
                    'value' => $entity ? $entity->translate($lng, ($lng == $this->_defaultLocale ? true : false))->getName() : '',
					'translate' => true
				),
				'body_summary' => array(
					'label' => 'adm.field.body_summary',
					'type' => 'textarea',
					'name' => $lng.'[body_summary]',
					'required' => false,
                    'value' => $entity ? $entity->translate($lng, ($lng == $this->_defaultLocale ? true : false))->getBodySummary() : '',
					'translate' => true
				),
				'body' => array(
					'label' => 'adm.field.body',
					'type' => 'texteditor',
					'name' => $lng.'[body]',
					'required' => false,
                    'value' => $entity ? $entity->translate($lng, ($lng == $this->_defaultLocale ? true : false))->getBody() : '',
					'translate' => true
				),
				'slug' => array(
					'label' => 'adm.field.slug',
					'type' => 'text',
					'name' => $lng.'[slug]',
					'required' => false,
                    'value' => $entity ? $entity->translate($lng, ($lng == $this->_defaultLocale ? true : false))->getSlug() : '',
					'translate' => true
				),
				'separator_domain' => array(
					'label' => 'adm.field.domains',
					'type' => 'separator',
					'translate' => false
				),
				'type_domain' => array(
					'label' => 'adm.field.domains',
					'type' => 'checkbox_multiple',
					'name' => $lng.'[type_domain]',
					'values' => $domainValues,
					'checked_values' => $checked_values, //only checked values array
					'translate' => false
				),
				'master_destination' => array(
					'label' => 'adm.field.master_destination',
					'type' => 'checkbox',
					'name' => $lng.'[master_destination]',
					'value' => 1,
					'checked' => $entity->getMasterDestination() ? true : false,
					'translate' => false
				),
				'present_in_list' => array(
					'label' => 'adm.field.present_in_list',
					'type' => 'checkbox',
					'name' => $lng.'[present_in_list]',
					'value' => 1,
					'checked' => $entity->getPresentInList() ? true : false,
					'translate' => false
				),
				'etiquette' => array(
					'label' => 'adm.field.etiquette',
					'type' => 'radio',
					'name' => $lng.'[etiquette]',
					'values' => $etiquetteValues,
					'value_zero' => true,
					'checked_value' =>  $entity->getEtiquette() ? $entity->getEtiquette()->getId() : false,
					'translate' => false
				),
				'separator_places' => array(
					'label' => 'adm.field.places',
					'type' => 'separator',
					'translate' => false
				),
				'parent' => array(
					'label' => 'adm.field.parent',
					'type' => 'relation_many',
					'autocomplete' => 'parent',
					'autocomplete_path' => 'admin_autocomplete_destination',
					'name' => $lng.'[parent][]',
					'add' => 'admin_destination_add',
					'field_rel' => array('id','image','name'),
					'values' => $entity->getParent() ? $entity->getParent() : array(),
					'translate' => false,
					'editLink' => array(
						'type' => 'destination',
						'path' => array(),
					),
				),
				'location' => array(
					'label' => 'adm.field.location',
					'type' => 'relation_one',
					'autocomplete' => 'location',
					'autocomplete_path' => 'admin_autocomplete_location',
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
				'separator_images' => array(
					'label' => 'adm.field.images',
					'type' => 'separator',
					'translate' => false
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
				'image_header' => array(
					'label' => 'adm.field.image.header',
					'type' => 'image',
					'name' => $lng.'[image_header]',
					'path' => $entity->getImageHeader() ? $entity->getImageHeader()->getUrl() : false,
					'value' => $entity->getImageHeader() ? $entity->getImageHeader()->getId() : false,
					'required' => false,
					'translate' => false
				),
				'image_background' => array(
					'label' => 'adm.field.image.background',
					'type' => 'image',
					'name' => $lng.'[image_background]',
					'path' => $entity->getImageBackground() ? $entity->getImageBackground()->getUrl() : false,
					'value' => $entity->getImageBackground() ? $entity->getImageBackground()->getId() : false,
					'required' => false,
					'translate' => false
				),
				'image_panorama' => array(
					'label' => 'adm.field.image.panorama',
					'type' => 'image',
					'name' => $lng.'[image_panorama]',
					'path' => $entity->getImagePanorama() ? $entity->getImagePanorama()->getUrl() : false,
					'value' => $entity->getImagePanorama() ? $entity->getImagePanorama()->getId() : false,
					'required' => false,
					'translate' => false
				),
				'image_others' => array(
					'label' => 'adm.field.image.others',
					'type' => 'images',
					'name' => $lng.'[image_others][]',
					'values' => $otherImagesValue,
					'maximum' => 10,
					'required' => false,
					'translate' => false
				),
				'separator_seo' => array(
					'label' => 'adm.field.seo',
					'type' => 'separator',
					'translate' => false
				),
				'keywords' => array(
					'label' => 'adm.field.keywords',
					'type' => 'text',
					'name' => $lng.'[keywords]',
					'required' => false,
                    'value' => $entity ? $entity->translate($lng, ($lng == $this->_defaultLocale ? true : false))->getKeywords() : '',
					'translate' => true
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
//echo'<pre>'; print_r($data); exit;
		//SAVE ENTITY ACCORDING TO LOCALE
		foreach ($data as $localeName => $locale) {
			if (in_array($localeName,$this->_locales)) {
				$entity->setTranslatableLocale($localeName);

				/* BLOCK FOR TRANSLABLE VALUES*/
				if (!empty($data[$localeName]['name'])) {
                    $entity->translate($localeName,false)->setName(trim($data[$localeName]['name']));
				}
				if (!empty($data[$localeName]['body_summary'])) {
                    $entity->translate($localeName,false)->setBodySummary(trim($data[$localeName]['body_summary']));
				}
				if (!empty($data[$localeName]['body'])) {
                    $entity->translate($localeName,false)->setBody(trim($data[$localeName]['body']));
				}
				if (!empty($data[$localeName]['keywords'])) {
                    $entity->translate($localeName,false)->setkeywords(trim($data[$localeName]['keywords']));
				}
				if (!empty($data[$localeName]['slug'])) {
                    $entity->translate($localeName,false)->setSlug(trim($data[$localeName]['slug']));
				}

				/* BLOCK FOR NON TRANSLATED VALUES*/
				if ($localeName == $this->_defaultLocale) {

					// NON TRANSLATED STRING
					$entity->setMasterDestination(!empty($data[$localeName]['master_destination']) ? 1 : 0);
					$entity->setPresentInList(!empty($data[$localeName]['present_in_list']) ? 1 : 0);


					if (!empty($data[$localeName]['type_destination'])) {
						$typeDestination = $em->getRepository('AppBundle\Entity\BookTypeDestination')->findOneBy( array( 'id'=> $data[$localeName]['type_destination']) );
						$entity->setTypeDestination($typeDestination);
					} else {
						$entity->setTypeDestination(null);
					}

					if (!empty($data[$localeName]['parent'])) {

                        $parentDestination = $em->getRepository('AppBundle\Entity\Destination')->findBy( array( 'id'=> $data[$localeName]['parent']) );
                        $entity->setParent($parentDestination);
                    } else {
                        $entity->setParent(null);
                    }

                    if (!empty($data[$localeName]['location'])) {
                        $location = $em->getRepository('AppBundle\Entity\Location')->findOneBy( array( 'id'=> $data[$localeName]['location']) );

                        $entity->setLocation($location);
                    } else {
                        $entity->setLocation(null);
                    }


					// ONE TO MANY
					if (!empty($data[$localeName]['type_domain'])) {
						$selectedDomains = array();
						foreach($data[$localeName]['type_domain'] as $domain => $value){
							if (!empty($value)) {
								$selectedDomains[] = $domain;
							}
						}
						$domains = $em->getRepository('AppBundle\Entity\BookDomain')->findBy( array( 'id'=> $selectedDomains) );
						$entity->setTypeDomain($domains);
					} else {
						$entity->setTypeDomain(null);
					}



					// IMAGE
					$image = $em->getRepository('AppBundle\Entity\Files')->findOneBy( array( 'id'=> $data[$localeName]['image']) );
					if ($image) {
						$entity->setImage($image);
					} else {
						$entity->setImage(null);
					}

					$headerImage = $em->getRepository('AppBundle\Entity\Files')->findOneBy( array( 'id'=> $data[$localeName]['image_header']) );
					if ($headerImage) {
						$entity->setImageHeader($headerImage);
					} else {
						$entity->setImageHeader(null);
					}

					$backgroundImage = $em->getRepository('AppBundle\Entity\Files')->findOneBy( array( 'id'=> $data[$localeName]['image_background']) );
					if ($backgroundImage) {
						$entity->setImageBackground($backgroundImage);
					} else {
						$entity->setImageBackground(null);
					}

					$panoramaImage = $em->getRepository('AppBundle\Entity\Files')->findOneBy( array( 'id'=> $data[$localeName]['image_panorama']) );
					if ($panoramaImage) {
						$entity->setImagePanorama($panoramaImage);
					} else {
						$entity->setImagePanorama(null);
					}

					if (!empty($data[$localeName]['image_others'])) {
						$otherImages = array_filter($data[$localeName]['image_others'], function($value) { return $value !== ''; });
						$otherImage = $em->getRepository('AppBundle\Entity\Files')->findBy( array( 'id'=> $otherImages ) );
						$entity->setImageOthers($otherImage);
					} else {
						$entity->setImageOthers(null);
					}

					if (!empty($data[$localeName]['etiquette'])) {
						$internet = $em->getRepository('AppBundle\Entity\BookEtiquette')->findOneBy( array( 'id'=> $data[$localeName]['etiquette']) );
						$entity->setEtiquette($internet);
					} else {
						$entity->setEtiquette(null);
					}
				}

                $entity->mergeNewTranslations();
			}
		}
        $em->persist($entity);
        $em->flush();

		return true;
	}

}
