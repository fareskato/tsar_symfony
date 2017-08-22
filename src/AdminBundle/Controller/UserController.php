<?php
/**
 * Created by PhpStorm.
 * User: fares
 * Date: 14.07.2017
 * Time: 17:02
 */

namespace AdminBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Service\Locales;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserController extends Controller
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
     * @Route("/user", name="admin_user")
     */
    public function indexAction(Request $request){



        $em = $this->getDoctrine()->getManager();
        // Get list of all users
//        $data['data_list'] = $em->getRepository('AppBundle:User')->findAll();
		$data['data_list'] = $em->getRepository('AppBundle\Entity\User')->findAllForAdminList($em,'AppBundle\Entity\User',$request);


		//Get field to display
        $data['data_fields'] = array('id','username','email');
        // Title
        $data['data_title'] = 'adm.user';
        //Buttons in top and bottom
        $data['data_buttons'] = [
            [
                'name' => 'add.user',
                'link' => $this->generateUrl('admin_user_add', [], UrlGeneratorInterface::ABSOLUTE_PATH),
                'class' => 'primary',
            ],
        ];
        //Buttons of action on each entity
        $data['data_actions'] = [
            [
                'name' => 'edit',
                'link' => 'admin_user_edit',
                'class' => '',
                'confirm' => FALSE,
            ],
            [
                'name' => 'activate',
                'link' => 'admin_user_activate',
                'class' => 'info',
                'confirm' => false
            ]
        ];

		$page = !empty($request->query->get('page')) ? intval($request->query->get('page')) : 1;
		$paginator = array(
			'currentFilters' => $request->query->all(),
			'currentPage' => $page,
			'paginationPath' => 'admin_user',
			'showAlwaysFirstAndLast' => true,
			'lastPage' => ceil(count($data['data_list']) / $this->_itemsOnPage),
		);

		$data['data_list'] = array_slice($data['data_list'], ($page * $this->_itemsOnPage - $this->_itemsOnPage), $this->_itemsOnPage);
		$data = array_merge($data,$paginator);

        //RENDER TEMPLATE
        return $this->render('AdminBundle:Default:list.html.twig', $data);

    }


    private function createEntityForm($entity, $id =0, $em) {
        if (!empty($id)) {
            $form['action'] = $this->generateUrl('admin_user_edit', array('id' => $id), UrlGeneratorInterface::ABSOLUTE_PATH);
        } else{
            $form['action'] = $this->generateUrl('admin_user_add', array(), UrlGeneratorInterface::ABSOLUTE_PATH);
        }
        $form['id'] = $id;
        $form['form_id'] = 'form_'.md5('article');
        $form['separator'] = true;

        // Roles handling
        $rolesArr = array_keys($this->getParameter('security.role_hierarchy.roles'));
        $roles = [];
        foreach ($rolesArr as $r){
            $roles[$r] = $r;
        }
        $checked_values = array();
        if (!empty($roles)) {
            foreach($entity->getRoles() as $role => $key) {
                $checked_values[] = $key;
            }
        }


            $lng = $this->_defaultLocale;
            $fields = array(
                'username' => array(
                    'label' => 'adm.field.name',
                    'type' => 'text',
                    'name' => $lng.'[username]',
                    'required' => ($lng != $this->_defaultLocale ? false : true),
                    'value' => $entity->getUsername() ? $entity->getUsername() : null,
                    'translate' => false
                ),
                'email' => array(
                    'label' => 'adm.field.email',
                    'type' => 'text',
                    'name' => $lng.'[email]',
                    'required' => ($lng != $this->_defaultLocale ? false : true),
                    'value' => $entity->getEmail()? $entity->getEmail() : null,
                    'translate' => false
                ),
                'password' => array(
                    'label' => 'adm.field.password',
                    'type' => 'text',
                    'name' => $lng.'[password]',
                    // edit => optional AND add => required
                    'required' => ($entity->getId() != null || $lng != $this->_defaultLocale) ? false : true,
                    // edit => default value or new value AND add => new value
                    'value' => (isset($data[$lng]['password']))? $entity->setPassword($data[$lng]['password']) : '',
                    'translate' => false
                ),
                'active' => array(
                    'label' => 'adm.field.active',
                    'type' => 'checkbox',
                    'name' => $lng.'[active]',
                    'value' => 1,
                    'checked' => $entity->isActive() ? 1 : 0,
                    'translate' => false
                ),
                'roles' => array(
                    'label' => 'adm.field.roles',
                    'type' => 'checkbox_multiple',
                    'name' => $lng.'[roles]',
                    'values' => $roles,
                    'checked_values' => $checked_values,
                    'translate' => false
                ),
            );

            $fieldset = 'translate';
            if ($lng == $this->_defaultLocale) {
                $fieldset = 'default';
            }
            $form[$fieldset][$lng] = $fields;

        return $form;
    }

    //SAVE ENTITY
    private function saveEntity($data,$entity,$em){

        //SAVE ENTITY ACCORDING TO LOCALE
        foreach ($data as $localeName => $locale) {
            if (in_array($localeName,$this->_locales)) {
//                $entity->setTranslatableLocale($localeName);
                if ($localeName == $this->_defaultLocale) {
                    // NON TRANSLATED STRING

                    // username
                    if(!empty($data[$localeName]['username'])){
                        $entity->setUsername(trim($data[$localeName]['username']));
                        $entity->setUsernameCanonical(trim($data[$localeName]['username']));
                    }

                    // email
                    if(!empty($data[$localeName]['email'])){
                        $entity->setEmail(trim($data[$localeName]['email']));
                        $entity->setEmailCanonical(trim($data[$localeName]['email']));
                    }

                    // activity
                    $entity->setActive(!empty($data[$localeName]['active']) ? 1 : 0);
                    $entity->setEnabled(!empty($data[$localeName]['active']) ? 1 : 0);

                    // Password
                    $factory = $this->get('security.encoder_factory');
                    $encoder = $factory->getEncoder($entity);
                    $entity->setSalt(md5(time()));
                    if(!empty($data[$localeName]['password'])){
                        $entity->setPassword($encoder->encodePassword($data[$localeName]['password'], $entity->getSalt()));
                    }
                    //Roles
                    $selectedRoles = array();
                    foreach($data[$localeName]['roles'] as $role => $value){
                        if (!empty($value)) {
                            $selectedRoles[] = $role;
                        }
                    }
                    $entity->setRoles($selectedRoles);
                }
                $em->persist($entity);
                $em->flush();
            }
        }
        return true;
    }



    /**
     * @Route("/user/edit/{id}", defaults={"id" = 0}, name="admin_user_edit")
     * @Route("/user/add", name="admin_user_add")
     */
    public function editAction($id = 0, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        //ALWAYS CREATE NEW ONE
        $entity = new User();
        if (!empty($id)) {
            $entity = $em->getRepository('AppBundle\Entity\User')->findOneBy(array('id'=>$id));
        }
//        dump($entity);die();
        //MOVE ENTITY TO FRONT
        $data['entity'] = $entity;

        //TITLE FOR PAGE
        $data['data_title'] = (!empty($data['entity']->getUsername()) ? $data['entity']->getUsername() : $this->get('translator')->trans('adm.action.new'));

        //TYPE FOR PAGE
        $data['data_type'] =  'adm.user';

        //BUTTONS
        $data['data_buttons'] = array(
            array(
                'name' => 'save',
                'link' => 'admin_user_edit',
                'class' => 'primary',
                'button' => true,
                'button_type' => 'submit'
            ),
            array(
                'name' => 'cancel',
                'link' => !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->generateUrl('admin_user', array(), UrlGeneratorInterface::ABSOLUTE_PATH),
                'class' => 'default',
            )
        );

        //IF SAVE


        if ($request->isMethod('POST')) {

            $data = $request->request->all();

            $this->saveEntity($data,$entity,$em);

            return $this->redirectToRoute('admin_user', array());
        }

        //FORM CREATION
        $data['form'] = $this->createEntityForm($entity,$id,$em);


        return $this->render('AdminBundle:Default:form.html.twig',$data);
    }

    /**
     * @Route("/user/activate/{id}", name="admin_user_activate")
     */
    public function activateAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository('AppBundle\Entity\User')->findOneBy(array('id' => intval($id)));
        if ($data) {
            if ($data->isActive()) {
                $data->setActive(0);
                $data->setEnabled(0);
            } else {
                $data->setActive(1);
                $data->setEnabled(1);
            }
            $em->persist($data);
            $em->flush();
        }

        return !empty($_SERVER['HTTP_REFERER']) ? $this->redirect($_SERVER['HTTP_REFERER']) : $this->generateUrl('admin_user', array(), UrlGeneratorInterface::ABSOLUTE_PATH);

    }


    /**
     * @Route("/user/delete/{id}", name="admin_user_delete")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository('AppBundle\Entity\User')->findOneBy(array('id' => intval($id)));
        if ($data) {
            $em->remove($data);
            $em->flush();
        }

        return !empty($_SERVER['HTTP_REFERER']) ? $this->redirect($_SERVER['HTTP_REFERER']) : $this->generateUrl('admin_user', array(), UrlGeneratorInterface::ABSOLUTE_PATH);
    }



}