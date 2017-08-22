<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\Partners;
use AppBundle\Service\Locales;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PartnersController extends Controller
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
	 * @Route("/partners", name="admin_partners")
	 */
	public function indexAction(Request $request)
	{

		$em = $this->getDoctrine()->getManager();


//		$data['data_list'] = $em->getRepository('AppBundle\Entity\Partners')->findBy(array(), array('id' => 'DESC'));
		$data['data_list'] = $em->getRepository('AppBundle\Entity\Partners')->findAllForAdminList($em,'AppBundle\Entity\Partners',$request);


		$data['data_fields'] = array('id', 'image', 'name', 'link', 'active_lang');
		$data['data_title'] = 'adm.partners';
		$data['data_book_name'] = '';

		$link = $this->generateUrl('admin_partners_add', array(), UrlGeneratorInterface::ABSOLUTE_PATH);

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
				'link' => 'admin_partners_edit',
				'class' => '',
				'confirm' => false
			),
			/*array(
			  'name' => 'activate',
			  'link' => 'admin_partners_activate',
			  'class' => 'info',
			  'confirm' => false
			),*/
			array(
				'name' => 'delete',
				'link' => 'admin_partners_delete',
				'class' => 'danger',
				'confirm' => 'adm.action.delete.confirm'
			),
		);

		$page = !empty($request->query->get('page')) ? intval($request->query->get('page')) : 1;
		$paginator = array(
			'currentFilters' => $request->query->all(),
			'currentPage' => $page,
			'paginationPath' => 'admin_partners',
			'showAlwaysFirstAndLast' => true,
			'lastPage' => ceil(count($data['data_list']) / $this->_itemsOnPage),
		);

		$data['data_list'] = array_slice($data['data_list'], ($page * $this->_itemsOnPage - $this->_itemsOnPage), $this->_itemsOnPage);
		$data = array_merge($data, $paginator);

		return $this->render('AdminBundle:Default:list.html.twig', $data);
	}

	/**
	 * @Route("/partners/add", name="admin_partners_add")
	 * @Route("/partners/edit/{id}", defaults={"id" = 0}, name="admin_partners_edit")
	 */
	public function editAction($id = 0, Request $request)
	{

		$em = $this->getDoctrine()->getManager();

		$entity = new Partners();
		$entity->setDefaultLocale($this->_defaultLocale);
		if (!empty($id)) {
			$entityRecieved = $em->getRepository('AppBundle\Entity\Partners')->findOneBy(array('id' => $id));
			if (!empty($entityRecieved)) {
				$entity = $entityRecieved;
			}
		}
		$data['entity'] = $entity;
		$data['data_title'] = (!empty($data['entity']->translate()->getName()) ? $data['entity']->translate()->getName() : $this->get('translator')->trans('adm.action.new'));

		$data['data_type'] = 'adm.partners';

		$data['data_buttons'] = array(
			array(
				'name' => 'save',
				'link' => 'admin_partners_edit',
				'class' => 'primary',
				'button' => true,
				'button_type' => 'submit'
			),
			array(
				'name' => 'cancel',
				'link' => !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->generateUrl('admin_partners', array(), UrlGeneratorInterface::ABSOLUTE_PATH),
				'class' => 'default',
			)
		);

		if ($request->isMethod('POST')) {

			$data = $request->request->all();

			$this->saveEntity($data, $entity, $em);
			return $this->redirectToRoute('admin_partners', array());
		}

		$data['form'] = $this->createEntityForm($entity, $id, $em);


		return $this->render('AdminBundle:Default:form.html.twig', $data);

	}

	/**
	 * @Route("/partners/delete/{id}", name="admin_partners_delete")
	 */
	public function deleteAction($id)
	{
		$em = $this->getDoctrine()->getManager();

		$data = $em->getRepository('AppBundle\Entity\Partners')->findOneBy(array('id' => intval($id)));
		if ($data) {
			$em->remove($data);
			$em->flush();
		}

		return !empty($_SERVER['HTTP_REFERER']) ? $this->redirect($_SERVER['HTTP_REFERER']) : $this->generateUrl('admin_partners', array(), UrlGeneratorInterface::ABSOLUTE_PATH);
	}


	private function createEntityForm($entity, $id = 0, $em)
	{
		if (!empty($id)) {
			$form['action'] = $this->generateUrl('admin_partners_edit', array('id' => $id), UrlGeneratorInterface::ABSOLUTE_PATH);
		} else {
			$form['action'] = $this->generateUrl('admin_partners_add', array(), UrlGeneratorInterface::ABSOLUTE_PATH);
		}

		$form['id'] = $id;
		$form['form_id'] = 'form_' . md5('partners');
		$form['separator'] = true;

		$domains = $em->getRepository('AppBundle\Entity\BookDomain')->findAll();
		$domainValues = array();
		foreach ($domains as $domain) {
			$domainValues[$domain->getId()] = $domain->translate()->getName();
		}
		$checked_values = array();
		if (!empty($entity->getTypeDomain())) {
			foreach ($entity->getTypeDomain() as $domain) {
				$checked_values[] = $domain->getId();
			}
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
				'url' => array(
					'label' => 'adm.field.url',
					'type' => 'text',
					'name' => $lng . '[url]',
					'required' => false,
					'value' => $entity ? $entity->getLink() : '',
					'translate' => false
				),
				'slug' => array(
					'label' => 'adm.field.slug',
					'type' => 'text',
					'name' => $lng . '[slug]',
					'required' => false,
					'value' => $entity ? $entity->translate($lng, ($lng == $this->_defaultLocale ? true : false))->getSlug() : '',
					'translate' => true
				),
				'active' => array(
					'label' => 'adm.field.active',
					'type' => 'checkbox',
					'name' => $lng . '[active]',
					'value' => 1,
					'checked' => $entity ? ($entity->translate($lng, ($lng == $this->_defaultLocale ? true : false))->isActive() ? 1 : 0) : 0,
					'translate' => true
				),
				'separator_image' => array(
					'label' => 'adm.field.image',
					'type' => 'separator',
					'translate' => false
				),
				'image' => array(
					'label' => 'adm.field.image',
					'type' => 'image',
					'name' => $lng . '[image]',
					'path' => $entity->getImage() ? $entity->getImage()->getUrl() : false,
					'value' => $entity->getImage() ? $entity->getImage()->getId() : false,
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
					'name' => $lng . '[keywords]',
					'required' => false,
					'value' => $entity ? $entity->translate($lng, ($lng == $this->_defaultLocale ? true : false))->getKeywords() : '',
					'translate' => true
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

	private function saveEntity($data, $entity, $em)
	{

		foreach ($data as $localeName => $locale) {
			if (in_array($localeName, $this->_locales)) {

				/* BLOCK FOR TRANSLABLE VALUES*/
				if (!empty($data[$localeName]['name'])) {
					$entity->translate($localeName, false)->setName(trim($data[$localeName]['name']));

				}
				if (!empty($data[$localeName]['body_summary'])) {
					$entity->translate($localeName, false)->setBodySummary(trim($data[$localeName]['body_summary']));
				}
				if (!empty($data[$localeName]['body'])) {
					$entity->translate($localeName, false)->setBody(trim($data[$localeName]['body']));
				}
				if (!empty($data[$localeName]['keywords'])) {
					$entity->translate($localeName, false)->setKeywords(trim($data[$localeName]['keywords']));
				}
				if (!empty($data[$localeName]['slug'])) {
					$entity->translate($localeName, false)->setSlug(trim($data[$localeName]['slug']));
				}
				if (!empty($data[$localeName]['name'])) {
					$entity->translate($localeName, false)->setActive(!empty($data[$localeName]['active']) ? 1 : 0);
				}

				/* BLOCK FOR NON TRANSLATED VALUES*/
				if ($localeName == $this->_defaultLocale) {

					$image = $em->getRepository('AppBundle\Entity\Files')->findOneBy(array('id' => $data[$localeName]['image']));
					if ($image) {
						$entity->setImage($image);
					} else {
						$entity->setImage(null);
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
