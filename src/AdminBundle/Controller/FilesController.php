<?php

namespace AdminBundle\Controller;

use AppBundle\Controller\FileController;
use AppBundle\Entity\Files;
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

class FilesController extends Controller
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
     * @Route("/files", name="admin_files")
     */
    public function indexAction(Request $request)
    {

		$em = $this->getDoctrine()->getManager();

		// Get list of all entities
//		$data['data_list'] = $em->getRepository('AppBundle\Entity\Files')->findBy(array(),array('id'=>'DESC'));
		$data['data_list'] = $em->getRepository('AppBundle\Entity\Files')->findAllForAdminList($em,'AppBundle\Entity\Files',$request);



		//Get field to display
		$data['data_fields'] = array('id','file_url','name','file_name','mime');

		//Tranlate in AdminBundle/Resources/translations/messages.en.yml
		$data['data_title'] = 'adm.files';


		//Buttons in top and bottom
		$data['data_buttons'] = array(
			array(
				'name' => 'add',
				'link' => $this->generateUrl('admin_files_add', array(), UrlGeneratorInterface::ABSOLUTE_PATH),
				'class' => 'primary'
			)
		);

		//Buttons of action on each entity
		$data['data_actions'] = array(
				array(
					'name' => 'edit',
					'link' => 'admin_files_edit',
					'class' => '',
					'confirm' => false
				),
				/*array(
					'name' => 'delete',
					'link' => 'admin_files_delete',
					'class' => 'danger',
					'confirm' => 'adm.action.delete.confirm'
				),*/
			);

		$page = !empty($request->query->get('page')) ? intval($request->query->get('page')) : 1;
		$paginator = array(
			'currentFilters' => $request->query->all(),
			'currentPage' => $page,
			'paginationPath' => 'admin_files',
			'showAlwaysFirstAndLast' => true,
			'lastPage' => ceil(count($data['data_list']) / $this->_itemsOnPage),
		);

		$data['data_list'] = array_slice($data['data_list'], ($page * $this->_itemsOnPage - $this->_itemsOnPage), $this->_itemsOnPage);
		$data = array_merge($data,$paginator);

		//RENDER TEMPLATE
        return $this->render('AdminBundle:Default:list.html.twig',$data);
    }

	/**
	 * @Route("/files/add", name="admin_files_add")
	 * @Route("/files/edit/{id}", defaults={"id" = 0}, name="admin_files_edit")
	 */
	public function editAction($id = 0, Request $request)
	{

		$em = $this->getDoctrine()->getManager();

		//ALWAYS CREATE NEW ONE
		$entity = new Files();
		$entity->setDefaultLocale($this->_defaultLocale);
		if (!empty($id)) {
			$entity = $em->getRepository('AppBundle\Entity\Files')->findOneBy(array('id'=>$id));
		}

		//MOVE ENTITY TO FRONT
		$data['entity'] = $entity;

		//TITLE FOR PAGE
		$data['data_title'] = (!empty($data['entity']->translate()->getName()) ? $data['entity']->translate()->getName() : $this->get('translator')->trans('adm.action.new'));

		//TYPE FOR PAGE
		$data['data_type'] =  'adm.file.file_name';

		//BUTTONS
		$data['data_buttons'] = array(
			array(
				'name' => 'save',
				'link' => 'admin_files_edit',
				'class' => 'primary',
				'button' => true,
				'button_type' => 'submit'
			),
			array(
				'name' => 'cancel',
				'link' => !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->generateUrl('admin_files', array(), UrlGeneratorInterface::ABSOLUTE_PATH),
				'class' => 'default',
			)
		);

		//IF SAVE
		if ($request->isMethod('POST')) {
			$data = $request->request->all();

			$this->saveEntity($data,$entity,$em,$request);

			return $this->redirectToRoute('admin_files', array());
		}

		//FORM CREATION
		$data['form'] = $this->createEntityForm($entity,$id,$em);


		return $this->render('AdminBundle:Default:form.html.twig',$data);

	}

	/**
	 * @Route("/files/delete/{id}", name="admin_files_delete")
	 */
	public function deleteAction($id)
	{
		$em = $this->getDoctrine()->getManager();

		$data = $em->getRepository('AppBundle\Entity\files')->findOneBy(array('id' => intval($id)));
		if ($data) {
			$em->remove($data);
			$em->flush();
		}

		return !empty($_SERVER['HTTP_REFERER']) ? $this->redirect($_SERVER['HTTP_REFERER']) : $this->generateUrl('admin_files', array(), UrlGeneratorInterface::ABSOLUTE_PATH);
	}

	//CREATE FORM FOR ENTITY
	private function createEntityForm($entity, $id =0, $em) {



		//WHERE TO SAVE
		if (!empty($id)) {
			$form['action'] = $this->generateUrl('admin_files_edit', array('id' => $id), UrlGeneratorInterface::ABSOLUTE_PATH);
		} else{
			$form['action'] = $this->generateUrl('admin_files_add', array(), UrlGeneratorInterface::ABSOLUTE_PATH);
		}

		//ID OF ENTITY
		$form['id'] = $id;
		$form['form_id'] = 'form_'.md5('files');
		$form['separator'] = true;


		foreach($this->_locales as $lng) {

			$fields = array(
				'name' => array(
					'label' => 'adm.field.name',
					'type' => 'text',
					'name' => $lng.'[name]',
					'required' => false,
                    'value' => $entity ? $entity->translate($lng, ($lng == $this->_defaultLocale ? true : false))->getName() : '',
					'translate' => true
				),
				'description' => array(
					'label' => 'adm.field.description',
					'type' => 'textarea',
					'name' => $lng.'[description]',
					'required' => false,
					'value' => $entity ? $entity->translate($lng,($lng == $this->_defaultLocale ? true : false))->getDescription() : '',
					'translate' => true
				),
				'file' => array(
					'label' => 'adm.field.file',
					'type' => 'file',
					'name' => 'file',
					'required' => false,
					'value' => $entity ? $entity->getId() : '',
					'url' => $entity ? $entity->getUrl() : '',
					'file_type' => $entity ? $entity->getType() : '',
					'translate' => false
				),
				'active' => array(
						'label' => 'adm.field.active',
						'type' => 'checkbox',
						'name' => $lng.'[active]',
						'value' => 1,
						'checked' => $entity ? $entity->isActive() : 0,
						'translate' => false
				),
				'file_name' => array(
					'label' => 'adm.field.file_name',
					'type' => 'text',
					'name' => $lng.'[file_name]',
					'required' => ($lng != $this->_defaultLocale ? false : true),
					'value' => $entity ? $entity->getFileName() : '',
					'translate' => false,
					'disabled' => 1
				),
				'mime' => array(
					'label' => 'adm.field.mime',
					'type' => 'text',
					'name' => $lng.'[mime]',
					'required' => ($lng != $this->_defaultLocale ? false : true),
					'value' => $entity ? $entity->getMime() : '',
					'translate' => false,
					'disabled' => 1
				),
				'type' => array(
					'label' => 'adm.field.file_type',
					'type' => 'text',
					'name' => $lng.'[type]',
					'required' => ($lng != $this->_defaultLocale ? false : true),
					'value' => $entity ? $entity->getType() : '',
					'translate' => false,
					'disabled' => 1
				),
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
	private function saveEntity($data,$entity,$em,$request){


		//SAVE ENTITY ACCORDING TO LOCALE
		foreach ($data as $localeName => $locale) {
			if (in_array($localeName,$this->_locales)) {
				$entity->setTranslatableLocale($localeName);

				/* BLOCK FOR TRANSLABLE VALUES*/
				if (!empty($data[$localeName]['name'])) {
                    $entity->translate($localeName,false)->setName(trim($data[$localeName]['name']));
				}

				if (!empty($data[$localeName]['description'])) {
                    $entity->translate($localeName,false)->setDescription(trim($data[$localeName]['description']));
				}

				/* BLOCK FOR NON TRANSLATED VALUES*/
				if ($localeName == $this->_defaultLocale) {
					$entity->setActive(!empty($data[$localeName]['active']) ? 1 : 0);
				}


                $entity->mergeNewTranslations();
			}
		}


		$file = $request->files->get('file');

		if (empty($entity->getId())) {
			$entity->setUrl('tmp');
		}

        $em->persist($entity);
        $em->flush();

		$id = $entity->getId();

		if ($file) {
			$controller = new FileController();
			$controller->indexAction($request, $id, $file, $this->getParameter('upload.dir'), false, $this->getUser(), $em);
		}

		return true;
	}

}
