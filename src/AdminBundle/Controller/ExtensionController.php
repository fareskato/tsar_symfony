<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\Extension;
use AppBundle\Entity\ExtensionToDay;
use AppBundle\Entity\ExtensionToExtensionDestination;
use AppBundle\Entity\Minigroup;
use AppBundle\Service\Locales;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ExtensionController extends Controller
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
     * @Route("/extension", name="admin_extension")
     */
    public function indexAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

//		$data['data_list'] = $em->getRepository('AppBundle\Entity\Extension')->findBy(array(), array('id' => 'DESC'));
		$data['data_list'] = $em->getRepository('AppBundle\Entity\Extension')->findAllForAdminList($em,'AppBundle\Entity\Extension',$request);


		$data['data_fields'] = array('id','image_miniature','name','active_domain','active_lang');
		$data['data_title'] = 'adm.extension';

		$link = $this->generateUrl('admin_extension_add', array(), UrlGeneratorInterface::ABSOLUTE_PATH);


		$data['data_buttons'] = array(
			array(
				'name' => 'add',
				'link' => $link,
				'class' => 'primary'
			),
			array(
				'name' => 'reset_filters',
				'link' => $this->generateUrl('admin_extension'),
				'class' => 'default'
			)
		);

		$data['data_actions'] = array(
				array(
					'name' => 'edit',
					'link' => 'admin_extension_edit',
					'class' => '',
					'confirm' => false
				),
				array(
					'name' => 'delete',
					'link' => 'admin_extension_delete',
					'class' => 'danger',
					'confirm' => 'adm.action.delete.confirm'
				),
			);

		$page = !empty($request->query->get('page')) ? intval($request->query->get('page')) : 1;
		$paginator = array(
			'currentFilters' => $request->query->all(),
			'currentPage' => $page,
			'paginationPath' => 'admin_extension',
			'showAlwaysFirstAndLast' => true,
			'lastPage' => ceil(count($data['data_list']) / $this->_itemsOnPage),
		);

		$data['data_list'] = array_slice($data['data_list'], ($page * $this->_itemsOnPage - $this->_itemsOnPage), $this->_itemsOnPage);
		$data = array_merge($data,$paginator);

        return $this->render('AdminBundle:Default:list.html.twig',$data);
    }

    /**
     * @Route("/extension/add", name="admin_extension_add")
     * @Route("/extension/edit/{id}", defaults={"id" = 0}, name="admin_extension_edit")
     */
    public function editAction($id = 0, Request $request){
        $em = $this->getDoctrine()->getManager();

        $entity = new Extension();
		$entity->setDefaultLocale($this->_defaultLocale);
        if (!empty($id)) {
            $entity = $em->getRepository('AppBundle\Entity\Extension')->findOneBy(array('id'=>$id));
        }//var_dump($entity->isActive()); exit;
        $data['entity'] = $entity;
//print_r(get_class_methods($data['entity']->translate())); exit;
        //TITLE FOR PAGE
        $data['data_title'] = (!empty($data['entity']->translate()) ? $data['entity']->translate()->getName() : $this->get('translator')->trans('adm.action.new'));

        $data['data_type'] =  'adm.extension';

        $data['data_buttons'] = array(
            array(
                'name' => 'save',
                'link' => 'admin_extension_edit',
                'class' => 'primary',
                'button' => true,
                'button_type' => 'submit'
            ),
            array(
                'name' => 'cancel',
                'link' => !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->generateUrl('admin_extension', array(), UrlGeneratorInterface::ABSOLUTE_PATH),
                'class' => 'default',
            )
        );

        if ($request->isMethod('POST')) {

            $data = $request->request->all();

            $this->saveEntity($data,$entity,$em);
            return $this->redirectToRoute('admin_extension', array());
        }

        $data['form'] = $this->createEntityForm($entity,$id,$em);

        return $this->render('AdminBundle:Default:form.html.twig',$data);
    }

    /**
     * @Route("/extension/delete/{id}", name="admin_extension_delete")
     */
    public function deleteAction($id){
        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository('AppBundle\Entity\Extension')->findOneBy(array('id' => intval($id)));
        if ($data) {
            $em->remove($data);
            $em->flush();
        }


        return !empty($_SERVER['HTTP_REFERER']) ? $this->redirect($_SERVER['HTTP_REFERER']) : $this->generateUrl('admin_extension', UrlGeneratorInterface::ABSOLUTE_PATH);

    }

	private function createEntityForm($entity, $id = 0, $em) {
        if (!empty($id)) {
            $form['action'] = $this->generateUrl('admin_extension_edit', array('id' => $id), UrlGeneratorInterface::ABSOLUTE_PATH);
        } else{
            $form['action'] = $this->generateUrl('admin_extension_add', array(), UrlGeneratorInterface::ABSOLUTE_PATH);
        }
        $form['id'] = $id;
        $form['form_id'] = 'form_'.md5('extension');
        $form['separator'] = true;

        $otherImages = $entity->getImageOther() ? $entity->getImageOther() : array();
        $otherImagesValue = array();
        foreach($otherImages as $i) {
            $otherImagesValue[] = array(
                'path' => $i->getUrl(),
                'value' => $i->getId(),
            );
        }
        $domains = $em->getRepository('AppBundle\Entity\BookDomain')->findAll();
        $domainValues = array();
        foreach ($domains as $domain ) {
            $domainValues[$domain->getId()] = $domain->translate()->getName();
        }
        $domainCheckedValues = array();
        if (!empty($entity->getTypeDomain())) {
            foreach($entity->getTypeDomain() as $domain) {
                $domainCheckedValues[] = $domain->getId();
            }
        }



        $extension_season = $em->getRepository('AppBundle\Entity\BookSeason')->findAll();
        $extension_season_values = array();
        foreach ($extension_season as $season ) {
            $extension_season_values[$season->getId()] = $season->translate()->getName();
        }
        $extension_season_checed = array();
        if (!empty($entity->getSeason())) {
            foreach ($entity->getSeason() as $season) {
                $extension_season_checed[] = $season->getId();
            }
        }

        foreach($this->_locales as $lng) {
            $type_extension=array();
            $extensions = $em->getRepository('AppBundle\Entity\BookTypeExtension')->findAll();
            foreach ($extensions as $extension ) {
                $type_extension[$extension->getId()] = $extension->translate($lng,($lng == $this->_defaultLocale ? true : false))->getName();
            }

            $fields = array(
                'name' => array(
                    'label' => 'adm.field.title',
                    'type' => 'text',
                    'name' => $lng.'[name]',
                    'required' => ($lng != $this->_defaultLocale ? false : true),
                    'value' => $entity ? $entity->translate($lng,($lng == $this->_defaultLocale ? true : false))->getName() : '',
                    'translate' => true
                ),
                'headline_liste' => array(
                    'label' => 'adm.field.headline_liste',
                    'type' => 'text',
                    'name' => $lng.'[headline_liste]',
                    'required' => false,
                    'value' => $entity ? $entity->translate($lng,($lng == $this->_defaultLocale ? true : false))->getHeadlineListe() : '',
                    'translate' => true
                ),

              'slug' => array(
                'label' => 'adm.field.slug',
                'type' => 'text',
                'name' => $lng.'[slug]',
                'required' => false,
                'value' => $entity ? $entity->translate($lng,($lng == $this->_defaultLocale ? true : false))->getSlug() : '',
                'translate' => true
              ),
				'body_summary' => array(
					'label' => 'adm.field.body_summary',
					'type' => 'text',
					'name' => $lng.'[body_summary]',
					'required' => false,
					'value' => $entity ? $entity->translate($lng,($lng == $this->_defaultLocale ? true : false))->getBodySummary() : '',
					'translate' => true
				),
				'body' => array(
					'label' => 'adm.field.body',
					'type' => 'texteditor',
					'name' => $lng.'[body]',
					'required' => false,
					'value' => $entity ? $entity->translate($lng,($lng == $this->_defaultLocale ? true : false))->getBody() : '',
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
					'checked_values' => $domainCheckedValues, //only checked values array
					'translate' => false
				),
				'favorite' => array(
					'label' => 'adm.field.favorite',
					'type' => 'checkbox',
					'name' => $lng.'[favorite]',
					'value' => 1,
					'checked' => $entity ? $entity->isFavorite() : 0,
					'translate' => true
				),

				'separator_type' => array(
					'label' => 'adm.field.type',
					'type' => 'separator',
					'translate' => false
				),
                /*'type_extension' => array(
                    'label' => 'adm.field.type_extension',
                    'type' => 'select',
                    'name' => $lng.'[type_extension]',
                    'required' => false,
                    'value' => $entity->getTypeExtension() ? $entity->getTypeExtension()->getId() : null,
                    'value_zero' => false,
                    'values' => $type_extension,
                    'translate' => false
                ),*/
				'extension_season' => array(
					'label' => 'adm.field.extension_season',
					'type' => 'checkbox_multiple',
					'name' => $lng.'[extension_season]',
					'values' => $extension_season_values,
					'checked_values' => $extension_season_checed, //only checked values array
					'translate' => false
				),
				'separator_days' => array(
					'label' => 'adm.field.days',
					'type' => 'separator',
					'translate' => false
				),
                'amount_days' => array(
                    'label' => 'adm.field.tarif.amount_days',
                    'type' => 'text',
                    'name' => $lng.'[amount_days]',
                    'required' => false,
                    'value' => $entity->getAmountDays() ? $entity->getAmountDays() : 0,
                    'translate' => false
                ),
				'day' => array(
					'label' => 'adm.field.day',
					'type' => 'relation_many',
					'autocomplete' => 'day',
					'autocomplete_path' => 'admin_autocomplete_day',
					'name' => $lng.'[day][]',
					'add' => 'admin_location_add',
					'field_rel' => array('id','name'),
					'values' => $entity->getDay() ? $entity->getDay() : null,
					'sortable' => true,
					'translate' => false,
					'editLink' => array(
						'type' => 'day',
						'path' => array(),
					),
				),

				'separator_places' => array(
					'label' => 'adm.field.places',
					'type' => 'separator',
					'translate' => false
				),
				'starting_point' => array(
					'label' => 'adm.field.starting_point',
					'type' => 'relation_one',
					'autocomplete' => 'destination',
					'autocomplete_path' => 'admin_autocomplete_destination',
					'name' => $lng.'[starting_point]',
					'add' => 'admin_destination_add',
					'field_rel' => array('id','name'),
					'values' => $entity->getStartingPoint() ? $entity->getStartingPoint() : array(),
					'translate' => false,
					'editLink' => array(
						'type' => 'destination',
						'path' => $entity->getStartingPoint() ? array('id' => $entity->getStartingPoint()->getId()) : array(),
					),
				),
				'extension' => array(
					'label' => 'adm.field.extension.vers',
					'type' => 'relation_many',
					'autocomplete' => 'destination',
					'autocomplete_path' => 'admin_autocomplete_destination',
					'name' => $lng.'[extension][]',
					'add' => 'admin_destination_add',
					'field_rel' => array('id','name'),
					'values' => $entity->getExtension() ? $entity->getExtension() : array(),
					'sortable' => true,
					'translate' => false,
					'editLink' => array(
						'type' => 'destination',
						'path' => array(),
					),
				),
				'related_content' => array(
					'label' => 'adm.field.trip_lies',
					'type' => 'relation_many',
					'autocomplete' => 'destination',
					'autocomplete_path' => 'admin_autocomplete_destination',
					'name' => $lng.'[related_content][]',
					'add' => 'admin_destination_add',
					'field_rel' => array('id','name'),
					'values' => $entity->getRelatedContent() ? $entity->getRelatedContent() : array(),
					'translate' => false,
					'editLink' => array(
						'type' => 'destination',
						'path' => array(),
					),
				),
				'extension_recreation' => array(
					'label' => 'adm.field.extension_recreation',
					'type' => 'relation_many',
					'autocomplete' => 'recreation',
					'autocomplete_path' => 'admin_autocomplete_recreation',
					'name' => $lng.'[extension_recreation][]',
					'add' => 'admin_destination_add',
					'field_rel' => array('id','name'),
					'values' => $entity->getExtensionRecreation() ? $entity->getExtensionRecreation() : array(),
					'translate' => false,
					'editLink' => array(
						'type' => 'book',
						'path' => array('book_name' => 'book_type_recreation'),
					),
				),
				'separator_location' => array(
					'label' => 'adm.field.location',
					'type' => 'separator',
					'translate' => false
				),
				'location' => array(
					'label' => 'adm.field.location',
					'type' => 'relation_one',
					'autocomplete' => 'location',
					'autocomplete_path' => 'admin_autocomplete_location',
					'name' => $lng.'[location]',
					'add' => 'admin_location_add',
					'field_rel' => array('id','name','street','city','getCountryName'),
					'values' => $entity->getLocation() ? $entity->getLocation() : null,
					'translate' => false,
					'editLink' => array(
						'type' => 'location',
						'path' => $entity->getLocation() ? array('id' => $entity->getLocation()->getId()) : array(),
					),
				),
                'separator_images' => array(
                    'label' => 'adm.field.images',
                    'type' => 'separator',
                    'translate' => false
                ),
                'image_background' => array(
                    'label' => 'adm.field.image.background',
                    'type' => 'image',
                    'name' => $lng.'[image_background]',
                    'path' => $entity->getImageBackground() ? $entity->getImageBackground()->getUrl() : false,
                    'value' => $entity->getImageBackground() ? $entity->getImageBackground()->getId() : false,
                    'required' => false,
                    'translate' => false
                ),
                'image_miniature' => array(
                    'label' => 'adm.field.image.miniature',
                    'type' => 'image',
                    'name' => $lng.'[image_miniature]',
                    'path' => $entity->getImageThumbnail() ? $entity->getImageThumbnail()->getUrl() : false,
                    'value' => $entity->getImageThumbnail() ? $entity->getImageThumbnail()->getId() : false,
                    'required' => false,
                    'translate' => false
                ),
                'image_other' => array(
                    'label' => 'adm.field.image.others',
                    'type' => 'images',
                    'name' => $lng.'[image_others][]',
                    'values' => $otherImagesValue,
                    'maximum' => 10,
                    'required' => false,
                    'translate' => false
                ),

                'separator_tarifs' => array(
                    'label' => 'adm.field.tarifs',
                    'type' => 'separator',
                    'translate' => false
                ),
                'tariffed_product' => array(
                    'label' => 'adm.field.tariffed_product',
                    'type' => 'checkbox',
                    'name' => $lng.'[tariffed_product]',
                    'value' => 1,
                    'checked' => $entity ? $entity->isTariffedProduct() : 0,
                    'translate' => false
                ),
                'price_flexibility' => array(
                    'label' => 'adm.field.price_flexibility',
                    'type' => 'checkbox',
                    'name' => $lng.'[price_flexibility]',
                    'value' => 1,
                    'checked' => $entity ? $entity->isPriceFlexibility() : 0,
                    'translate' => false
                ),
                'text_under_price' => array(
                    'label' => 'adm.field.text_under_price',
                    'type' => 'text',
                    'name' => $lng.'[text_under_price]',
                    'required' => false,
                    'value' => $entity ? $entity->translate($lng,($lng == $this->_defaultLocale ? true : false))->getTextUnderPrice() : '',
                    'translate' => true
                ),
                'product_365' => array(
                    'label' => 'adm.field.product_365',
                    'type' => 'text',
                    'name' => $lng.'[product_365]',
                    'required' => false,
                    'value' => $entity ? $entity->getProduct365() : '',
                    'translate' => false
                ),
                'service_details' => array(
                    'label' => 'adm.field.service_details',
                    'type' => 'text',
                    'name' => $lng.'[service_details]',
                    'required' => false,
                    'value' => $entity ? $entity->translate($lng,($lng == $this->_defaultLocale ? true : false))->getServiceDetails() : '',
                    'translate' => true
                ),
                'external_booking' => array(
                    'label' => 'adm.field.external_booking',
                    'type' => 'checkbox',
                    'name' => $lng.'[external_booking]',
                    'value' => 1,
                    'checked' => $entity ? $entity->isExternalBooking() : 0,
                    'translate' => true
                ),
                'external_booking_link' => array(
                    'label' => 'adm.field.external_booking_link',
                    'type' => 'text',
                    'name' => $lng.'[external_booking_link]',
                    'required' => false,
                    'value' => $entity ? $entity->getExternalBookingLink() : '',
                    'translate' => false
                ),
                'external_booking_link_label' => array(
                    'label' => 'adm.field.external_booking_link_label',
                    'type' => 'text',
                    'name' => $lng.'[external_booking_link_label]',
                    'required' => false,
                    'value' => $entity ? $entity->translate($lng,($lng == $this->_defaultLocale ? true : false))->getExternalBookingLinkLabel() : '',
                    'translate' => true
                ),
                'conditions_sale' => array(
                    'label' => 'adm.field.conditions_sale',
                    'type' => 'texteditor',
                    'name' => $lng.'[conditions_sale]',
                    'required' => false,
                    'value' => $entity ? $entity->translate($lng,($lng == $this->_defaultLocale ? true : false))->getConditionsSale() : '',
                    'translate' => true
                ),
                'price_displayed' => array(
                    'label' => 'adm.field.price_displayed',
                    'type' => 'text',
                    'name' => $lng.'[price_displayed]',
                    'required' => false,
                    'value' => $entity ? $entity->getPriceDisplayed() : '',
                    'translate' => false
                ),
                'prix_euro' => array(
                    'label' => 'adm.field.prix_euro',
                    'type' => 'text',
                    'name' => $lng.'[prix_euro]',
                    'required' => false,
                    'value' => $entity ? $entity->getPrixEuro()  :  '',
                    'translate' => false
                ),
                'prix_rouble' => array(
                    'label' => 'adm.field.prix_rouble',
                    'type' => 'text',
                    'name' => $lng.'[prix_rouble]',
                    'required' => false,
                    'value' => $entity ? $entity->getPrixRouble()  :  '',
                    'translate' => false
                ),
                'auto_update_price' => array(
                    'label' => 'adm.field.auto_update_price',
                    'type' => 'checkbox',
                    'name' => $lng.'[auto_update_price]',
                    'value' => 1,
                    'checked' => $entity ? $entity->isAutoUpdatePrice() : 0,
                    'translate' => false
                ),

                'separator_minigroup' => array(
                    'label' => 'adm.field.separator_minigroup',
                    'type' => 'separator',
                    'translate' => false
                ),
                'mini_groupe' => array(
                    'label' => 'adm.field.mini_groupe',
                    'type' => 'checkbox',
                    'name' => $lng.'[mini_groupe]',
                    'value' => 1,
                    'checked' => $entity ? $entity->isMiniGroupe()  : 0,
                    'translate' => false
                ),
                'promoted_fronpage' => array(
                    'label' => 'adm.field.promoted_fronpage',
                    'type' => 'checkbox',
                    'name' => $lng.'[promoted_fronpage]',
                    'value' => 1,
                    'checked' => $entity ? $entity->isPromotedFronpage()  : 0,
                    'translate' => false
                ),
                'minigroup_name' => array(
                    'label' => 'adm.field.minigroup_name',
                    'type' => 'text',
                    'name' => $lng.'[minigroup_name]',
                    'required' => false,
                    'value' => $entity ? $entity->getMinigroupName()  :  '',
                    'translate' => false
                ),
                'minigroup_promotion_weight' => array(
                    'label' => 'adm.field.minigroup_promotion_weight',
                    'type' => 'text',
                    'name' => $lng.'[minigroup_promotion_weight]',
                    'required' => false,
                    'value' => $entity ? $entity->getMinigroupPromotionWeight()  :  '',
                    'translate' => false
                ),
                'minigroup_prix_euros' => array(
                    'label' => 'adm.field.minigroup_prix_euros',
                    'type' => 'text',
                    'name' => $lng.'[minigroup_prix_euros]',
                    'required' => false,
                    'value' => $entity ? $entity->getMinigroupPrixEuros()  :  '',
                    'translate' => false
                ),
                'minigroup_prix_rubles' => array(
                    'label' => 'adm.field.minigroup_prix_rubles',
                    'type' => 'text',
                    'name' => $lng.'[minigroup_prix_rubles]',
                    'required' => false,
                    'value' => $entity ? $entity->getMinigroupPrixRubles()  :  '',
                    'translate' => false
                ),
				'minigroup' => array(
					'label' => 'adm.field.minigroup',
					'type' => 'minigroup',
					'name' => $lng.'[minigroup]',
					'values' => $entity->getExtensionMinigroup() ? $entity->getExtensionMinigroup() : array(),
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
        //echo'<pre>'; print_r($data); exit;
        //$em->persist($entity);
        //$em->flush();
        foreach ($data as $localeName => $locale) {
            if (in_array($localeName,$this->_locales)) {
                $entity->setTranslatableLocale($localeName);
                /* BLOCK FOR TRANSLABLE VALUES*/
                if (!empty($data[$localeName]['name'])) {
                    $entity->translate($localeName,false)->setName(trim($data[$localeName]['name']));
                }
                if (!empty($data[$localeName]['headline_liste'])) {
                    $entity->translate($localeName,false)->setHeadlineListe(trim($data[$localeName]['headline_liste']));
                }
                if (!empty($data[$localeName]['body_summary'])) { //print_r($data[$localeName]['body_summary']); echo'<hr>';
                    $entity->translate($localeName,false)->setBodySummary(trim($data[$localeName]['body_summary']));
                }
                if (!empty($data[$localeName]['body'])) {
                    $entity->translate($localeName,false)->setBody(trim($data[$localeName]['body']));
                }
                if (!empty($data[$localeName]['name'])) {
                    $entity->translate($localeName, false)->setActive(!empty($data[$localeName]['active']) ? 1 : 0);
                }
              if (!empty($data[$localeName]['name'])) {
                $entity->translate($localeName,false)->setSlug($data[$localeName]['slug']);
              }
                /* TARIFS */
                if (!empty($data[$localeName]['text_under_price'])) {
                    $entity->translate($localeName,false)->setTextUnderPrice(trim($data[$localeName]['text_under_price']));
                }
                if (!empty($data[$localeName]['service_details'])) {
                    $entity->translate($localeName,false)->setServiceDetails(trim($data[$localeName]['service_details']));
                }
                if (!empty($data[$localeName]['external_booking_link_label'])) {
                    $entity->translate($localeName,false)->setExternalBookingLinkLabel(trim($data[$localeName]['external_booking_link_label']));
                }
                if (!empty($data[$localeName]['conditions_sale'])) {
                    $entity->translate($localeName,false)->setConditionsSale(trim($data[$localeName]['conditions_sale']));
                }
                /* END TARIFS */
                /* END BLOCK FOR TRANSLABLE VALUES*/

                /* BLOCK FOR NON TRANSLATED VALUES*/
                if ($localeName == $this->_defaultLocale) {
                    /* TARIFS */
                    $entity->setTariffedProduct(!empty($data[$localeName]['tariffed_product']) ? 1 : 0);
                    $entity->setPriceFlexibility(!empty($data[$localeName]['price_flexibility']) ? 1 : 0);
                    $entity->setAutoUpdatePrice(!empty($data[$localeName]['auto_update_price']) ? 1 : 0);
                    if (!empty($data[$localeName]['product_365'])) {
                        $entity->setProduct365(!empty($data[$localeName]['product_365']) ? $data[$localeName]['product_365'] : 0);
                    }
                    if (!empty($data[$localeName]['external_booking_link'])) {
                        $entity->setExternalBookingLink(!empty($data[$localeName]['external_booking_link']) ? $data[$localeName]['external_booking_link'] : 0);
                    }
                    $entity->setExternalBooking(!empty($data[$localeName]['external_booking']) ? 1 : 0);
                    if (!empty($data[$localeName]['price_displayed'])) {
                        $entity->setPriceDisplayed(!empty($data[$localeName]['price_displayed']) ? $data[$localeName]['price_displayed'] : 0);
                    }
                    $entity->setPrixEuro(!empty($data[$localeName]['prix_euro']) ? intval($data[$localeName]['prix_euro']) : 0);
                    $entity->setPrixRouble(!empty($data[$localeName]['prix_rouble']) ? intval($data[$localeName]['prix_rouble']) : 0);
                    /* END TARIFS */
					if (isset($data[$localeName]['favorite'])) {
						$entity->setFavorite(!empty($data[$localeName]['favorite']) ? 1 : 0);
					}

                    $entity->setMiniGroupe(!empty($data[$localeName]['mini_groupe']) ? 1 : 0);
                    $entity->setPromotedFronpage(!empty($data[$localeName]['promoted_fronpage']) ? 1 : 0);
                    if (!empty($data[$localeName]['minigroup_name'])) {
                        $entity->setMinigroupName(trim($data[$localeName]['minigroup_name']));
                    }
                    if (!empty($data[$localeName]['minigroup_promotion_weight'])) {
                        $entity->setMinigroupPromotionWeight(intval($data[$localeName]['minigroup_promotion_weight']));
                    }
                    if (!empty($data[$localeName]['minigroup_prix_euros'])) {
                        $entity->setMinigroupPrixEuros(intval($data[$localeName]['minigroup_prix_euros']));
                    }
                    if (!empty($data[$localeName]['minigroup_prix_rubles'])) {
                        $entity->setMinigroupPrixRubles(intval($data[$localeName]['minigroup_prix_rubles']));
                    }
                    if (!empty($data[$localeName]['amount_days'])) {
                        $entity->setAmountDays(intval($data[$localeName]['amount_days']));
                    }
                    $imageMiniature = $em->getRepository('AppBundle\Entity\Files')->findOneBy( array( 'id'=> $data[$localeName]['image_miniature']) );
                    if ($imageMiniature) {
                        $entity->setImageThumbnail($imageMiniature);
                    } else {
                        $entity->setImageThumbnail(null);
                    }

                    $backgroundImage = $em->getRepository('AppBundle\Entity\Files')->findOneBy( array( 'id'=> $data[$localeName]['image_background']) );
                    if ($backgroundImage) {
                        $entity->setImageBackground($backgroundImage);
                    } else {
                        $entity->setImageBackground(null);
                    }

                    if (!empty($data[$localeName]['image_others'])) {
                        $otherImages = array_filter($data[$localeName]['image_others'], function($value) { return $value !== ''; });
                        $otherImage = $em->getRepository('AppBundle\Entity\Files')->findBy( array( 'id'=> $otherImages ) );
                        $entity->setImageOther($otherImage);
                    } else {
                        $entity->setImageOther(null);
                    }

                    if (!empty($data[$localeName]['type_domain'])) {
                        $selectedDomains = array();
                        foreach($data[$localeName]['type_domain'] as $domain => $value){
                            if (!empty($value)) {
                                $selectedDomains[] = $domain;
                            }
                        }
                        $domains = $em->getRepository('AppBundle\Entity\BookDomain')->findBy( array( 'id'=> $selectedDomains) );
                        $entity->setTypeDomain($domains);
                    } else {
                        $entity->setTypeDomain(null);
                    }

                    /*$type_extension = $em->getRepository('AppBundle\Entity\BookTypeExtension')->findOneBy( array( 'id'=> $data[$localeName]['type_extension']) );
                    if ($type_extension) {
                        $entity->setTypeExtension($type_extension);
                    } else {
                        $entity->setTypeExtension(null);
                    }*/

                    if (!empty($data[$localeName]['starting_point'])) {
                        $starting_point = $em->getRepository('AppBundle\Entity\Destination')->findOneBy(array('id' => $data[$localeName]['starting_point']));
                        if ($starting_point) {
                            $entity->setStartingPoint($starting_point);
                        } else {
                            $entity->setStartingPoint(null);
                        }
                    }

                    /*if (!empty($data[$localeName]['extension'])) {
                        $extension = $em->getRepository('AppBundle\Entity\Destination')->findBy( array( 'id'=> $data[$localeName]['extension']) );
                        $entity->setExtension($extension);
                    } else {
                        $entity->setExtension(null);
                    }*/

                    $extension = $em->getRepository('AppBundle\Entity\ExtensionToExtensionDestination')->findBy( array( 'extension'=> $entity) );
                    foreach($extension as $value) {
                        $em->remove($value);
                    }
                    if (!empty($data[$localeName]['extension'])) {
                        foreach($data[$localeName]['extension'] as $key=>$value){
                            $array=array();
                            $Destination = $em->getRepository('AppBundle\Entity\Destination')->findOneBy( array( 'id'=> $value) );
                            if($Destination){
                                $toDay = new ExtensionToExtensionDestination();
                                $toDay->setDestination($Destination);
                                $toDay->setExtension($entity);
                                $toDay->setPosition($key);
                                $em->persist($toDay);
                                $array[]=$toDay;
                            }
                        }

                        $entity->setExtension($array);
                    } else {
                        $entity->setExtension(array());
                    }




                    if (!empty($data[$localeName]['extension_season'])) {
                        $selectedSeason = array();
                        foreach($data[$localeName]['extension_season'] as $domain => $value){
                            if (!empty($value)) {
                                $selectedSeason[] = $domain;
                            }
                        }
                        $Season = $em->getRepository('AppBundle\Entity\BookSeason')->findBy( array( 'id'=> $selectedSeason) );
                        $entity->setSeason($Season);
                    } else {
                        $entity->setSeason(null);
                    }

                    if (!empty($data[$localeName]['extension_recreation'])) {
                        $extension = $em->getRepository('AppBundle\Entity\BookTypeRecreation')->findBy( array( 'id'=> $data[$localeName]['extension_recreation']) );
                        $entity->setExtensionRecreation($extension);
                    } else {
                        $entity->setExtensionRecreation(null);
                    }

                    if (!empty($data[$localeName]['related_content'])) {
                        $related_content = $em->getRepository('AppBundle\Entity\Destination')->findBy( array( 'id'=> $data[$localeName]['related_content']) );
                        $entity->setRelatedContent($related_content);
                    } else {
                        $entity->setRelatedContent(null);
                    }

                    if (!empty($data[$localeName]['location'])) {
                        $location = $em->getRepository('AppBundle\Entity\Location')->findOneBy( array( 'id'=> $data[$localeName]['location']) );
                        $entity->setLocation($location);
                    } else {
                        $entity->setLocation(null);
                    }
                    $day = $em->getRepository('AppBundle\Entity\ExtensionToDay')->findBy( array( 'extension'=> $entity) );
                    foreach($day as $value) {
                        $em->remove($value);
                    }
                    if (!empty($data[$localeName]['day'])) {
                        foreach($data[$localeName]['day'] as $key=>$value){
                            $array=array();
                            $day = $em->getRepository('AppBundle\Entity\Day')->findOneBy( array( 'id'=> $value) );
                            if($day){
                                $toDay = new ExtensionToDay();
                                $toDay->setDay($day);
                                $toDay->setExtension($entity);
                                $toDay->setPosition($key);
                                $em->persist($toDay);
                                $array[]=$toDay;
                            }
                        }

                        $entity->setDay($array);
                    } else {
                        $entity->setDay(array());
                    }
                    $array=array();
                    if($entity->getExtensionMinigroup()) {
                        foreach ($entity->getExtensionMinigroup()->toArray() as $value) {
                            $array[] = $value->getId();
                        }
                    }
                    $entity->setExtensionMinigroup(null);
                    foreach($array as $value){
                        $d = $em->getRepository('AppBundle\Entity\Minigroup')->findOneBy( array( 'id'=> $value) );
                        $em->remove($d);
                        $em->flush();
                    }
                    if (!empty($data[$localeName]['minigroup'])) {
                        //echo'<pre>';
                        $array=array();
                        foreach($data[$localeName]['minigroup'] as $key=>$value){
                            if(strpos($key,'new')!==FALSE){
                                if(!empty($value['start_date']) && !empty($value['end_date'] && $value['start_date']!='')) {
                                    $start = explode(" ", $value['start_date']);
                                    $start[0] = explode(".", $start[0]);
                                    $start[1] = str_replace(":", "", $start[1]);
                                    $start = $start[0][2] . $start[0][1] . $start[0][0] . $start[1];
                                    $end = explode(" ", $value['end_date']);
                                    $end[0] = explode(".", $end[0]);
                                    $end[1] = str_replace(":", "", $end[1]);
                                    $end = $end[0][2] . $end[0][1] . $end[0][0] . $end[1];
                                    $price_euro = intval($value['price_euro']);
                                    $price_rub = intval($value['price_rub']);
                                    $minigroup = new Minigroup();
                                    $minigroup->setStart($start);
                                    $minigroup->setEnd($end);
                                    $minigroup->setPrixEur($price_euro);
                                    $minigroup->setPrixRub($price_rub);
                                    $array[] = $minigroup;
                                    $em->persist($minigroup);
                                }
                            }elseif(!empty($value['start_date']) && !empty($value['end_date'] && $value['start_date']!='')){
                                $start = explode(" ", $value['start_date']);
                                $start[0]=explode(".", $start[0]);
                                $start[1]=str_replace(":", "", $start[1]);
                                $start = $start[0][2].$start[0][1].$start[0][0].$start[1];
                                $end = explode(" ", $value['end_date']);
                                $end[0]=explode(".", $end[0]);
                                $end[1]=str_replace(":", "", $end[1]);
                                $end = $end[0][2].$end[0][1].$end[0][0].$end[1];
                                $price_euro = intval($value['price_euro']);
                                $price_rub = intval($value['price_rub']);
                                $minigroup = new Minigroup();
                                $minigroup->setStart($start);
                                $minigroup->setEnd($end);
                                $minigroup->setPrixEur($price_euro);
                                $minigroup->setPrixRub($price_rub);
                                $array[]=$minigroup;
                                $em->persist($minigroup);
                            }
                        }
                        $entity->setExtensionMinigroup($array);
                    }
                }

                $entity->mergeNewTranslations();
            }
            //$entity->setTarifs($tarif);
            /* END BLOCK FOR NON TRANSLATED VALUES*/

        }
		$em->persist($entity);
		$em->flush();
        return true;
    }
}
