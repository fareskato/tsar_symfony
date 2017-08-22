<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\Minigroup;
use AppBundle\Entity\Visit;
use AppBundle\Service\Locales;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class VisitController extends Controller
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
     * @Route("/visit", name="admin_visit")
     */
    public function indexAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

//		$data['data_list'] = $em->getRepository('AppBundle\Entity\Visit')->findBy(array(), array('id' => 'DESC'));
		$data['data_list'] = $em->getRepository('AppBundle\Entity\Visit')->findAllForAdminList($em,'AppBundle\Entity\Visit',$request);
		$data['data_fields'] = array('id','image','name','active_domain','active_lang');
		$data['data_title'] = 'adm.visit';

		$link = $this->generateUrl('admin_visit_add', array(), UrlGeneratorInterface::ABSOLUTE_PATH);


		$data['data_buttons'] = array(
			array(
				'name' => 'add',
				'link' => $link,
				'class' => 'primary'
			),
			array(
				'name' => 'reset_filters',
				'link' => $this->generateUrl('admin_visit'),
				'class' => 'default'
			)
		);

		$data['data_actions'] = array(
				array(
					'name' => 'edit',
					'link' => 'admin_visit_edit',
					'class' => '',
					'confirm' => false
				),
				array(
					'name' => 'delete',
					'link' => 'admin_visit_delete',
					'class' => 'danger',
					'confirm' => 'adm.action.delete.confirm'
				),
			);

		$page = !empty($request->query->get('page')) ? intval($request->query->get('page')) : 1;
		$paginator = array(
			'currentFilters' => $request->query->all(),
			'currentPage' => $page,
			'paginationPath' => 'admin_visit',
			'showAlwaysFirstAndLast' => true,
			'lastPage' => ceil(count($data['data_list']) / $this->_itemsOnPage),
		);

		$data['data_list'] = array_slice($data['data_list'], ($page * $this->_itemsOnPage - $this->_itemsOnPage), $this->_itemsOnPage);
		$data = array_merge($data,$paginator);

        return $this->render('AdminBundle:Default:list.html.twig',$data);
    }

    /**
     * @Route("/visit/add", name="admin_visit_add")
     * @Route("/visit/edit/{id}", defaults={"id" = 0}, name="admin_visit_edit")
     */
    public function editAction($id = 0, Request $request){
        $em = $this->getDoctrine()->getManager();



        $entity = new Visit();
		$entity->setDefaultLocale($this->_defaultLocale);
		if (!empty($id)) {
			$entity = $em->getRepository('AppBundle\Entity\Visit')->findOneBy(array('id'=>$id));
		}

        $data['entity'] = $entity;

		//TITLE FOR PAGE
		$data['data_title'] = (!empty($data['entity']->translate()->getName()) ? $data['entity']->translate()->getName() : $this->get('translator')->trans('adm.action.new'));

        $data['data_type'] =  'adm.visit.visit_name';

        $data['data_buttons'] = array(
            array(
                'name' => 'save',
                'link' => 'admin_visit_edit',
                'class' => 'primary',
                'button' => true,
                'button_type' => 'submit'
            ),
            array(
                'name' => 'cancel',
                'link' => !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->generateUrl('admin_visit', array(), UrlGeneratorInterface::ABSOLUTE_PATH),
                'class' => 'default',
            )
        );

        if ($request->isMethod('POST')) {

            $data = $request->request->all();

            $this->saveEntity($data,$entity,$em);
            return $this->redirectToRoute('admin_visit', array());
        }

        $data['form'] = $this->createEntityForm($entity,$id,$em);


        return $this->render('AdminBundle:Default:form.html.twig',$data);
    }

    /**
     * @Route("/visit/delete/{id}", name="admin_visit_delete")
     */
    public function deleteAction($id){
        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository('AppBundle\Entity\Visit')->findOneBy(array('id' => intval($id)));
        if ($data) {
            $em->remove($data);
            $em->flush();
        }


        return !empty($_SERVER['HTTP_REFERER']) ? $this->redirect($_SERVER['HTTP_REFERER']) : $this->generateUrl('admin_visit', UrlGeneratorInterface::ABSOLUTE_PATH);

    }

	private function createEntityForm($entity, $id = 0, $em) {
		if (!empty($id)) {
			$form['action'] = $this->generateUrl('admin_visit_edit', array('id' => $id), UrlGeneratorInterface::ABSOLUTE_PATH);
		} else{
			$form['action'] = $this->generateUrl('admin_visit_add', array(), UrlGeneratorInterface::ABSOLUTE_PATH);
		}

		$form['id'] = $id;
		$form['form_id'] = 'form_'.md5('visit');
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

        $season = $em->getRepository('AppBundle\Entity\BookSeason')->findAll();
        $season_values = array();
        foreach ($season as $season ) {
            $season_values[$season->getId()] = $season->translate()->getName();
        }
        $season_checed = array();
        if (!empty($entity->getSeason())) {
            foreach ($entity->getSeason() as $season) {
                $season_checed[] = $season->getId();
            }
        }

        $otherImages = $entity->getImageOther() ? $entity->getImageOther() : array();
        $otherImagesValue = array();
        foreach($otherImages as $i) {
            $otherImagesValue[] = array(
                'path' => $i->getUrl(),
                'value' => $i->getId(),
            );
        }


		foreach($this->_locales as $lng) {
            $VisitDurationValues=array();
            $data=$em->getRepository('AppBundle\Entity\BookDuration')->findAll();
            foreach($data as $value){
                $VisitDurationValues[$value->getId()]=$value->translate($lng,($lng == $this->_defaultLocale ? true : false))->getName();
            }
			$fields = array(
				'name' => array(/* Будем считать, что это исходное поле Title*/
					'label' => 'adm.field.name',
					'type' => 'text',
					'name' => $lng.'[name]',
					'required' => ($lng != $this->_defaultLocale ? false : true),
					'value' => $entity ? $entity->translate($lng,($lng == $this->_defaultLocale ? true : false))->getName() : '',
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
				'recreation' => array(
					'label' => 'adm.field.recreation',
					'type' => 'relation_many',
					'autocomplete' => 'destination',
					'autocomplete_path' => 'admin_autocomplete_recreation',
					'name' => $lng.'[recreation][]',
					'add' => 'admin_destination_add',
					'field_rel' => array('id','name'),
					'values' => $entity->getRecreation() ? $entity->getRecreation() : array(),
					'translate' => false,
					'editLink' => array(
						'type' => 'book',
						'path' => array('book_name' => 'book_type_recreation'),
					),
				),
				'visit_duration' => array(
					'label' => 'adm.field.visit_duration',
					'type' => 'select',
					'name' => $lng.'[visit_duration]',
					'required' => false,
					'value' => $entity->getVisitDuration() ? $entity->getVisitDuration()->getId() : null,
					'value_default' => 'adm.field.select.toplevel',
					'value_zero' => FALSE,
					'values' => $VisitDurationValues,
					'translate' => false
				),
				'number_hours_visit' => array(
					'label' => 'adm.field.number_hours_visit',
					'type' => 'text',
					'name' => $lng.'[number_hours_visit]',
					'required' => false,
					'value' => $entity ? $entity->getNumberHoursVisit() : 0,
					'translate' => false
				),
				'season' => array(
					'label' => 'adm.field.season',
					'type' => 'checkbox_multiple',
					'name' => $lng.'[season]',
					'values' => $season_values,
					'checked_values' => $season_checed, //only checked values array
					'translate' => false
				),
				'separator_location' => array(
					'label' => 'adm.field.location',
					'type' => 'separator',
					'translate' => false
				),
				'travel_points' => array(
					'label' => 'adm.field.travel_points',
					'type' => 'relation_many',
					'autocomplete' => 'destination',
					'autocomplete_path' => 'admin_autocomplete_destination',
					'name' => $lng.'[travel_points][]',
					'add' => 'admin_destination_add',
					'field_rel' => array('id','name'),
					'values' => $entity->getTravelPoints() ? $entity->getTravelPoints() : array(),
					'sortable' => false,
					'translate' => false,
					'editLink' => array(
						'type' => 'destination',
						'path' => array(),
					),
				),
				'ville' => array(
					'label' => 'adm.field.ville',
					'type' => 'relation_one',
					'autocomplete' => 'destination',
					'autocomplete_path' => 'admin_autocomplete_destination',
					'name' => $lng.'[ville]',
					'add' => 'admin_destination_add',
					'field_rel' => array('id','name'),
					'values' => $entity->getVille() ? $entity->getVille() : array(),
					'translate' => false,
					'editLink' => array(
						'type' => 'destination',
						'path' => $entity->getStartingPoint() ? array('id' => $entity->getStartingPoint()->getId()) : array(),
					),
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
				'image' => array(
					'label' => 'adm.field.image',
					'type' => 'image',
					'name' => $lng.'[image]',
					'path' => $entity->getImage() ? $entity->getImage()->getUrl() : false,
					'value' => $entity->getImage() ? $entity->getImage()->getId() : false,
					'required' => false,
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
					'path' => $entity->getImageMiniature() ? $entity->getImageMiniature()->getUrl() : false,
					'value' => $entity->getImageMiniature() ? $entity->getImageMiniature()->getId() : false,
					'required' => false,
					'translate' => false
				),
				'image_others' => array(
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
                    'values' => $entity->getVisitMinigroup() ? $entity->getVisitMinigroup() : array(),
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
                        $entity->setPriceDisplayed(!empty($data[$localeName]['price_displayed']) ? $data[$localeName]['price_displayed'] : '');
                    }
                    $entity->setPrixEuro(!empty($data[$localeName]['prix_euro']) ? intval($data[$localeName]['prix_euro']) : 0);
                    $entity->setPrixRouble(!empty($data[$localeName]['prix_rouble']) ? intval($data[$localeName]['prix_rouble']) : 0);
                    /* END TARIFS */
                    if (!empty($data[$localeName]['travel_points'])) {
                        $travel_points = $em->getRepository('AppBundle\Entity\Destination')->findBy( array( 'id'=> $data[$localeName]['travel_points']) );
                        $entity->setTravelPoints($travel_points);
                    } else {
                        $entity->setTravelPointse(null);
                    }
                    if (!empty($data[$localeName]['number_hours_visit'])) {
                        $entity->setNumberHoursVisit(trim($data[$localeName]['number_hours_visit']));
                    }
                    if (!empty($data[$localeName]['visit_duration'])) {
                        $visit_duration = $em->getRepository('AppBundle\Entity\BookDuration')->findOneBy(array('id' => $data[$localeName]['visit_duration']));
                        if ($visit_duration) {
                            $entity->setVisitDuration($visit_duration);
                        } else {
                            $entity->setVisitDuration(null);
                        }
                    }else {
                        $entity->setVisitDuration(null);
                    }
                    if (!empty($data[$localeName]['ville'])) {
                        $destination = $em->getRepository('AppBundle\Entity\Destination')->findOneBy( array( 'id'=> $data[$localeName]['ville']) );
                        $entity->setVille($destination);
                    } else {
                        $entity->setVille(null);
                    }
                    if (!empty($data[$localeName]['season'])) {
                        $selectedSeason = array();
                        foreach($data[$localeName]['season'] as $domain => $value){
                            if (!empty($value)) {
                                $selectedSeason[] = $domain;
                            }
                        }
                        $Season = $em->getRepository('AppBundle\Entity\BookSeason')->findBy( array( 'id'=> $selectedSeason) );
                        $entity->setSeason($Season);
                    } else {
                        $entity->setSeason(null);
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
                    if (!empty($data[$localeName]['recreation'])) {
                        $voyage = $em->getRepository('AppBundle\Entity\BookTypeRecreation')->findBy( array( 'id'=> $data[$localeName]['recreation']) );
                        $entity->setRecreation($voyage);
                    } else {
                        $entity->setRecreation(null);
                    }
                    // IMAGE
                    $image = $em->getRepository('AppBundle\Entity\Files')->findOneBy( array( 'id'=> $data[$localeName]['image']) );
                    if ($image) {
                        $entity->setImage($image);
                    } else {
                        $entity->setImage(null);
                    }

                    $imageMiniature = $em->getRepository('AppBundle\Entity\Files')->findOneBy( array( 'id'=> $data[$localeName]['image_miniature']) );
                    if ($imageMiniature) {
                        $entity->setImageMiniature($imageMiniature);
                    } else {
                        $entity->setImageMiniature(null);
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
                        $entity->setImageOthers($otherImage);
                    } else {
                        $entity->setImageOthers(null);
                    }
                    /* END IMAGE */

                    if (!empty($data[$localeName]['location'])) {
                        $location = $em->getRepository('AppBundle\Entity\Location')->findOneBy( array( 'id'=> $data[$localeName]['location']) );
                        $entity->setLocation($location);
                    } else {
                        $entity->setLocation(null);
                    }
                    /**
                     * MINIGROUP
                     */
					if (isset($data[$localeName]['favorite'])) {
						$entity->setFavorite(!empty($data[$localeName]['favorite']) ? 1 : 0);
					}

                    //echo'<pre>'; print_r($data[$localeName]); //exit;
                    $entity->setMiniGroupe(!empty($data[$localeName]['mini_groupe']) ? 1 : 0);
                    //print_r($entity->getMiniGroupe()); exit;
                    if (!empty($data[$localeName]['minigroup_prix_euros'])) {
                        $entity->setMinigroupPrixEuros(intval($data[$localeName]['minigroup_prix_euros']));
                    }
                    if (!empty($data[$localeName]['minigroup_prix_rubles'])) {
                        $entity->setMinigroupPrixRubles(intval($data[$localeName]['minigroup_prix_rubles']));
                    }
                    $array=array();
                    if($entity->getVisitMinigroup()) {
                        foreach ($entity->getVisitMinigroup()->toArray() as $value) {
                            $array[] = $value->getId();
                        }
                    }
                    $entity->setVisitMinigroup(null);
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
                        $entity->setVisitMinigroup($array);
                    }
                    /**
                     * END MINIGROUP
                     */
				}
                $entity->mergeNewTranslations();
            }
            $em->persist($entity);
            $em->flush();
        }

        return true;
    }
}
