<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Service\Locales;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ArticleController extends Controller
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
     * @Route("/article", name="admin_article")
     */
    public function indexAction(Request $request)
    {

		$em = $this->getDoctrine()->getManager();


//		$data['data_list'] = $em->getRepository('AppBundle\Entity\Article')->findBy(array(), array('id' => 'DESC'));
		$data['data_list'] = $em->getRepository('AppBundle\Entity\Article')->findAllForAdminList($em,'AppBundle\Entity\Article',$request);


		$data['data_fields'] = array('id','image','name','body_summary','active_domain','active_lang');
		$data['data_title'] = 'adm.article';
		$data['data_book_name'] = '';

		$link = $this->generateUrl('admin_article_add', array(), UrlGeneratorInterface::ABSOLUTE_PATH);

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
				'link' => 'admin_article_edit',
				'class' => '',
				'confirm' => false
			),
			/*array(
				'name' => 'activate',
				'link' => 'admin_article_activate',
				'class' => 'info',
				'confirm' => false
			),*/
			array(
					'name' => 'delete',
					'link' => 'admin_article_delete',
					'class' => 'danger',
					'confirm' => 'adm.action.delete.confirm'
				),
			);

		$page = !empty($request->query->get('page')) ? intval($request->query->get('page')) : 1;
		$paginator = array(
			'currentFilters' => $request->query->all(),
			'currentPage' => $page,
			'paginationPath' => 'admin_article',
			'showAlwaysFirstAndLast' => true,
			'lastPage' => ceil(count($data['data_list']) / $this->_itemsOnPage),
		);

		$data['data_list'] = array_slice($data['data_list'], ($page * $this->_itemsOnPage - $this->_itemsOnPage), $this->_itemsOnPage);
		$data = array_merge($data,$paginator);

        return $this->render('AdminBundle:Default:list.html.twig',$data);
    }

	/**
	 * @Route("/article/add", name="admin_article_add")
	 * @Route("/article/edit/{id}", defaults={"id" = 0}, name="admin_article_edit")
	 */
	public function editAction($id = 0, Request $request)
	{

		$em = $this->getDoctrine()->getManager();

		$entity = new Article();
		$entity->setDefaultLocale($this->_defaultLocale);

		if ( !empty($id) ) {
			$entityRecieved = $em->getRepository('AppBundle\Entity\Article')->findOneBy(array('id'=>$id));
			if (!empty($entityRecieved )) {
				$entity = $entityRecieved;
			}
		}
		$data['entity'] = $entity;
		$data['data_title'] = (!empty($data['entity']->translate()->getName()) ? $data['entity']->translate()->getName() : $this->get('translator')->trans('adm.action.new'));

		$data['data_type'] =  'adm.article';

		$data['data_buttons'] = array(
			array(
				'name' => 'save',
				'link' => 'admin_article_edit',
				'class' => 'primary',
				'button' => true,
				'button_type' => 'submit'
			),
			array(
				'name' => 'cancel',
				'link' => !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->generateUrl('admin_article', array(), UrlGeneratorInterface::ABSOLUTE_PATH),
				'class' => 'default',
			)
		);

		if ($request->isMethod('POST')) {

			$data = $request->request->all();

			$this->saveEntity($data,$entity,$em);
			return $this->redirectToRoute('admin_article', array());
		}

		$data['form'] = $this->createEntityForm($entity,$id,$em);


		return $this->render('AdminBundle:Default:form.html.twig',$data);

	}

	/**
	 * @Route("/article/delete/{id}", name="admin_article_delete")
	 */
	public function deleteAction($id)
	{
		$em = $this->getDoctrine()->getManager();

		$data = $em->getRepository('AppBundle\Entity\Article')->findOneBy(array('id' => intval($id)));
		if ($data) {
			$em->remove($data);
			$em->flush();
		}

		return !empty($_SERVER['HTTP_REFERER']) ? $this->redirect($_SERVER['HTTP_REFERER']) : $this->generateUrl('admin_article', array(), UrlGeneratorInterface::ABSOLUTE_PATH);
	}

	/**
	 * @Route("/article/activate/{id}", name="admin_article_activate")
	 */
	public function activateAction($id)
	{
		$em = $this->getDoctrine()->getManager();

		$data = $em->getRepository('AppBundle\Entity\Article')->findOneBy(array('id' => intval($id)));
		if ($data) {
			if ($data->isActive()) {
				$data->setActive(0);
			} else {
				$data->setActive(1);
			}
			$em->persist($data);
			$em->flush();
		}

		return !empty($_SERVER['HTTP_REFERER']) ? $this->redirect($_SERVER['HTTP_REFERER']) : $this->generateUrl('admin_article', array(), UrlGeneratorInterface::ABSOLUTE_PATH);

	}


	private function createEntityForm($entity, $id =0, $em) {
		if (!empty($id)) {
			$form['action'] = $this->generateUrl('admin_article_edit', array('id' => $id), UrlGeneratorInterface::ABSOLUTE_PATH);
		} else{
			$form['action'] = $this->generateUrl('admin_article_add', array(), UrlGeneratorInterface::ABSOLUTE_PATH);
		}

		$form['id'] = $id;
		$form['form_id'] = 'form_'.md5('article');
		$form['separator'] = true;

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
					'checked_values' => $checked_values,
					'translate' => false
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
				)


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
					$entity->translate($localeName,false)->setKeywords(trim($data[$localeName]['keywords']));
				}
				if (!empty($data[$localeName]['slug'])) {
					$entity->translate($localeName,false)->setSlug(trim($data[$localeName]['slug']));
				}
				if (!empty($data[$localeName]['name']) && !empty($data[$localeName]['slug'])) {
					$entity->translate($localeName, false)->setActive(!empty($data[$localeName]['active']) ? 1 : 0);
				}

				/* BLOCK FOR NON TRANSLATED VALUES*/
				if ($localeName == $this->_defaultLocale) {
					$selectedDomains = array();
					foreach($data[$localeName]['type_domain'] as $domain => $value){
						if (!empty($value)) {
							$selectedDomains[] = $domain;
						}
					}
					$domains = $em->getRepository('AppBundle\Entity\BookDomain')->findBy( array( 'id'=> $selectedDomains) );
					$entity->setTypeDomain($domains);

					$image = $em->getRepository('AppBundle\Entity\Files')->findOneBy( array( 'id'=> $data[$localeName]['image']) );
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
