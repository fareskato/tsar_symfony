<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\Transferts;
use AppBundle\Entity\Visa;
use AppBundle\Service\Locales;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TransfertsController extends Controller
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
     * @Route("/transferts", name="admin_transferts")
     */
    public function indexAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

//		$data['data_list'] = $em->getRepository('AppBundle\Entity\Transferts')->findBy(array(), array('id' => 'DESC'));
		$data['data_list'] = $em->getRepository('AppBundle\Entity\Transferts')->findAllForAdminList($em,'AppBundle\Entity\Transferts',$request);

		$data['data_fields'] = array('id','label');
		$data['data_title'] = 'adm.transferts';

		$link = $this->generateUrl('admin_transferts_add', array(), UrlGeneratorInterface::ABSOLUTE_PATH);


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
					'link' => 'admin_transferts_edit',
					'class' => '',
					'confirm' => false
				),
				array(
					'name' => 'delete',
					'link' => 'admin_transferts_delete',
					'class' => 'danger',
					'confirm' => 'adm.action.delete.confirm'
				),
			);

		$page = !empty($request->query->get('page')) ? intval($request->query->get('page')) : 1;
		$paginator = array(
			'currentFilters' => $request->query->all(),
			'currentPage' => $page,
			'paginationPath' => 'admin_transferts',
			'showAlwaysFirstAndLast' => true,
			'lastPage' => ceil(count($data['data_list']) / $this->_itemsOnPage),
		);

		$data['data_list'] = array_slice($data['data_list'], ($page * $this->_itemsOnPage - $this->_itemsOnPage), $this->_itemsOnPage);
		$data = array_merge($data,$paginator);

        return $this->render('AdminBundle:Default:list.html.twig',$data);
    }

    /**
     * @Route("/transferts/add", name="admin_transferts_add")
     * @Route("/transferts/edit/{id}", defaults={"id" = 0}, name="admin_transferts_edit")
     */
    public function editAction($id = 0, Request $request){
        $em = $this->getDoctrine()->getManager();



        $entity = new Transferts();
		$entity->setDefaultLocale($this->_defaultLocale);
		if (!empty($id)) {
			$entity = $em->getRepository('AppBundle\Entity\Transferts')->findOneBy(array('id'=>$id));
		}

        $data['entity'] = $entity;

		//TITLE FOR PAGE
		$data['data_title'] = (!empty($data['entity']->getLabel()) ? $data['entity']->getLabel() : $this->get('translator')->trans('adm.action.new'));

        $data['data_type'] =  'adm.transferts.transferts_name';

        $data['data_buttons'] = array(
            array(
                'name' => 'save',
                'link' => 'admin_transferts_edit',
                'class' => 'primary',
                'button' => true,
                'button_type' => 'submit'
            ),
            array(
                'name' => 'cancel',
                'link' => !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->generateUrl('admin_transferts', array(), UrlGeneratorInterface::ABSOLUTE_PATH),
                'class' => 'default',
            )
        );

        if ($request->isMethod('POST')) {

            $data = $request->request->all();

            $this->saveEntity($data,$entity,$em);
            return $this->redirectToRoute('admin_transferts', array());
        }

        $data['form'] = $this->createEntityForm($entity,$id,$em);


        return $this->render('AdminBundle:Default:form.html.twig',$data);
    }

    /**
     * @Route("/transferts/delete/{id}", name="admin_transferts_delete")
     */
    public function deleteAction($id){
        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository('AppBundle\Entity\Transferts')->findOneBy(array('id' => intval($id)));
        if ($data) {
            $em->remove($data);
            $em->flush();
        }


        return !empty($_SERVER['HTTP_REFERER']) ? $this->redirect($_SERVER['HTTP_REFERER']) : $this->generateUrl('admin_transferts', UrlGeneratorInterface::ABSOLUTE_PATH);

    }

	private function createEntityForm($entity, $id = 0, $em) {
		if (!empty($id)) {
			$form['action'] = $this->generateUrl('admin_transferts_edit', array('id' => $id), UrlGeneratorInterface::ABSOLUTE_PATH);
		} else{
			$form['action'] = $this->generateUrl('admin_transferts_add', array(), UrlGeneratorInterface::ABSOLUTE_PATH);
		}

		$form['id'] = $id;
		$form['form_id'] = 'form_'.md5('transferts');
		$form['separator'] = true;


        $BookTypeTransfert = $em->getRepository('AppBundle\Entity\BookTypeTransfert')->findAll();
        $BookTypeTransfertValues = array();
        foreach ($BookTypeTransfert as $value ) {
            $BookTypeTransfertValues[$value->getId()] = $value->translate()->getName();
        }
        $BookVehicle = $em->getRepository('AppBundle\Entity\BookVehicle')->findAll();
        $BookVehicleValues = array();
        foreach ($BookVehicle as $value ) {
            $BookVehicleValues[$value->getId()] = $value->translate()->getName();
        }

		foreach($this->_locales as $lng) {
			$fields = array(
				'label' => array(/* Будем считать, что это исходное поле Title*/
					'label' => 'adm.field.label',
					'type' => 'text',
					'name' => $lng.'[label]',
					'required' => false,
					'value' => $entity ? $entity->getLabel() : '',
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

                'ville' => array(
                    'label' => 'adm.field.ville',
                    'type' => 'relation_one',
                    'autocomplete' => 'location',
                    'autocomplete_path' => 'admin_autocomplete_location',
                    'name' => $lng.'[ville]',
                    'add' => 'admin_location_add',
                    'field_rel' => array('id','name','street','city','getCountryName'),
                    'values' => $entity->getVille() ? $entity->getVille() : null,
                    'translate' => false,
                    'editLink' => array(
                        'type' => 'location',
                        'path' => $entity->getVille() ? array('id' => $entity->getVille()->getId()) : array(),
                    ),
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
                'number_passengers' => array(
                    'label' => 'adm.field.number_passengers',
                    'type' => 'text',
                    'name' => $lng.'[number_passengers]',
                    'required' => false,
                    'value' => $entity ? $entity->getNumberPassengers() : '',
                    'translate' => true
                ),
                'type_transfert' => array(
                    'label' => 'adm.field.type_transfert',
                    'type' => 'select',
                    'name' => $lng.'[type_transfert]',
                    'required' => false,
                    'value' => $entity->getTypeTransfert() ? $entity->getTypeTransfert()->getId() : null,
                    'value_default' => 'adm.field.select.toplevel',
                    'values' => $BookTypeTransfertValues,
                    'translate' => false
                ),

                'vehicle' => array(
                    'label' => 'adm.field.vehicle',
                    'type' => 'select',
                    'name' => $lng.'[vehicle]',
                    'required' => false,
                    'value' => $entity->getVehicle() ? $entity->getVehicle()->getId() : null,
                    'value_default' => 'adm.field.select.toplevel',
                    'values' => $BookVehicleValues,
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

                'separator_additional_information' => array(
                    'label' => 'adm.field.additional_information',
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

                'separator_information_supplémentaire' => array(
                    'label' => 'adm.field.information_supplémentaire',
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
                if (!empty($data[$localeName]['commentaires'])) {
                    $entity->translate($localeName,false)->setCommentaires(trim($data[$localeName]['commentaires']));
                }
                $entity->translate($localeName, false)->setActive(!empty($data[$localeName]['active']) ? 1 : 0);
                if (!empty($data[$localeName]['informations_supplementaires'])) {
                    $entity->translate($localeName,false)->setInformationsSupplementaires(trim($data[$localeName]['informations_supplementaires']));
                }
                if (!empty($data[$localeName]['information_utile'])) {
                    $entity->translate($localeName,false)->setInformationUtile(trim($data[$localeName]['information_utile']));
                }
				/* BLOCK FOR NON TRANSLATED VALUES*/
				if ($localeName == $this->_defaultLocale) {
                    if (!empty($data[$localeName]['number_passengers'])) {
                        $entity->setNumberPassengers(intval($data[$localeName]['number_passengers']));
                    }
                    if (!empty($data[$localeName]['type_transfert'])) {
                        $type_transfert = $em->getRepository('AppBundle\Entity\BookTypeTransfert')->findOneBy(array('id' => $data[$localeName]['type_transfert']));
                        if ($type_transfert) {
                            $entity->setTypeTransfert($type_transfert);
                        } else {
                            $entity->setTypeTransferta(null);
                        }
                    }else{
                        $entity->setTypeTransfert(null);
                    }
                    if (!empty($data[$localeName]['vehicle'])) {
                        $vehicle = $em->getRepository('AppBundle\Entity\BookVehicle')->findOneBy(array('id' => $data[$localeName]['vehicle']));
                        if ($vehicle) {
                            $entity->setVehicle($vehicle);
                        } else {
                            $entity->setVehicle(null);
                        }
                    }else{
                        $entity->setVehicle(null);
                    }
                    if (!empty($data[$localeName]['ville'])) {
                        $ville = $em->getRepository('AppBundle\Entity\Location')->findOneBy( array( 'id'=> $data[$localeName]['ville']) );
                        $entity->setVille($ville);
                    } else {
                        $entity->setVille(null);
                    }
                    if (!empty($data[$localeName]['departure_city'])) {
                        $departure_city = $em->getRepository('AppBundle\Entity\Location')->findOneBy( array( 'id'=> $data[$localeName]['departure_city']) );
                        $entity->setDepartureCity($departure_city);
                    } else {
                        $entity->setDepartureCity(null);
                    }
                    if (!empty($data[$localeName]['city_arrival'])) {
                        $city_arrival = $em->getRepository('AppBundle\Entity\Location')->findOneBy( array( 'id'=> $data[$localeName]['city_arrival']) );
                        $entity->setCityArrival($city_arrival);
                    } else {
                        $entity->setCityArrival(null);
                    }
                    if (!empty($data[$localeName]['label'])) {
                        $entity->setLabel(trim($data[$localeName]['label']));
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
                    $label = 'Tran_';
                    if($entity->getVille()){
                        $label=$label.substr($entity->getVille()->translate($localeName, true)->getCity(), 0, 3).'_';
                    }
                    if($entity->getTypeTransfert()){
                        if($entity->getTypeTransfert()->getId()==1){
                            $label=$label.'Apt_';
                        }
                        if($entity->getTypeTransfert()->getId()==2){
                            $label=$label.'Gare_';
                        }
                        if($entity->getTypeTransfert()->getId()==3){
                            $label=$label.'Intra_';
                        }
                        if($entity->getTypeTransfert()->getId()==4){
                            $label=$label.'Inter_';
                        }
                    }
                    if($entity->getDepartureCity()){
                        $label=$label.$entity->getDepartureCity()->translate($localeName, true)->getCity().'_';
                    }
                    if($entity->getCityArrival()){
                        $label=$label.$entity->getCityArrival()->translate($localeName, true)->getCity().'_';
                    }
                    if($vehicle){
                        $label=$label.$vehicle->translate($localeName, true)->getName();
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
