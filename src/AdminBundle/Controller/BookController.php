<?php

namespace AdminBundle\Controller;

use AppBundle\Service\Locales;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BookController extends Controller
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
     * @Route("/book/{book_name}", name="admin_book")
     */
    public function indexAction($book_name = '', Request $request)
    {

        $em = $this->getDoctrine()->getManager();

		$entityName = $this->getEntityFromTableName($book_name,$em);
		if (empty($entityName) || !preg_match('/book/',$book_name)) {
			throw new NotFoundHttpException('Book with name '.$book_name.' not found!');
		}



//		$data['data_list'] = $em->getRepository($entityName)->findBy(array(), array('id' => 'DESC'));
		$data['data_list'] = $em->getRepository($entityName)->findAllForAdminList($em,$entityName,$request);


		$data['data_fields'] = array('id','name','description');
		$data['data_title'] = $book_name;
		$data['data_book_name'] = $book_name;
		
		$link = $this->generateUrl('admin_book_add', array('book_name' => $book_name), UrlGeneratorInterface::ABSOLUTE_PATH);



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
					'link' => 'admin_book_edit',
					'class' => '',
					'prefix' => array( 'book_name' => $book_name),
					'confirm' => false
				),
				array(
					'name' => 'delete',
					'link' => 'admin_book_delete',
					'class' => 'danger',
					'prefix' => array( 'book_name' => $book_name),
					'confirm' => 'adm.action.delete.confirm'
				),
			);

		$page = !empty($request->query->get('page')) ? intval($request->query->get('page')) : 1;
		$paginator = array(
			'currentFilters' => array_merge($request->query->all(),array('book_name' => $book_name)),
			'currentPage' => $page,
			'paginationPath' => 'admin_book',
			'showAlwaysFirstAndLast' => true,
			'lastPage' => ceil(count($data['data_list']) / $this->_itemsOnPage),
		);

		$data['data_list'] = array_slice($data['data_list'], ($page * $this->_itemsOnPage - $this->_itemsOnPage), $this->_itemsOnPage);
		$data = array_merge($data,$paginator);

        return $this->render('AdminBundle:Default:list.html.twig',$data);
    }

	/**
	 * @Route("/book/{book_name}/add", name="admin_book_add")
	 * @Route("/book/{book_name}/edit/{id}", defaults={"id" = 0}, name="admin_book_edit")
	 */
	public function editAction($book_name, $id = 0, Request $request)
	{

		$em = $this->getDoctrine()->getManager();

		$entityName = $this->getEntityFromTableName($book_name,$em);
		if (empty($entityName) || !preg_match('/book/',$book_name)) {
			throw new NotFoundHttpException('Book with name '.$book_name.' not found!');
		}

		$entity = new $entityName();
		$entity->setDefaultLocale($this->_defaultLocale);

		if ( !empty($id) ) {
			$entityRecieved = $em->getRepository($entityName)->findOneBy(array('id'=>$id));
			if (!empty($entityRecieved )) {
				$entity = $entityRecieved;
			}
		}
		$data['entity'] = $entity;
		$data['data_book_name'] = $book_name;
		$data['data_title'] = $book_name . ' ' .(!empty($data['entity']->getName()) ? $data['entity']->getName() : $this->get('translator')->trans('adm.action.new'));

		$data['data_type'] =  'adm.book.book_name';

		$data['data_buttons'] = array(
			array(
				'name' => 'save',
				'link' => 'admin_book_edit',
				'class' => 'primary',
				'button' => true,
				'button_type' => 'submit'
			),
			array(
				'name' => 'cancel',
				'link' => !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->generateUrl('admin_book', array('book_name' => $book_name), UrlGeneratorInterface::ABSOLUTE_PATH),
				'class' => 'default',
			)
		);

		if ($request->isMethod('POST')) {

			$data = $request->request->all();

			$this->saveEntity($data,$entity,$em);
			return $this->redirectToRoute('admin_book', array('book_name' => $book_name));
		}

		$data['form'] = $this->createEntityForm($entity,$id,$book_name,$em);


		return $this->render('AdminBundle:Default:form.html.twig',$data);
	}

    /**
     * @Route("/book/{book_name}/delete/{id}", name="admin_book_delete")
     */
    public function deleteAction($book_name, $id)
    {
		$em = $this->getDoctrine()->getManager();

		$entityName = $this->getEntityFromTableName($book_name,$em);
		if (empty($entityName) || !preg_match('/book/',$book_name) || empty($id)) {
			throw new NotFoundHttpException('Book with name '.$book_name.' not found!');
		}

		$data = $em->getRepository($entityName)->findOneBy(array('id' => intval($id)));
		if ($data) {
			$em->remove($data);
			$em->flush();
		}


		return !empty($_SERVER['HTTP_REFERER']) ? $this->redirect($_SERVER['HTTP_REFERER']) : $this->generateUrl('admin_book', array('book_name' => $book_name), UrlGeneratorInterface::ABSOLUTE_PATH);

    }

    private function getEntityFromTableName($table = '',$em){
		$classNames = $em->getConfiguration()->getMetadataDriverImpl()->getAllClassNames();
		foreach ($classNames as $className) {
			$classMetaData = $em->getClassMetadata($className);
			if ($table == $classMetaData->getTableName()) {
				return $classMetaData->getName();
			}
		}
		return null;
	}

	private function createEntityForm($entity, $id = 0, $book_name, $em) {
		if (!empty($id)) {
			$form['action'] = $this->generateUrl('admin_book_edit', array('book_name' => $book_name, 'id' => $id), UrlGeneratorInterface::ABSOLUTE_PATH);
		} else{
			$form['action'] = $this->generateUrl('admin_book_add', array('book_name' => $book_name), UrlGeneratorInterface::ABSOLUTE_PATH);
		}

		$form['id'] = $id;
		$form['form_id'] = 'form_'.md5($book_name);

		foreach($this->_locales as $lng) {
			$fields = array(
				'name' => array(
					'label' => 'adm.field.name',
					'type' => 'text',
					'name' => $lng.'[name]',
					'required' => ($lng != $this->_defaultLocale ? false : true),
					'value' => $entity ? $entity->translate($lng,($lng == $this->_defaultLocale ? true : false))->getName() : '',
					'translate' => true
				),
				'description' => array(
					'label' => 'adm.field.description',
					'type' => 'textarea',
					'name' => $lng.'[description]',
					'required' => false,
					'value' => $entity ? $entity->translate($lng,($lng == $this->_defaultLocale ? true : false))->getDescription() : '',
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
				if (!empty($data[$localeName]['description'])) {
					$entity->translate($localeName,false)->setDescription(trim($data[$localeName]['description']));
				}

				/* BLOCK FOR NON TRANSLATED VALUES*/
				if ($localeName == $this->_defaultLocale) {
				}
				$entity->mergeNewTranslations();
			}
		}

		$em->persist($entity);
		$em->flush();

		return true;
	}
}
