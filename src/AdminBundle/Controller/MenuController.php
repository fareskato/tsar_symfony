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

class MenuController extends Controller
{
	
	private $_locales;
	private $_defaultLocale;

	public function __construct()
	{
		$loc = new Locales();
		$this->_locales = $loc->getLocales();
		$this->_defaultLocale = $loc->getDefaultLocale();
	}

	/**
     * @Route("/menu", name="admin_menu")
     */
    public function indexAction(Request $request)
    {

		$em = $this->getDoctrine()->getManager();

		$data['data_list'] = $em->getRepository('AppBundle\Entity\Menu')->getRootNodes('reorder');
		$data['data_fields'] = array('id','name','slug','type','active_domain','active_lang');
		$data['data_title'] = 'adm.menu';
		$data['data_book_name'] = '';
		$data['data_ajax_nested'] = $this->generateUrl('admin_menu_order', array(), UrlGeneratorInterface::ABSOLUTE_PATH);

		$link = $this->generateUrl('admin_menu_add', array(), UrlGeneratorInterface::ABSOLUTE_PATH);

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
					'link' => 'admin_menu_edit',
					'class' => '',
					'confirm' => false
				),
				/*array(
					'name' => 'activate',
					'link' => 'admin_menu_activate',
					'class' => 'info',
					'confirm' => false
				),*/
				array(
						'name' => 'delete',
						'link' => 'admin_menu_delete',
						'class' => 'danger',
						'confirm' => 'adm.action.delete.confirm'
					),
			);

        return $this->render('AdminBundle:Default:tree.html.twig',$data);
    }

	/**
	 * @Route("/menu/add", name="admin_menu_add")
	 * @Route("/menu/edit/{id}", defaults={"id" = 0}, name="admin_menu_edit")
	 */
	public function editAction($id = 0, Request $request)
	{

		$em = $this->getDoctrine()->getManager();

		$entity = new Menu();
		$entity->setDefaultLocale($this->_defaultLocale);
		if (!empty($id)) {
			$entity = $em->getRepository('AppBundle\Entity\Menu')->findOneBy(array('id'=>$id));
		}

		$data['entity'] = $entity;
		$data['data_title'] = (!empty($data['entity']->translate()->getName()) ? $data['entity']->translate()->getName() : $this->get('translator')->trans('adm.action.new'));

		$data['data_type'] =  'adm.menu.menu_name';

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

		if ($request->isMethod('POST')) {
			$data = $request->request->all();

			$this->saveEntity($data,$entity,$em);

			return $this->redirectToRoute('admin_menu', array());
		}

		$data['form'] = $this->createEntityForm($entity,$id,$em);


		return $this->render('AdminBundle:Default:form.html.twig',$data);

	}

	/**
	 * @Route("/menu/delete/{id}", name="admin_menu_delete")
	 */
	public function deleteAction($id)
	{
		$em = $this->getDoctrine()->getManager();

		$data = $em->getRepository('AppBundle\Entity\Menu')->findOneBy(array('id' => intval($id)));
		if ($data) {
			$em->remove($data);
			$em->flush();
		}

		return !empty($_SERVER['HTTP_REFERER']) ? $this->redirect($_SERVER['HTTP_REFERER']) : $this->generateUrl('admin_menu', array(), UrlGeneratorInterface::ABSOLUTE_PATH);
	}

	/**
	 * @Route("/menu/activate/{id}", name="admin_menu_activate")
	 */
	public function activateAction($id)
	{
		$em = $this->getDoctrine()->getManager();

		$data = $em->getRepository('AppBundle\Entity\Menu')->findOneBy(array('id' => intval($id)));
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

	/**
	 * @Route("/menu/order", name="admin_menu_order")
	 */
	public function orderAction(Request $request)
	{
		$result = array();
		$em = $this->getDoctrine()->getManager();

		if ($request->isMethod('POST')) {
			$data = $request->request->all();

			if ($data['order_by']) {
				foreach ($data['order_by'] as $orderItem) {
					$item = $em->getRepository('AppBundle\Entity\Menu')->findOneBy(array('id'=>$orderItem['id']));
					$item->setReorder($orderItem['order']+1);
					$em->persist($item);
				}
				$em->flush();
			}

			if ($data['new_order']) {
				$lines = json_decode($data['new_order']);
				foreach($lines as $line) {
					$this->reOrder(null, $line);
				}
			}
		}

		echo json_encode($result);
		die();

	}

	private function reOrder($parent_id = null, $line) {
		$em = $this->getDoctrine()->getManager();

		$itemMenu = $em->getRepository('AppBundle\Entity\Menu')->findOneBy(array('id'=>$line->id));
		if ($parent_id) {
			$parent_id = $em->getRepository('AppBundle\Entity\Menu')->findOneBy(array('id'=>$parent_id));
		}
		$itemMenu->setParent($parent_id);
		$em->persist($itemMenu);
		$em->flush();

		if (!empty($line->children)) {
			foreach($line->children as $child) {
				$this->reOrder($line->id, $child);
			}
		}
		return;
	}

	private function buildSelectFromValues($array, $prefix = '') {
		$select = array();
		foreach($array as $item) {
			$select[$item->getId()] = ($prefix ? $prefix . ' ' : '').$item->translate()->getName();
			if (!empty($item->getChildren())) {
				$select = array_replace_recursive($select,$this->buildSelectFromValues($item->getChildren()->toArray(),'-'.$prefix));
			}
		}
		return $select;
	}

	private function createEntityForm($entity, $id =0, $em) {

		$allMenu = $em->getRepository('AppBundle\Entity\Menu')->getRootNodes('reorder');;
		$parentValues = $this->buildSelectFromValues($allMenu);

		$enumValues = array(
			'url'=>'adm.field.url',
			'category'=>'adm.field.category',
			'separator'=>'adm.field.separator'
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

		if (!empty($id)) {
			$form['action'] = $this->generateUrl('admin_menu_edit', array('id' => $id), UrlGeneratorInterface::ABSOLUTE_PATH);
		} else{
			$form['action'] = $this->generateUrl('admin_menu_add', array(), UrlGeneratorInterface::ABSOLUTE_PATH);
		}

		$form['id'] = $id;
		$form['form_id'] = 'form_'.md5('menu');

		foreach($this->_locales as $lng) {

			$fields = array(
				'name' => array(
					'label' => 'adm.field.name',
					'type' => 'text',
					'name' => $lng.'[name]',
					'required' => ($lng != $this->_defaultLocale ? false : true),
					'value' => $entity ? $entity->translate($lng, ($lng == $this->_defaultLocale ? true : false))->getName() : '',
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
				'type' => array(
					'label' => 'adm.field.type',
					'type' => 'select',
					'name' => $lng.'[type]',
					'required' => false,
					'value' => $entity->getType() ? $entity->getType() : null,
					'value_zero' => false,
					'value_default' => '',
					'values' => $enumValues,
					'translate' => false
				),
				'parent' => array(
					'label' => 'adm.field.parent',
					'type' => 'select',
					'name' => $lng.'[parent]',
					'required' => false,
					'value' => $entity->getParent() ? $entity->getParent()->getId() : null,
					'value_default' => 'adm.field.select.toplevel',
					'value_zero' => true,
					'values' => $parentValues,
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
				'external' => array(
					'label' => 'adm.field.external',
					'type' => 'checkbox',
					'name' => $lng.'[external]',
					'value' => 1,
					'checked' => $entity->isExternal() ? true : false,
					'translate' => false
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
					'checked_values' => $checked_values,
					'translate' => false
				),
				'separator_other' => array(
					'label' => 'adm.field.other',
					'type' => 'separator',
					'translate' => false
				),
				'class' => array(
					'label' => 'adm.field.class',
					'type' => 'text',
					'name' => $lng.'[class]',
					'required' => false,
					'value' => $entity ? $entity->getClass() : '',
					'translate' => false
				),
			);

			$fieldset = 'translate';
			if ($lng == $this->_defaultLocale) {
				$fieldset = 'default';
			}
			$form[$fieldset][$lng] = $fields;
		}
		return $form;
	}

	private function saveEntity($data,$entity,$em){
		foreach ($data as $localeName => $locale) {
			if (in_array($localeName,$this->_locales)) {
				$entity->setTranslatableLocale($localeName);

				/* BLOCK FOR TRANSLABLE VALUES*/
				if (!empty($data[$localeName]['name'])) {
					$entity->translate($localeName,false)->setName(trim($data[$localeName]['name']));
				}
				if (!empty($data[$localeName]['slug'])) {
					$entity->translate($localeName,false)->setSlug(trim($data[$localeName]['slug']));
				}
				if (!empty($data[$localeName]['name'])) {
					$entity->translate($localeName, false)->setActive(!empty($data[$localeName]['active']) ? 1 : 0);
				}

				/* BLOCK FOR NON TRANSLATED VALUES*/
				if ($localeName == $this->_defaultLocale) {
					$entity->setType($data[$localeName]['type']);
					$entity->setExternal(!empty($data[$localeName]['external']) ? 1 : 0);
					if (!empty($data[$localeName]['parent'])) {
						$entityParent = $em->getRepository('AppBundle\Entity\Menu')->findOneBy(array('id'=>$data[$localeName]['parent']));
						$entity->setParent($entityParent);
					} else {
						$entity->setParent(null);
					}

					$selectedDomains = array();
					foreach($data[$localeName]['type_domain'] as $domain => $value){
						if (!empty($value)) {
							$selectedDomains[] = $domain;
						}
					}
					$domains = $em->getRepository('AppBundle\Entity\BookDomain')->findBy( array( 'id'=> $selectedDomains) );
					$entity->setTypeDomain($domains);

				}

				$entity->mergeNewTranslations();


			}
		}

		$em->persist($entity);
		$em->flush();

		return true;
	}
}
