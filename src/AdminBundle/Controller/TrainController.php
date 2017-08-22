<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\Train;
use AppBundle\Entity\Visa;
use AppBundle\Service\Locales;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TrainController extends Controller
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
     * @Route("/train", name="admin_train")
     */
    public function indexAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

//		$data['data_list'] = $em->getRepository('AppBundle\Entity\Train')->findBy(array(), array('id' => 'DESC'));
		$data['data_list'] = $em->getRepository('AppBundle\Entity\Train')->findAllForAdminList($em,'AppBundle\Entity\Train',$request);

		$data['data_fields'] = array('id','label');
		$data['data_title'] = 'adm.train';

		$link = $this->generateUrl('admin_train_add', array(), UrlGeneratorInterface::ABSOLUTE_PATH);


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
					'link' => 'admin_train_edit',
					'class' => '',
					'confirm' => false
				),
				array(
					'name' => 'delete',
					'link' => 'admin_train_delete',
					'class' => 'danger',
					'confirm' => 'adm.action.delete.confirm'
				),
			);

		$page = !empty($request->query->get('page')) ? intval($request->query->get('page')) : 1;
		$paginator = array(
			'currentFilters' => $request->query->all(),
			'currentPage' => $page,
			'paginationPath' => 'admin_train',
			'showAlwaysFirstAndLast' => true,
			'lastPage' => ceil(count($data['data_list']) / $this->_itemsOnPage),
		);

		$data['data_list'] = array_slice($data['data_list'], ($page * $this->_itemsOnPage - $this->_itemsOnPage), $this->_itemsOnPage);
		$data = array_merge($data,$paginator);

        return $this->render('AdminBundle:Default:list.html.twig',$data);
    }

    /**
     * @Route("/train/add", name="admin_train_add")
     * @Route("/train/edit/{id}", defaults={"id" = 0}, name="admin_train_edit")
     */
    public function editAction($id = 0, Request $request){
        $em = $this->getDoctrine()->getManager();



        $entity = new Train();
		$entity->setDefaultLocale($this->_defaultLocale);
		if (!empty($id)) {
			$entity = $em->getRepository('AppBundle\Entity\Train')->findOneBy(array('id'=>$id));
		}

        $data['entity'] = $entity;

		//TITLE FOR PAGE
		$data['data_title'] = (!empty($data['entity']->getLabel()) ? $data['entity']->getLabel() : $this->get('translator')->trans('adm.action.new'));

        $data['data_type'] =  'adm.train.train_name';

        $data['data_buttons'] = array(
            array(
                'name' => 'save',
                'link' => 'admin_train_edit',
                'class' => 'primary',
                'button' => true,
                'button_type' => 'submit'
            ),
            array(
                'name' => 'cancel',
                'link' => !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->generateUrl('admin_train', array(), UrlGeneratorInterface::ABSOLUTE_PATH),
                'class' => 'default',
            )
        );

        if ($request->isMethod('POST')) {

            $data = $request->request->all();

            $this->saveEntity($data,$entity,$em);
            return $this->redirectToRoute('admin_train', array());
        }

        $data['form'] = $this->createEntityForm($entity,$id,$em);


        return $this->render('AdminBundle:Default:form.html.twig',$data);
    }

    /**
     * @Route("/train/delete/{id}", name="admin_train_delete")
     */
    public function deleteAction($id){
        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository('AppBundle\Entity\Train')->findOneBy(array('id' => intval($id)));
        if ($data) {
            $em->remove($data);
            $em->flush();
        }


        return !empty($_SERVER['HTTP_REFERER']) ? $this->redirect($_SERVER['HTTP_REFERER']) : $this->generateUrl('admin_train', UrlGeneratorInterface::ABSOLUTE_PATH);

    }

	private function createEntityForm($entity, $id = 0, $em) {
		if (!empty($id)) {
			$form['action'] = $this->generateUrl('admin_train_edit', array('id' => $id), UrlGeneratorInterface::ABSOLUTE_PATH);
		} else{
			$form['action'] = $this->generateUrl('admin_train_add', array(), UrlGeneratorInterface::ABSOLUTE_PATH);
		}

		$form['id'] = $id;
		$form['form_id'] = 'form_'.md5('train');
		$form['separator'] = true;

        $BookTrainPeriod = $em->getRepository('AppBundle\Entity\BookTrainPeriod')->findAll();
        $BookTrainPeriodValues = array();
        foreach ($BookTrainPeriod as $value ) {
            $BookTrainPeriodValues[$value->getId()] = $value->translate()->getName();
        }
        $BookTrainCategorie = $em->getRepository('AppBundle\Entity\BookTrainCategorie')->findAll();
        $BookTrainCategorieValues = array();
        foreach ($BookTrainCategorie as $value ) {
            $BookTrainCategorieValues[$value->getId()] = $value->translate()->getName();
        }
        $BookTrainTypeCustomer = $em->getRepository('AppBundle\Entity\BookTrainTypeCustomer')->findAll();
        $BookTrainTypeCustomerValues = array();
        foreach ($BookTrainTypeCustomer as $value ) {
            $BookTrainTypeCustomerValues[$value->getId()] = $value->translate()->getName();
        }
		foreach($this->_locales as $lng) {
			$fields = array(
				'label' => array(
					'label' => 'adm.field.label',
					'type' => 'text',
					'name' => $lng.'[label]',
					'required' => false,
					'value' => $entity ? $entity->getLabel() : '',
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

                'departure_city' => array(
                    'label' => 'adm.field.departure_city',
                    'type' => 'relation_one',
                    'autocomplete' => 'location',
                    'autocomplete_path' => 'admin_autocomplete_location',
                    'name' => $lng.'[departure_city]',
                    'add' => 'admin_location_add',
                    'field_rel' => array('id','name','street','city','getCountryName'),
                    'values' => $entity->getDepartureCity() ? $entity->getDepartureCity() : null,
                    'translate' => false,
                    'editLink' => array(
                        'type' => 'location',
                        'path' => $entity->getDepartureCity() ? array('id' => $entity->getDepartureCity()->getId()) : array(),
                    ),
                ),

                'city_arrival' => array(
                    'label' => 'adm.field.city_arrival',
                    'type' => 'relation_one',
                    'autocomplete' => 'location',
                    'autocomplete_path' => 'admin_autocomplete_location',
                    'name' => $lng.'[city_arrival]',
                    'add' => 'admin_location_add',
                    'field_rel' => array('id','name','street','city','getCountryName'),
                    'values' => $entity->getCityArrival() ? $entity->getCityArrival() : null,
                    'translate' => false,
                    'editLink' => array(
                        'type' => 'location',
                        'path' => $entity->getCityArrival() ? array('id' => $entity->getCityArrival()->getId()) : array(),
                    ),
                ),

                'period' => array(
                    'label' => 'adm.field.period',
                    'type' => 'select',
                    'name' => $lng.'[period]',
                    'required' => false,
                    'value' => $entity->getPeriod() ? $entity->getPeriod()->getId() : null,
                    'value_default' => 'adm.field.select.toplevel',
                    'values' => $BookTrainPeriodValues,
                    'translate' => false
                ),
                'categorie' => array(
                    'label' => 'adm.field.categorie',
                    'type' => 'select',
                    'name' => $lng.'[categorie]',
                    'required' => false,
                    'value' => $entity->getCategorie() ? $entity->getCategorie()->getId() : null,
                    'value_default' => 'adm.field.select.toplevel',
                    'values' => $BookTrainCategorieValues,
                    'translate' => false
                ),
                'type_customer' => array(
                    'label' => 'adm.field.type_customer',
                    'type' => 'select',
                    'name' => $lng.'[type_customer]',
                    'required' => false,
                    'value' => $entity->getTypeCustomer() ? $entity->getTypeCustomer()->getId() : null,
                    'value_default' => 'adm.field.select.toplevel',
                    'values' => $BookTrainTypeCustomerValues,
                    'translate' => false
                ),

                'commentaires' => array(
                    'label' => 'adm.field.commentaires',
                    'type' => 'textarea',
                    'name' => $lng.'[commentaires]',
                    'required' => false,
                    'value' => $entity ? $entity->translate($lng,($lng == $this->_defaultLocale ? true : false))->getCommentaires() : '',
                    'translate' => true
                ),
                'separator_information_supplémentaire' => array(
                    'label' => 'adm.field.information_supplémentaire',
                    'type' => 'separator',
                    'translate' => false
                ),

                'phone' => array(
                    'label' => 'adm.field.phone',
                    'type' => 'text',
                    'name' => $lng.'[phone]',
                    'required' => false,
                    'value' => $entity ? $entity->getPhone() : '',
                    'translate' => false
                ),
                'informations_supplementaires' => array(
                    'label' => 'adm.field.informations_supplementaires',
                    'type' => 'textarea',
                    'name' => $lng.'[informations_supplementaires]',
                    'required' => false,
                    'value' => $entity ? $entity->translate($lng,($lng == $this->_defaultLocale ? true : false))->getInformationsSupplementaires() : '',
                    'translate' => true
                ),
                'separator_additional_information' => array(
                    'label' => 'adm.field.additional_information',
                    'type' => 'separator',
                    'translate' => false
                ),
                'information_utile' => array(
                    'label' => 'adm.field.information_utile',
                    'type' => 'textarea',
                    'name' => $lng.'[information_utile]',
                    'required' => false,
                    'value' => $entity ? $entity->translate($lng,($lng == $this->_defaultLocale ? true : false))->getInformationUtile() : '',
                    'translate' => true
                ),
                'type_group' => array(
                    'label' => 'adm.field.type_group',
                    'type' => 'checkbox',
                    'name' => $lng.'[type_group]',
                    'value' => 1,
                    'checked' => $entity->isTypeGroup() ? true : false,
                    'translate' => false
                ),

                'separator_excel' => array(
                    'label' => 'adm.field.excel',
                    'type' => 'separator',
                    'translate' => false
                ),
                'custom_id' => array(
                    'label' => 'adm.field.custom_id',
                    'type' => 'text',
                    'name' => $lng.'[custom_id]',
                    'required' => false,
                    'value' => $entity ? $entity->getCustomId() : '',
                    'translate' => false
                ),
                'excel_title' => array(
                    'label' => 'adm.field.excel_title',
                    'type' => 'text',
                    'name' => $lng.'[excel_title]',
                    'required' => false,
                    'value' => $entity ? $entity->getExcelTitle() : '',
                    'translate' => false
                ),
                'excel_description' => array(
                    'label' => 'adm.field.excel_description',
                    'type' => 'text',
                    'name' => $lng.'[excel_description]',
                    'required' => false,
                    'value' => $entity ? $entity->getExcelDescription() : '',
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

                if (!empty($data[$localeName]['informations_supplementaires'])) {
                    $entity->translate($localeName,false)->setInformationsSupplementaires(trim($data[$localeName]['informations_supplementaires']));
                }
                if (!empty($data[$localeName]['information_utile'])) {
                    $entity->translate($localeName,false)->setInformationUtile(trim($data[$localeName]['information_utile']));
                }
                $entity->translate($localeName, false)->setActive(!empty($data[$localeName]['active']) ? 1 : 0);
                if (!empty($data[$localeName]['commentaires'])) {
                    $entity->translate($localeName,false)->setCommentaires(trim($data[$localeName]['commentaires']));
                }

				/* BLOCK FOR NON TRANSLATED VALUES*/
				if ($localeName == $this->_defaultLocale) {
                    if (!empty($data[$localeName]['departure_city'])) {
                        $location = $em->getRepository('AppBundle\Entity\Location')->findOneBy( array( 'id'=> $data[$localeName]['departure_city']) );
                        $entity->setDepartureCity($location);
                    } else {
                        $entity->setDepartureCity(null);
                    }
                    if (!empty($data[$localeName]['city_arrival'])) {
                        $location = $em->getRepository('AppBundle\Entity\Location')->findOneBy( array( 'id'=> $data[$localeName]['city_arrival']) );
                        $entity->setCityArrival($location);
                    } else {
                        $entity->setCityArrival(null);
                    }
                    if (!empty($data[$localeName]['period'])) {
                        $period = $em->getRepository('AppBundle\Entity\BookTrainPeriod')->findOneBy(array('id' => $data[$localeName]['period']));
                        if ($period) {
                            $entity->setPeriod($period);
                        } else {
                            $entity->setPeriod(null);
                        }
                    }else{
                        $entity->setPeriod(null);
                    }
                    if (!empty($data[$localeName]['categorie'])) {
                        $categorie = $em->getRepository('AppBundle\Entity\BookTrainCategorie')->findOneBy(array('id' => $data[$localeName]['categorie']));
                        if ($categorie) {
                            $entity->setCategorie($categorie);
                        } else {
                            $entity->setCategorie(null);
                        }
                    }else{
                        $entity->setCategorie(null);
                    }
                    if (!empty($data[$localeName]['type_customer'])) {
                        $type_customer = $em->getRepository('AppBundle\Entity\BookTrainTypeCustomer')->findOneBy(array('id' => $data[$localeName]['type_customer']));
                        if ($type_customer) {
                            $entity->setTypeCustomer($type_customer);
                        } else {
                            $entity->setTypeCustomer(null);
                        }
                    }else{
                        $entity->setTypeCustomer(null);
                    }

                    $entity->setTypeGroup(!empty($data[$localeName]['type_group']) ? 1 : 0);
                    if (!empty($data[$localeName]['phone'])) {
                        $entity->setPhone(trim($data[$localeName]['phone']));
                    }

                    if (!empty($data[$localeName]['custom_id'])) {
                        $entity->setCustomId(intval($data[$localeName]['custom_id']));
                    }
                    if (!empty($data[$localeName]['excel_title'])) {
                        $entity->setExcelTitle(trim($data[$localeName]['excel_title']));
                    }
                    if (!empty($data[$localeName]['excel_description'])) {
                        $entity->setExcelDescription(trim($data[$localeName]['excel_description']));
                    }

                    //  Формируем метку из предоставленных данных
                    $label = 'Trai_';
                    if($entity->getDepartureCity()){
                        $label=$label.substr($entity->getDepartureCity()->translate($localeName, true)->getCity(), 0, 3).'_';
                    }
                    if($entity->getCityArrival()){
                        $label=$label.substr($entity->getCityArrival()->translate($localeName, true)->getCity(), 0, 3).'_';
                    }
                    if($period){
                        $label=$label.substr($period->translate($localeName, true)->getName(), 0, 3).'_';
                    }
                    if($categorie){
                        $label=$label.substr($categorie->translate($localeName, true)->getName(), 0, 3).'_';
                    }
                    if($type_customer){
                        $label=$label.substr($type_customer->translate($localeName, true)->getName(), 0, 3);
                    }
                    $entity->setLabel($label);


				}
                $entity->mergeNewTranslations();
            }
            $em->persist($entity);
            $em->flush();
        }

        return true;
    }
}
