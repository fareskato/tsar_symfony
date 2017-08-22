<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\Assurance;
use AppBundle\Service\Locales;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AssuranceController extends Controller
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
     * @Route("/assurance", name="admin_assurance")
     */
    public function indexAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

//		$data['data_list'] = $em->getRepository('AppBundle\Entity\Assurance')->findBy(array(), array('id' => 'DESC'));
		$data['data_list'] = $em->getRepository('AppBundle\Entity\Assurance')->findAllForAdminList($em,'AppBundle\Entity\Assurance',$request);


		$data['data_fields'] = array('id','label');
		$data['data_title'] = 'adm.assurance';

		$link = $this->generateUrl('admin_assurance_add', array(), UrlGeneratorInterface::ABSOLUTE_PATH);


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
					'link' => 'admin_assurance_edit',
					'class' => '',
					'confirm' => false
				),
				array(
					'name' => 'delete',
					'link' => 'admin_assurance_delete',
					'class' => 'danger',
					'confirm' => 'adm.action.delete.confirm'
				),
			);

		$page = !empty($request->query->get('page')) ? intval($request->query->get('page')) : 1;
		$paginator = array(
			'currentFilters' => $request->query->all(),
			'currentPage' => $page,
			'paginationPath' => 'admin_assurance',
			'showAlwaysFirstAndLast' => true,
			'lastPage' => ceil(count($data['data_list']) / $this->_itemsOnPage),
		);

		$data['data_list'] = array_slice($data['data_list'], ($page * $this->_itemsOnPage - $this->_itemsOnPage), $this->_itemsOnPage);
		$data = array_merge($data,$paginator);

        return $this->render('AdminBundle:Default:list.html.twig',$data);
    }

    /**
     * @Route("/assurance/add", name="admin_assurance_add")
     * @Route("/assurance/edit/{id}", defaults={"id" = 0}, name="admin_assurance_edit")
     */
    public function editAction($id = 0, Request $request){
        $em = $this->getDoctrine()->getManager();



        $entity = new Assurance();
		$entity->setDefaultLocale($this->_defaultLocale);
		if (!empty($id)) {
			$entity = $em->getRepository('AppBundle\Entity\Assurance')->findOneBy(array('id'=>$id));
		}

        $data['entity'] = $entity;

		//TITLE FOR PAGE
		$data['data_title'] = (!empty($data['entity']->translate()->getName()) ? $data['entity']->translate()->getName() : $this->get('translator')->trans('adm.action.new'));

        $data['data_type'] =  'adm.assurance.assurance_name';

        $data['data_buttons'] = array(
            array(
                'name' => 'save',
                'link' => 'admin_assurance_edit',
                'class' => 'primary',
                'button' => true,
                'button_type' => 'submit'
            ),
            array(
                'name' => 'cancel',
                'link' => !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->generateUrl('admin_assurance', array(), UrlGeneratorInterface::ABSOLUTE_PATH),
                'class' => 'default',
            )
        );

        if ($request->isMethod('POST')) {

            $data = $request->request->all();

            $this->saveEntity($data,$entity,$em);
            return $this->redirectToRoute('admin_assurance', array());
        }

        $data['form'] = $this->createEntityForm($entity,$id,$em);


        return $this->render('AdminBundle:Default:form.html.twig',$data);
    }

    /**
     * @Route("/assurance/delete/{id}", name="admin_assurance_delete")
     */
    public function deleteAction($id){
        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository('AppBundle\Entity\Assurance')->findOneBy(array('id' => intval($id)));
        if ($data) {
            $em->remove($data);
            $em->flush();
        }


        return !empty($_SERVER['HTTP_REFERER']) ? $this->redirect($_SERVER['HTTP_REFERER']) : $this->generateUrl('admin_assurance', UrlGeneratorInterface::ABSOLUTE_PATH);

    }

	private function createEntityForm($entity, $id = 0, $em) {
		if (!empty($id)) {
			$form['action'] = $this->generateUrl('admin_assurance_edit', array('id' => $id), UrlGeneratorInterface::ABSOLUTE_PATH);
		} else{
			$form['action'] = $this->generateUrl('admin_assurance_add', array(), UrlGeneratorInterface::ABSOLUTE_PATH);
		}

		$form['id'] = $id;
		$form['form_id'] = 'form_'.md5('assurance');
		$form['separator'] = true;

        $BookTypeDassurance = $em->getRepository('AppBundle\Entity\BookTypeDassurance')->findAll();
        $BookTypeDassuranceValues = array();
        foreach ($BookTypeDassurance as $value ) {
            $BookTypeDassuranceValues[$value->getId()] = $value->translate()->getName();
        }

        $BookDurationInsurance = $em->getRepository('AppBundle\Entity\BookDurationInsurance')->findAll();
        $BookDurationInsuranceValues = array();
        foreach ($BookDurationInsurance as $value ) {
            $BookDurationInsuranceValues[$value->getId()] = $value->translate()->getName();
        }

        $BookTotalPriceStay = $em->getRepository('AppBundle\Entity\BookTotalPriceStay')->findAll();
        $BookTotalPriceStayValues = array();
        foreach ($BookTotalPriceStay as $value ) {
            $BookTotalPriceStayValues[$value->getId()] = $value->translate()->getName();
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
                'name' => array(
                    'label' => 'adm.field.title_insurance',
                    'type' => 'text',
                    'name' => $lng.'[name]',
                    'required' => false,
                    'value' => $entity ? $entity->translate($lng,($lng == $this->_defaultLocale ? true : false))->getName() : '',
                    'translate' => true
                ),
                'type_dassurance' => array(
                    'label' => 'adm.field.type_dassurance',
                    'type' => 'select',
                    'name' => $lng.'[type_dassurance]',
                    'required' => false,
                    'value' => $entity->getTypeDassurance() ? $entity->getTypeDassurance()->getId() : null,
                    'value_default' => 'adm.field.select.toplevel',
                    'values' => $BookTypeDassuranceValues,
                    'translate' => false
                ),
                'duration_insurance' => array(
                    'label' => 'adm.field.duration_insurance',
                    'type' => 'select',
                    'name' => $lng.'[duration_insurance]',
                    'required' => false,
                    'value' => $entity->getDurationInsurance() ? $entity->getDurationInsurance()->getId() : null,
                    'value_default' => 'adm.field.select.toplevel',
                    'values' => $BookDurationInsuranceValues,
                    'translate' => false
                ),
                'total_price_stay' => array(
                    'label' => 'adm.field.total_price_stay',
                    'type' => 'select',
                    'name' => $lng.'[total_price_stay]',
                    'required' => false,
                    'value' => $entity->getTotalPriceStay() ? $entity->getTotalPriceStay()->getId() : null,
                    'value_default' => 'adm.field.select.toplevel',
                    'values' => $BookTotalPriceStayValues,
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
                if (!empty($data[$localeName]['name'])) {
                    $entity->translate($localeName,false)->setName(trim($data[$localeName]['name']));
                }
                $entity->translate($localeName, false)->setActive(!empty($data[$localeName]['active']) ? 1 : 0);
                if (!empty($data[$localeName]['informations_supplementaires'])) {
                    $entity->translate($localeName,false)->setInformationsSupplementaires(trim($data[$localeName]['informations_supplementaires']));
                }
                if (!empty($data[$localeName]['information_utile'])) {
                    $entity->translate($localeName,false)->setInformationUtile(trim($data[$localeName]['information_utile']));
                }
                if (!empty($data[$localeName]['commentaires'])) {
                    $entity->translate($localeName,false)->setCommentaires(trim($data[$localeName]['commentaires']));
                }

				/* BLOCK FOR NON TRANSLATED VALUES*/
				if ($localeName == $this->_defaultLocale) {
                    if (!empty($data[$localeName]['label'])) {
                        $entity->setLabel(trim($data[$localeName]['label']));
                    }
                    if (!empty($data[$localeName]['type_dassurance'])) {
                        $type_dassurance = $em->getRepository('AppBundle\Entity\BookTypeDassurance')->findOneBy(array('id' => $data[$localeName]['type_dassurance']));
                        if ($type_dassurance) {
                            $entity->setTypeDassurance($type_dassurance);
                        } else {
                            $entity->setTypeDassurance(null);
                        }
                    }else{
                        $entity->setTypeDassurance(null);
                    }
                    if (!empty($data[$localeName]['duration_insurance'])) {
                        $duration_insurance = $em->getRepository('AppBundle\Entity\BookDurationInsurance')->findOneBy(array('id' => $data[$localeName]['duration_insurance']));
                        if ($duration_insurance) {
                            $entity->setDurationInsurance($duration_insurance);
                        } else {
                            $entity->setDurationInsurance(null);
                        }
                    }else{
                        $entity->setDurationInsurance(null);
                    }
                    if (!empty($data[$localeName]['total_price_stay'])) {
                        $total_price_stay = $em->getRepository('AppBundle\Entity\BookTotalPriceStay')->findOneBy(array('id' => $data[$localeName]['total_price_stay']));
                        if ($total_price_stay) {
                            $entity->setTotalPriceStay($total_price_stay);
                        } else {
                            $entity->setTotalPriceStay(null);
                        }
                    }else{
                        $entity->setTotalPriceStay(null);
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
                    $label = 'Assu_';
                    if($type_dassurance){
                        $label=$label.$type_dassurance->translate($localeName, true)->getName().'_';
                    }
                    if($duration_insurance){
                        $label=$label.$duration_insurance->translate($localeName, true)->getName().'_';
                    }
                    if($total_price_stay){
                        $label=$label.$total_price_stay->translate($localeName, true)->getName();
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
