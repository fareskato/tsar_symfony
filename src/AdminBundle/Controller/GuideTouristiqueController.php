<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\GuideTouristique;
use AppBundle\Service\Locales;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class GuideTouristiqueController extends Controller
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
     * @Route("/guide_touristique", name="admin_guide_touristique")
     */
    public function indexAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

//		$data['data_list'] = $em->getRepository('AppBundle\Entity\GuideTouristique')->findBy(array(), array('id' => 'DESC'));
		$data['data_list'] = $em->getRepository('AppBundle\Entity\GuideTouristique')->findAllForAdminList($em,'AppBundle\Entity\GuideTouristique',$request);


		$data['data_fields'] = array('id','label');
		$data['data_title'] = 'adm.guide_touristique';

		$link = $this->generateUrl('admin_guide_touristique_add', array(), UrlGeneratorInterface::ABSOLUTE_PATH);


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
					'link' => 'admin_guidetouristique_edit',
					'class' => '',
					'confirm' => false
				),
				array(
					'name' => 'delete',
					'link' => 'admin_guidetouristique_delete',
					'class' => 'danger',
					'confirm' => 'adm.action.delete.confirm'
				),
			);

		$page = !empty($request->query->get('page')) ? intval($request->query->get('page')) : 1;
		$paginator = array(
			'currentFilters' => $request->query->all(),
			'currentPage' => $page,
			'paginationPath' => 'admin_guide_touristique',
			'showAlwaysFirstAndLast' => true,
			'lastPage' => ceil(count($data['data_list']) / $this->_itemsOnPage),
		);

		$data['data_list'] = array_slice($data['data_list'], ($page * $this->_itemsOnPage - $this->_itemsOnPage), $this->_itemsOnPage);
		$data = array_merge($data,$paginator);

        return $this->render('AdminBundle:Default:list.html.twig',$data);
    }

    /**
     * @Route("/guide_touristique/add", name="admin_guidetouristique_add")
     * @Route("/guide_touristique/edit/{id}", defaults={"id" = 0}, name="admin_guidetouristique_edit")
     */
    public function editAction($id = 0, Request $request){
        $em = $this->getDoctrine()->getManager();



        $entity = new GuideTouristique();
		$entity->setDefaultLocale($this->_defaultLocale);
		if (!empty($id)) {
			$entity = $em->getRepository('AppBundle\Entity\GuideTouristique')->findOneBy(array('id'=>$id));
		}

        $data['entity'] = $entity;

		//TITLE FOR PAGE
		$data['data_title'] = (!empty($data['entity']->translate()->getName()) ? $data['entity']->translate()->getName() : $this->get('translator')->trans('adm.action.new'));

        $data['data_type'] =  'adm.guide_touristique.guide_touristique_name';

        $data['data_buttons'] = array(
            array(
                'name' => 'save',
                'link' => 'admin_guidetouristique_edit',
                'class' => 'primary',
                'button' => true,
                'button_type' => 'submit'
            ),
            array(
                'name' => 'cancel',
                'link' => !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->generateUrl('admin_guide_touristique', array(), UrlGeneratorInterface::ABSOLUTE_PATH),
                'class' => 'default',
            )
        );

        if ($request->isMethod('POST')) {

            $data = $request->request->all();

            $this->saveEntity($data,$entity,$em);
            return $this->redirectToRoute('admin_guide_touristique', array());
        }

        $data['form'] = $this->createEntityForm($entity,$id,$em);


        return $this->render('AdminBundle:Default:form.html.twig',$data);
    }

    /**
     * @Route("/guide_touristique/delete/{id}", name="admin_guide_touristique_delete")
     */
    public function deleteAction($id){
        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository('AppBundle\Entity\GuideTouristique')->findOneBy(array('id' => intval($id)));
        if ($data) {
            $em->remove($data);
            $em->flush();
        }


        return !empty($_SERVER['HTTP_REFERER']) ? $this->redirect($_SERVER['HTTP_REFERER']) : $this->generateUrl('admin_guide_touristique', UrlGeneratorInterface::ABSOLUTE_PATH);

    }

	private function createEntityForm($entity, $id = 0, $em) {
		if (!empty($id)) {
			$form['action'] = $this->generateUrl('admin_guidetouristique_edit', array('id' => $id), UrlGeneratorInterface::ABSOLUTE_PATH);
		} else{
			$form['action'] = $this->generateUrl('admin_guidetouristique_add', array(), UrlGeneratorInterface::ABSOLUTE_PATH);
		}

		$form['id'] = $id;
		$form['form_id'] = 'form_'.md5('guide_touristique');
		$form['separator'] = true;

        $BookLangue = $em->getRepository('AppBundle\Entity\BookLangue')->findAll();
        $BookLangueValues = array();
        foreach ($BookLangue as $value ) {
            $BookLangueValues[$value->getId()] = $value->translate()->getName();
        }

        $BookTypeGuide = $em->getRepository('AppBundle\Entity\BookTypeGuide')->findAll();
        $BookTypeGuideValues = array();
        foreach ($BookTypeGuide as $value ) {
            $BookTypeGuideValues[$value->getId()] = $value->translate()->getName();
        }

        $BookDuree = $em->getRepository('AppBundle\Entity\BookDuree')->findAll();
        $BookDureeValues = array();
        foreach ($BookDuree as $value ) {
            $BookDureeValues[$value->getId()] = $value->translate()->getName();
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

                'langue' => array(
                    'label' => 'adm.field.langue',
                    'type' => 'select',
                    'name' => $lng.'[langue]',
                    'required' => false,
                    'value' => $entity->getLangue() ? $entity->getLangue()->getId() : null,
                    'value_default' => 'adm.field.select.toplevel',
                    'values' => $BookLangueValues,
                    'translate' => false
                ),
                'type_guide' => array(
                    'label' => 'adm.field.type_guide',
                    'type' => 'select',
                    'name' => $lng.'[type_guide]',
                    'required' => false,
                    'value' => $entity->getTypeGuide() ? $entity->getTypeGuide()->getId() : null,
                    'value_default' => 'adm.field.select.toplevel',
                    'values' => $BookTypeGuideValues,
                    'translate' => false
                ),
                'duree' => array(
                    'label' => 'adm.field.duree',
                    'type' => 'select',
                    'name' => $lng.'[duree]',
                    'required' => false,
                    'value' => $entity->getDuree() ? $entity->getDuree()->getId() : null,
                    'value_default' => 'adm.field.select.toplevel',
                    'values' => $BookDureeValues,
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
                if (!empty($data[$localeName]['informations_supplementaires'])) {
                    $entity->translate($localeName,false)->setInformationsSupplementaires(trim($data[$localeName]['informations_supplementaires']));
                }
                if (!empty($data[$localeName]['information_utile'])) {
                    $entity->translate($localeName,false)->setInformationUtile(trim($data[$localeName]['information_utile']));
                }
                if (!empty($data[$localeName]['commentaires'])) {
                    $entity->translate($localeName,false)->setCommentaires(trim($data[$localeName]['commentaires']));
                }
                $entity->translate($localeName, false)->setActive(!empty($data[$localeName]['active']) ? 1 : 0);


				/* BLOCK FOR NON TRANSLATED VALUES*/
				if ($localeName == $this->_defaultLocale) {
                    if (!empty($data[$localeName]['label'])) {
                        $entity->setLabel(trim($data[$localeName]['label']));
                    }
                    $entity->setTypeGroup(!empty($data[$localeName]['type_group']) ? 1 : 0);
                    if (!empty($data[$localeName]['phone'])) {
                        $entity->setPhone(trim($data[$localeName]['phone']));
                    }

                    if (!empty($data[$localeName]['ville'])) {
                        $ville = $em->getRepository('AppBundle\Entity\Location')->findOneBy( array( 'id'=> $data[$localeName]['ville']) );
                        $entity->setVille($ville);
                    } else {
                        $entity->setVille(null);
                    }


                    if (!empty($data[$localeName]['langue'])) {
                        $langue = $em->getRepository('AppBundle\Entity\BookLangue')->findOneBy(array('id' => $data[$localeName]['langue']));
                        if ($langue) {
                            $entity->setLangue($langue);
                        } else {
                            $entity->setLangue(null);
                        }
                    }else{
                        $entity->setLangue(null);
                    }
                    if (!empty($data[$localeName]['type_guide'])) {
                        $type_guide = $em->getRepository('AppBundle\Entity\BookTypeGuide')->findOneBy(array('id' => $data[$localeName]['type_guide']));
                        if ($type_guide) {
                            $entity->setTypeGuide($type_guide);
                        } else {
                            $entity->setTypeGuide(null);
                        }
                    }else{
                        $entity->setTypeGuide(null);
                    }
                    if (!empty($data[$localeName]['duree'])) {
                        $duree= $em->getRepository('AppBundle\Entity\BookDuree')->findOneBy(array('id' => $data[$localeName]['duree']));
                        if ($duree) {
                            $entity->setDuree($duree);
                        } else {
                            $entity->setDuree(null);
                        }
                    }else{
                        $entity->setDuree(null);
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

                    $label = 'Guid_';
                    if($ville){
                        $label=$label.$ville->translate($localeName, true)->getCity().'_';
                    }
                    if($langue){
                        $label=$label.$langue->translate($localeName, true)->getName().'_';
                    }
                    if($type_guide){
                        $label=$label.$type_guide->translate($localeName, true)->getName().'_';
                    }
                    if($duree){
                        $label=$label.$duree->translate($localeName, true)->getName();
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
