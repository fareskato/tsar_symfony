<?php

namespace AdminBundle\Controller;


use AppBundle\Entity\ShopSettings;
use AppBundle\Service\Locales;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use RuntimeException;

class ShopSettingsController extends Controller
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
     * @Route("/shopsettings", name="admin_shop_settings")
     */
    public function indexAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        // Get list of all entities
//        $data['data_list'] = $em->getRepository('AppBundle:ShopSettings')->findAll();
		$data['data_list'] = $em->getRepository('AppBundle\Entity\ShopSettings')->findAllForAdminList($em,'AppBundle\Entity\ShopSettings',$request);


        //Get field to display
        $data['data_fields'] = ['id', 'name', 'value'];

        $data['data_title'] = 'adm.shopsettings';

        //Buttons in top and bottom
        $data['data_buttons'] = [
            [
                'name' => 'add.shop.setting',
                'link' => $this->generateUrl('admin_shop_settings_add', [], UrlGeneratorInterface::ABSOLUTE_PATH),
                'class' => 'primary',
            ],
        ];

        //Buttons of action on each entity
        $data['data_actions'] = [
            [
                'name' => 'edit',
                'link' => 'admin_shop_settings_edit',
                'class' => '',
                'confirm' => FALSE,
            ],
        ];

        //RENDER TEMPLATE
        return $this->render('AdminBundle:Default:list.html.twig', $data);
    }

    //CREATE FORM FOR ENTITY
    private function createEntityForm($entity, $id = 0, $em)
    {

        //WHERE TO SAVE
        if (!empty($id)) {
            $form['action'] = $this->generateUrl('admin_shop_settings_edit', array('id' => $id), UrlGeneratorInterface::ABSOLUTE_PATH);
        } else{
            $form['action'] = $this->generateUrl('admin_shop_settings_add', array(), UrlGeneratorInterface::ABSOLUTE_PATH);
        }
        //ID OF ENTITY
        $form['id'] = $id;
        $form['form_id'] = 'form_' . md5('type');

        $form['separator'] = FALSE; // true/false;

        $fieldDisabled = empty($entity->getName()) ? FALSE : TRUE;
        $lng = $this->_defaultLocale;
//        foreach ($this->_locales as $lng) {

            $fields = [
                'name' => [
                    'label' => 'adm.field.name',
                    'type' => 'text',
                    'name' => $lng . '[name]',
                    'required' => ($lng != $this->_defaultLocale ? false : true),
                    'value' => $entity ? $entity->getName() : '',
                    'translate' => false,
                    'disabled' => $fieldDisabled,
                ],
                'value' => [
                    'label' => 'adm.field.value',
                    'type' => 'text',
                    'name' => $lng . '[value]',
                    'required' => ($lng != $this->_defaultLocale ? FALSE : TRUE),
                    'value' => $entity ? $entity->getValue() : '',
                    'translate' => false,
                ],
            ];

            //TECHNICAL INFORMATION
            $fieldset = 'translate';
            if ($lng == $this->_defaultLocale) {
                $fieldset = 'default';
            }
            $form[$fieldset][$lng] = $fields;
//        }
        return $form;
    }


    //SAVE ENTITY
    private function saveEntity($data, $entity, $em)
    {

        $validatedName = true;
        //SAVE ENTITY ACCORDING TO LOCALE
        foreach ($data as $localeName => $locale) {
            if (in_array($localeName, $this->_locales)) {
                /* BLOCK FOR NON TRANSLATED VALUES*/
                if ($localeName == $this->_defaultLocale) {

                    if (!empty($data[$localeName]['value'])) {
                        $entity->setValue(trim($data[$localeName]['value']));
                    }
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

                            $search_entity = $em->getRepository('AppBundle\Entity\ShopSettings')->findOneBy(['name' => $settingName]);

                            if (!empty($search_entity)) {
                                $validatedName = false;
                            }

                            $entity->setName($settingName);
                        }

                    }

                }

                $em->persist($entity);
                $em->flush();
            }
        }

        if ($validatedName) {

        } else {
            throw new MethodNotAllowedHttpException(get_class_methods($entity),'Name duplicated');

        }


        return true;
    }

    /**
     * @Route("/shopsettings/edit/{id}", defaults={"id" = 0}, name="admin_shop_settings_edit")
     * @Route("/shopsettings/add", name="admin_shop_settings_add")
     */
    public function editAction($id = 0, Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        //ALWAYS CREATE NEW ONE
        $entity = new ShopSettings();

        if (!empty($id)) {
            $entity = $em->getRepository('AppBundle\Entity\ShopSettings')->findOneBy(['id' => $id]);
        }

        //MOVE ENTITY TO FRONT
        $data['entity'] = $entity;

        //TITLE FOR PAGE
        $data['data_title'] = (!empty($data['entity']->getName()) ? $data['entity']->getName() : $this->get('translator')->trans('adm.action.new'));

        //TYPE FOR PAGE
        $data['data_type'] = 'adm.shop.settings';

        //BUTTONS
        $data['data_buttons'] = [
            [

                'name' => 'save',
                'link' => 'admin_shop_settings_edit',
                'class' => 'primary',
                'button' => true,
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



            return $this->redirectToRoute('admin_shop_settings', []);
        }

        //FORM CREATION
        $data['form'] = $this->createEntityForm($entity, $id, $em);


        return $this->render('AdminBundle:Default:form.html.twig', $data);

    }


}
