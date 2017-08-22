<?php
/**
 * Created by PhpStorm.
 * User: oalti
 * Date: 30/06/2017
 * Time: 12:51
 */

namespace AdminBundle\Controller;


use AppBundle\Entity\Settings;
use AppBundle\Service\Locales;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use RuntimeException;

class SettingsController extends Controller
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
	 * @Route("/settings", name="admin_settings")
	 */
	public function indexAction(Request $request)
	{

		$em = $this->getDoctrine()->getManager();

		// Get list of all entities
//		$data['data_list'] = $em->getRepository('AppBundle:Settings')->findAll();
		$data['data_list'] = $em->getRepository('AppBundle\Entity\Settings')->findAllForAdminList($em,'AppBundle\Entity\Settings',$request);


		//Get field to display
		$data['data_fields'] = ['id', 'name', 'image', 'value', 'description'];

		$data['data_title'] = 'adm.settings';

		//Buttons in top and bottom
		$data['data_buttons'] = [
			[
				'name' => 'add.setting',
				'link' => $this->generateUrl('admin_settings_add', [], UrlGeneratorInterface::ABSOLUTE_PATH),
				'class' => 'primary',
			],
		];

		//Buttons of action on each entity
		$data['data_actions'] = [
			[
				'name' => 'edit',
				'link' => 'admin_settings_edit',
				'class' => '',
				'confirm' => FALSE,
			],
		];

		//RENDER TEMPLATE
		return $this->render('AdminBundle:Default:list.html.twig', $data);
	}

	/**
	 * @Route("/settings/edit/{id}", defaults={"id" = 0}, name="admin_settings_edit")
	 * @Route("/settings/add", name="admin_settings_add")
	 */
	public function editAction($id = 0, Request $request)
	{

		$em = $this->getDoctrine()->getManager();

		//ALWAYS CREATE NEW ONE
		$entity = new Settings();
		$entity->setDefaultLocale($this->_defaultLocale);
		if (!empty($id)) {
			$entity = $em->getRepository('AppBundle:Settings')->findOneBy(['id' => $id]);
		}

		//MOVE ENTITY TO FRONT
		$data['entity'] = $entity;

		//TITLE FOR PAGE
		$data['data_title'] = (!empty($data['entity']->getName()) ? $data['entity']->getName() : $this->get('translator')
			->trans('adm.action.new'));

		//TYPE FOR PAGE
		$data['data_type'] = 'adm.menu.menu_name';

		//BUTTONS
		$data['data_buttons'] = [
			[
				'name' => 'save',
				'link' => 'admin_menu_edit',
				'class' => 'primary',
				'button' => TRUE,
				'button_type' => 'submit',
			],
			[
				'name' => 'cancel',
				'link' => !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->generateUrl('admin_menu', [], UrlGeneratorInterface::ABSOLUTE_PATH),
				'class' => 'default',
			],
		];

		//IF SAVE
		if ($request->isMethod('POST')) {
			$data = $request->request->all();

			$this->saveEntity($data, $entity, $em);

			return $this->redirectToRoute('admin_settings', []);
		}

		//FORM CREATION
		$data['form'] = $this->createEntityForm($entity, $id, $em);


		return $this->render('AdminBundle:Default:form.html.twig', $data);

	}

	//CREATE FORM FOR ENTITY
	private function createEntityForm($entity, $id = 0, $em)
	{

		//WHERE TO SAVE
		$form['action'] = $this->generateUrl('admin_settings_edit', ['id' => $id], UrlGeneratorInterface::ABSOLUTE_PATH);

		//ID OF ENTITY
		$form['id'] = $id;
		$form['form_id'] = 'form_' . md5('type');

		$form['separator'] = FALSE; // true/false;

		$fieldDisabled = empty($entity->getName()) ? FALSE : TRUE;

		foreach ($this->_locales as $lng) {

			$fields = [
				'name' => [
					'label' => 'adm.field.name',
					'type' => 'text',
					'name' => $lng . '[name]',
					'required' => ($lng != $this->_defaultLocale ? FALSE : TRUE),
					'value' => $entity ? $entity->getName() : '',
					'translate' => FALSE,
					'disabled' => $fieldDisabled,
					'placeholder' => 'adm.field.settings.name.disclaimer'
				],
				'value' => [
					'label' => 'adm.field.value',
					'type' => 'text',
					'name' => $lng . '[value]',
					'required' => ($lng != $this->_defaultLocale ? FALSE : TRUE),
					'value' => $entity ? $entity->translate($lng, ($lng == $this->_defaultLocale ? true : false))->getValue() : '',
					'translate' => TRUE,
				],
				'description' => [
					'label' => 'adm.field.description',
					'type' => 'textarea',
					'name' => $lng . '[description]',
					'required' => FALSE,
					'value' => $entity ? $entity->translate($lng, ($lng == $this->_defaultLocale ? true : false))->getDescription() : '',
					'translate' => TRUE,
				],
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
				'separator_image' => array(
					'label' => 'adm.field.image',
					'type' => 'separator',
					'translate' => false
				),
				'image' => [
					'label' => 'adm.field.image',
					'type' => 'image',
					'name' => $lng . '[image]',
					'path' => $entity->getImage() ? $entity->getImage()->getUrl() : FALSE,
					'value' => $entity->getImage() ? $entity->getImage()->getId() : FALSE,
					'required' => FALSE,
					'translate' => FALSE,
				],
			];

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
	private function saveEntity($data, $entity, $em)
	{

		$validatedName = true;
		//SAVE ENTITY ACCORDING TO LOCALE
		foreach ($data as $localeName => $locale) {
			if (in_array($localeName, $this->_locales)) {

				/* BLOCK FOR TRANSLABLE VALUES*/
				if (!empty($data[$localeName]['value'])) {
					$entity->translate($localeName,false)->setValue(trim($data[$localeName]['value']));
				}

				if (!empty($data[$localeName]['description'])) {
					$entity->translate($localeName,false)->setDescription(trim($data[$localeName]['description']));
				}

				if (!empty($data[$localeName]['body'])) {
					$entity->translate($localeName,false)->setBody(trim($data[$localeName]['body']));
				}

				if (!empty($data[$localeName]['slug'])) {
					$entity->translate($localeName,false)->setSlug(trim($data[$localeName]['slug']));
				}

				/* BLOCK FOR NON TRANSLATED VALUES*/
				if ($localeName == $this->_defaultLocale) {
					if (empty($entity->getId())) {

						$settingName = strtolower($data[$localeName]['name']);
						$settingName = preg_replace('/\s+/', ' ', $settingName);
						$settingName = str_replace(' ', '_', $settingName);
						$settingName = preg_replace("/[^a-z_]+/", "", $settingName);

						$noSpacesName = str_replace('_', '', $settingName);
						if (strlen($noSpacesName) < 2) {
							$validatedName = false;
						}

						$search_entity = $em->getRepository('AppBundle:Settings')->findOneBy(['name' => $settingName]);

						if (!empty($search_entity)) {
							$validatedName = false;
						}

						$entity->setName($settingName);
					}

					// IMAGE
					if (!empty($data[$localeName]['image'])) {
						$image = $em->getRepository('AppBundle\Entity\Files')->findOneBy(['id' => $data[$localeName]['image']]);
						$entity->setImage($image);
					} else {
						$entity->setImage(NULL);
					}

				}

				$entity->mergeNewTranslations();
			}
		}

		if ($validatedName) {
			$em->persist($entity);
			$em->flush();
		} else {
			throw new MethodNotAllowedHttpException(get_class_methods($entity),'Name duplicated');

		}

		return TRUE;
	}
}
