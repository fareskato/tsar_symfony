<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\FrontSlider;
use AppBundle\Service\Locales;
use AppBundle\Service\Slider;
use Doctrine\Common\Annotations\AnnotationReader;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SliderController extends Controller
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
     * @Route("/slider", name="admin_slider")
     */
    public function indexAction(Request $request)
    {

		$em = $this->getDoctrine()->getManager();

		// Get list of all entities
//		$data['data_list'] = $em->getRepository('AppBundle\Entity\FrontSlider')->findBy(array(),array('reorder'=>'ASC'));
		$data['data_list'] = $em->getRepository('AppBundle\Entity\FrontSlider')->findAllForAdminList($em,'AppBundle\Entity\FrontSlider',$request);


		//Get field to display
		$data['data_fields'] = array('id','image','name1','name2','color','position','active_domain','active_lang');

		//Tranlate in AdminBundle/Resources/translations/messages.en.yml
		$data['data_title'] = 'adm.slider';

		$data['data_ajax_order'] = $this->generateUrl('admin_slider_order', array(), UrlGeneratorInterface::ABSOLUTE_PATH);


		//Buttons in top and bottom
		$data['data_buttons'] = array(
			array(
				'name' => 'add',
				'link' => $this->generateUrl('admin_slider_add', array(), UrlGeneratorInterface::ABSOLUTE_PATH),
				'class' => 'primary'
			)
		);

		//Buttons of action on each entity
		$data['data_actions'] = array(
				array(
					'name' => 'edit',
					'link' => 'admin_slider_edit',
					'class' => '',
					'confirm' => false
				),
				array(
					'name' => 'delete',
					'link' => 'admin_slider_delete',
					'class' => 'danger',
					'confirm' => 'adm.action.delete.confirm'
				),
			);

		//RENDER TEMPLATE
        return $this->render('AdminBundle:Default:list.html.twig',$data);
    }

	/**
	 * @Route("/slider/add", name="admin_slider_add")
	 * @Route("/slider/edit/{id}", defaults={"id" = 0}, name="admin_slider_edit")
	 */
	public function editAction($id = 0, Request $request)
	{

		$em = $this->getDoctrine()->getManager();

		//ALWAYS CREATE NEW ONE
		$entity = new FrontSlider();
		$entity->setDefaultLocale($this->_defaultLocale);
		if (!empty($id)) {
			$entity = $em->getRepository('AppBundle\Entity\FrontSlider')->findOneBy(array('id'=>$id));
		}

		//MOVE ENTITY TO FRONT
		$data['entity'] = $entity;

		//TITLE FOR PAGE
		$data['data_title'] = (!empty($data['entity']->translate()->getName1()) ? $data['entity']->translate()->getName1() : $this->get('translator')->trans('adm.action.new'));

		//TYPE FOR PAGE
		$data['data_type'] =  'adm.slider.slider_name';

		//BUTTONS
		$data['data_buttons'] = array(
			array(
				'name' => 'save',
				'link' => 'admin_slider_edit',
				'class' => 'primary',
				'button' => true,
				'button_type' => 'submit'
			),
			array(
				'name' => 'cancel',
				'link' => !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->generateUrl('admin_slider', array(), UrlGeneratorInterface::ABSOLUTE_PATH),
				'class' => 'default',
			)
		);

		//IF SAVE
		if ($request->isMethod('POST')) {
			$data = $request->request->all();

			$this->saveEntity($data,$entity,$em);

			return $this->redirectToRoute('admin_slider', array());
		}

		//FORM CREATION
		$data['form'] = $this->createEntityForm($entity,$id,$em);


		return $this->render('AdminBundle:Default:form.html.twig',$data);

	}

	/**
	 * @Route("/slider/delete/{id}", name="admin_slider_delete")
	 */
	public function deleteAction($id)
	{
		$em = $this->getDoctrine()->getManager();

		$data = $em->getRepository('AppBundle\Entity\FrontSlider')->findOneBy(array('id' => intval($id)));
		if ($data) {
			$em->remove($data);
			$em->flush();
		}

		return !empty($_SERVER['HTTP_REFERER']) ? $this->redirect($_SERVER['HTTP_REFERER']) : $this->generateUrl('admin_slider', array(), UrlGeneratorInterface::ABSOLUTE_PATH);
	}

	/**
	 * @Route("/slider/order", name="admin_slider_order")
	 */
	public function orderAction(Request $request)
	{
		$result = array();
		$em = $this->getDoctrine()->getManager();

		if ($request->isMethod('POST')) {
			$data = $request->request->all();
			if ($data['order_by']) {
				foreach ($data['order_by'] as $orderItem) {
					$item = $em->getRepository('AppBundle\Entity\FrontSlider')->findOneBy(array('id'=>$orderItem['id']));
					$item->setReorder($orderItem['order']+1);
					$em->persist($item);
				}
				$em->flush();
			}
		}

		echo json_encode($result);
		die();

	}

	//CREATE FORM FOR ENTITY
	private function createEntityForm($entity, $id =0, $em) {



		//WHERE TO SAVE
		if (!empty($id)) {
			$form['action'] = $this->generateUrl('admin_slider_edit', array('id' => $id), UrlGeneratorInterface::ABSOLUTE_PATH);
		} else{
			$form['action'] = $this->generateUrl('admin_slider_add', array(), UrlGeneratorInterface::ABSOLUTE_PATH);
		}

		//ID OF ENTITY
		$form['id'] = $id;
		$form['form_id'] = 'form_'.md5('slider');
		$form['separator'] = true;


		$enumValues = array(
			'desktop'=>'adm.field.desktop',
			'mobile'=>'adm.field.mobile',
		);

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

		foreach($this->_locales as $lng) {

			$fields = array(
				'name1' => array(
					'label' => 'adm.field.name1',
					'type' => 'text',
					'name' => $lng.'[name1]',
					'required' => ($lng != $this->_defaultLocale ? false : true),
                    'value' => $entity ? $entity->translate($lng, ($lng == $this->_defaultLocale ? true : false))->getName1() : '',
					'translate' => true
				),
				'name2' => array(
					'label' => 'adm.field.name2',
					'type' => 'text',
					'name' => $lng.'[name2]',
					'required' => false,
                    'value' => $entity ? $entity->translate($lng, ($lng == $this->_defaultLocale ? true : false))->getName2() : '',
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
				'external' => array(
					'label' => 'adm.field.external',
					'type' => 'checkbox',
					'name' => $lng.'[external]',
					'value' => 1,
					'checked' => $entity->isExternal() ? true : false,
					'translate' => false
				),
				'active' => array(
					'label' => 'adm.field.active',
					'type' => 'checkbox',
					'name' => $lng.'[active]',
					'value' => 1,
					'checked' => $entity ? ($entity->translate($lng, ($lng == $this->_defaultLocale ? true : false))->isActive() ? 1 : 0) : 0,
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
				'separator_image' => array(
					'label' => 'adm.field.image',
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
				'separator_other' => array(
					'label' => 'adm.field.other',
					'type' => 'separator',
					'translate' => false
				),
				'position' => array(
					'label' => 'adm.field.position',
					'type' => 'select',
					'name' => $lng.'[position]',
					'required' => false,
                    'value' => $entity->getPosition() ? $entity->getPosition() : null,
					'value_default' => 'adm.field.select.position',
					'value_zero' => false,
					'values' => $enumValues,
					'translate' => false
				),
				'color' => array(
					'label' => 'adm.field.color',
					'type' => 'text',
					'name' => $lng.'[color]',
					'required' => false,
                    'value' => $entity->getColor() ? $entity->getColor() : null,
					'translate' => false
				)
			);

			//$fields = array_merge_recursive($fields,$fields2);

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
				if (!empty($data[$localeName]['name1'])) {
                    $entity->translate($localeName,false)->setName1(trim($data[$localeName]['name1']));
				}

				if (!empty($data[$localeName]['name2'])) {
                    $entity->translate($localeName,false)->setName2(trim($data[$localeName]['name2']));
				}

				if (!empty($data[$localeName]['body'])) {
                    $entity->translate($localeName,false)->setBody(trim($data[$localeName]['body']));
				}
				if (!empty($data[$localeName]['slug'])) {
                    $entity->translate($localeName,false)->setSlug(trim($data[$localeName]['slug']));
				}
				if (!empty($data[$localeName]['name1']) && !empty($data[$localeName]['slug'])) {
					$entity->translate($localeName, false)->setActive(!empty($data[$localeName]['active']) ? 1 : 0);
				}


				/* BLOCK FOR NON TRANSLATED VALUES*/
				if ($localeName == $this->_defaultLocale) {
					// NON TRANSLATED STRING
					$entity->setColor(!empty($data[$localeName]['color']) ? trim($data[$localeName]['color']) : null);
					$entity->setPosition(!empty($data[$localeName]['position']) ? trim($data[$localeName]['position']) : null);
					$entity->setExternal(!empty($data[$localeName]['external']) ? true : false);

					// ONE TO MANY
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

					if ( empty($entity->getId()) ) {
						$qb = $em->createQueryBuilder()
							->select('MAX(u.reorder)')
							->from('AppBundle\Entity\FrontSlider', 'u');
						$res =  $qb->getQuery()->getSingleScalarResult();
						$entity->setReorder($res+1);
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
