<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\Event;
use AppBundle\Entity\EventToDate;
use AppBundle\Entity\EventToDay;
use AppBundle\Entity\EventToRelatedProduct;
use AppBundle\Entity\Minigroup;
use AppBundle\Service\Locales;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


/**
 * @Route("/event")
 */
class EventController extends Controller
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
     * @Route("/", name="admin_event")
     */
    public function indexAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

//		$data['data_list'] = $em->getRepository('AppBundle\Entity\Event')->findBy(array(), array('id' => 'DESC'));
		$data['data_list'] = $em->getRepository('AppBundle\Entity\Event')->findAllForAdminList($em,'AppBundle\Entity\Event',$request);


		$data['data_fields'] = array('id','image','name','active_lang');
		$data['data_title'] = 'adm.event';

		$link = $this->generateUrl('admin_event_add', array(), UrlGeneratorInterface::ABSOLUTE_PATH);


		$data['data_buttons'] = array(
			array(
				'name' => 'add',
				'link' => $link,
				'class' => 'primary'
			),
			array(
				'name' => 'reset_filters',
				'link' => $this->generateUrl('admin_event'),
				'class' => 'default'
			)
		);

		$data['data_actions'] = array(
            array(
                'name' => 'edit',
                'link' => 'admin_event_edit',
                'class' => '',
                'confirm' => false
            ),
            array(
                'name' => 'delete',
                'link' => 'admin_event_delete',
                'class' => 'danger',
                'confirm' => 'adm.action.delete.confirm'
            ),
		);

		$page = !empty($request->query->get('page')) ? intval($request->query->get('page')) : 1;
		$paginator = array(
			'currentFilters' => $request->query->all(),
			'currentPage' => $page,
			'paginationPath' => 'admin_event',
			'showAlwaysFirstAndLast' => true,
			'lastPage' => ceil(count($data['data_list']) / $this->_itemsOnPage),
		);

		$data['data_list'] = array_slice($data['data_list'], ($page * $this->_itemsOnPage - $this->_itemsOnPage), $this->_itemsOnPage);
		$data = array_merge($data,$paginator);

        return $this->render('AdminBundle:Default:list.html.twig',$data);
    }

    /**
     * @Route("/add", name="admin_event_add")
     * @Route("/edit/{id}", defaults={"id" = 0}, name="admin_event_edit")
     */
    public function editAction($id = 0, Request $request){
        $em = $this->getDoctrine()->getManager();



        $entity = new Event();
		$entity->setDefaultLocale($this->_defaultLocale);
		if (!empty($id)) {
			$entity = $em->getRepository('AppBundle\Entity\Event')->findOneBy(array('id'=>$id));
		}

        $data['entity'] = $entity;

		//TITLE FOR PAGE
		$data['data_title'] = (!empty($data['entity']->translate()->getName()) ? $data['entity']->translate()->getName() : $this->get('translator')->trans('adm.action.new'));

        $data['data_type'] =  'adm.visit.visit_name';

        $data['data_buttons'] = array(
            array(
                'name' => 'save',
                'link' => 'admin_event_edit',
                'class' => 'primary',
                'button' => true,
                'button_type' => 'submit'
            ),
            array(
                'name' => 'cancel',
                'link' => !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->generateUrl('admin_event', array(), UrlGeneratorInterface::ABSOLUTE_PATH),
                'class' => 'default',
            )
        );

        if ($request->isMethod('POST')) {

            $data = $request->request->all();

            $this->saveEntity($data,$entity,$em);
            return $this->redirectToRoute('admin_event', array());
        }

        $data['form'] = $this->createEntityForm($entity,$id,$em);


        return $this->render('AdminBundle:Default:form.html.twig',$data);
    }

    /**
     * @Route("/delete/{id}", name="admin_event_delete")
     */
    public function deleteAction($id){
        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository('AppBundle\Entity\Event')->findOneBy(array('id' => intval($id)));
        if ($data) {
            $em->remove($data);
            $em->flush();
        }


        return !empty($_SERVER['HTTP_REFERER']) ? $this->redirect($_SERVER['HTTP_REFERER']) : $this->generateUrl('admin_event', UrlGeneratorInterface::ABSOLUTE_PATH);

    }

	private function createEntityForm($entity, $id = 0, $em) {
		if (!empty($id)) {
			$form['action'] = $this->generateUrl('admin_event_edit', array('id' => $id), UrlGeneratorInterface::ABSOLUTE_PATH);
		} else{
			$form['action'] = $this->generateUrl('admin_event_add', array(), UrlGeneratorInterface::ABSOLUTE_PATH);
		}

		$form['id'] = $id;
		$form['form_id'] = 'form_'.md5('event');
		$form['separator'] = true;


        $otherImages = $entity->getImageOther() ? $entity->getImageOther() : array();
        $otherImagesValue = array();
        foreach($otherImages as $i) {
            $otherImagesValue[] = array(
                'path' => $i->getUrl(),
                'value' => $i->getId(),
            );
        }
		foreach($this->_locales as $lng) {
            $EventType=array();
            $data=$em->getRepository('AppBundle\Entity\BookTypeEvent')->findAll();
            foreach($data as $value){
                $EventType[$value->getId()]=$value->translate($lng,($lng == $this->_defaultLocale ? true : false))->getName();
            }
			$fields = array(
				'name' => array(
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
                'date_start' => array(
                    'label' => 'adm.field.date_start',
                    'type' => 'date',
                    'name' => $lng.'[date_start]',
                    'required' => true,
                    'value' => $entity ? $entity->getStart() : '',
                    'translate' => false
                ),
                'date_end' => array(
                    'label' => 'adm.field.date_end',
                    'type' => 'date',
                    'name' => $lng.'[date_end]',
                    'required' => false,
                    'value' => $entity ? $entity->getEnd() : '',
                    'translate' => false
                ),
                'date_repeat' => array(
                    'label' => 'adm.field.date_repeat',
                    'type' => 'checkbox',
                    'name' => $lng.'[date_repeat]',
                    'value' => 1,
                    'checked' => $entity ? $entity->isRepeat()  : 0,
                    'translate' => false
                ),
                'exclude_dates' => array(
                    'label' => 'adm.field.exclude_dates',
                    'type' => 'date',
                    'name' => $lng.'[exclude_dates][]',
                    'required' => false,
                    //'value' => $entity ? $entity->getExcludeDatesList() : '',
                    'value' => '',
                    'showTime' => 0,
                    'translate' => false
                ),
                'include_dates' => array(
                    'label' => 'adm.field.include_dates',
                    'type' => 'date',
                    'name' => $lng.'[include_dates][]',
                    'required' => false,
                    //'value' => $entity ? $entity->getIncludeDatesList() : '',
                    'value' => '',
                    'showTime' => 0,
                    'translate' => false
                ),
                'email' => array(
                    'label' => 'adm.field.email',
                    'type' => 'text',
                    'name' => $lng.'[email]',
                    'required' => false,
                    'value' => $entity ? $entity->getEmail() : '',
                    'translate' => false
                ),
                'event_type' => array(
                    'label' => 'adm.field.event_type',
                    'type' => 'select',
                    'name' => $lng.'[event_type]',
                    'required' => false,
                    'value' => $entity->getEventType() ? $entity->getEventType()->getId() : null,
                    'value_default' => 'adm.field.select.toplevel',
                    'value_zero' => FALSE,
                    'values' => $EventType,
                    'translate' => false
                ),
                'show_times' => array(
                    'label' => 'adm.field.show_times',
                    'type' => 'text',
                    'name' => $lng.'[show_times]',
                    'required' => false,
                    'value' => $entity ? $entity->translate($lng,($lng == $this->_defaultLocale ? true : false))->getShowTimes() : '',
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
                'introduction' => array(
                    'label' => 'adm.field.introduction',
                    'type' => 'texteditor',
                    'name' => $lng.'[introduction]',
                    'required' => false,
                    'value' => $entity ? $entity->translate($lng,($lng == $this->_defaultLocale ? true : false))->getIntroduction() : '',
                    'translate' => true
                ),
                'body_summary' => array(
                    'label' => 'adm.field.body_summary',
                    'type' => 'texteditor',
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

                'slug' => array(
                    'label' => 'adm.field.slug',
                    'type' => 'text',
                    'name' => $lng.'[slug]',
                    'required' => false,
                    'value' => $entity ? $entity->translate($lng,($lng == $this->_defaultLocale ? true : false))->getSlug() : '',
                    'translate' => true
                ),

				'separator_domain' => array(
					'label' => 'adm.field.domains',
					'type' => 'separator',
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

                'separator_days' => array(
                    'label' => 'adm.field.days',
                    'type' => 'separator',
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

				'related_product' => array(
					'label' => 'adm.field.related_product',
					'type' => 'relation_one_entity',
					'autocomplete' => 'extension',
					'autocomplete_path' => 'admin_autocomplete_related_product',
					'name' => $lng.'[related_product]',
					'add' => 'admin_extension_add',
					'field_rel' => array('id','entity','name'),
					'values' => $entity->getRelatedProduct() ? $entity->getRelatedProduct() : array(),
					'translate' => false,
					'editLink' => array(
						'type' =>  $entity->getRelatedProduct() ? $entity->getRelatedProduct()->getClass() : '',
						'path' => $entity->getRelatedProduct() ? array('id' => $entity->getRelatedProduct()->getId()) : array(),
					),
				),
				'destination' => array(
					'label' => 'adm.field.destination',
					'type' => 'relation_many',
					'autocomplete' => 'destination',
					'autocomplete_path' => 'admin_autocomplete_destination',
					'name' => $lng.'[destination][]',
					'add' => 'admin_destination_add',
					'field_rel' => array('id','name'),
					'values' => $entity->getDestination() ? $entity->getDestination() : array(),
					'sortable' => false,
					'translate' => false,
					'editLink' => array(
						'type' => 'destination',
						'path' => array(),
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
                'service_details' => array(
                    'label' => 'adm.field.service_details',
                    'type' => 'text',
                    'name' => $lng.'[service_details]',
                    'required' => false,
                    'value' => $entity ? $entity->translate($lng,($lng == $this->_defaultLocale ? true : false))->getServiceDetails() : '',
                    'translate' => true
                ),
                'price_displayed' => array(
                    'label' => 'adm.field.price_displayed',
                    'type' => 'text',
                    'name' => $lng.'[price_displayed]',
                    'required' => false,
                    'value' => $entity ? $entity->getPriceDisplayed() : '',
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
                    'values' => $entity->getEventMinigroup() ? $entity->getEventMinigroup() : array(),
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
                if (!empty($data[$localeName]['name'])) {
                    $entity->translate($localeName, false)->setActive(!empty($data[$localeName]['active']) ? 1 : 0);
                }
                if (!empty($data[$localeName]['body_summary'])) {
                    $entity->translate($localeName,false)->setBodySummary(trim($data[$localeName]['body_summary']));
                }
                if (!empty($data[$localeName]['body'])) {
                    $entity->translate($localeName,false)->setBody(trim($data[$localeName]['body']));
                }
                if (!empty($data[$localeName]['introduction'])) {
                    $entity->translate($localeName,false)->setIntroduction(trim($data[$localeName]['introduction']));
                }
                if (!empty($data[$localeName]['headline_liste'])) {
                    $entity->translate($localeName,false)->setHeadlineListe(trim($data[$localeName]['headline_liste']));
                }
                if (!empty($data[$localeName]['show_times'])) {
                    $entity->translate($localeName,false)->setShowTimes(trim($data[$localeName]['show_times']));
                }
                /* TARIFS */
                if (!empty($data[$localeName]['text_under_price'])) {
                    $entity->translate($localeName,false)->setTextUnderPrice(trim($data[$localeName]['text_under_price']));
                }
                if (!empty($data[$localeName]['service_details'])) {
                    $entity->translate($localeName,false)->setServiceDetails(trim($data[$localeName]['service_details']));
                }
                if (!empty($data[$localeName]['conditions_sale'])) {
                    $entity->translate($localeName,false)->setConditionsSale(trim($data[$localeName]['conditions_sale']));
                }
                /* END TARIFS */
                if (!empty($data[$localeName]['slug'])) {
                    $entity->translate($localeName,false)->setSlug($data[$localeName]['slug']);
                }
				/* BLOCK FOR NON TRANSLATED VALUES*/
				if ($localeName == $this->_defaultLocale) {
                    /**
                     * DATE
                     */
                    if (!empty($data[$localeName]['date_start'])) {
                        $start = explode(" ", $data[$localeName]['date_start']);
                        $start[0] = explode(".", $start[0]);
                        $start[1] = str_replace(":", "", $start[1]);
                        $start = $start[0][2] . $start[0][1] . $start[0][0] . $start[1];

                        $entity->setStart(!empty($data[$localeName]['date_start']) ? $start : '');
                    }
                    if (!empty($data[$localeName]['date_end'])) {
                        $end = explode(" ", $data[$localeName]['date_end']);
                        $end[0] = explode(".", $end[0]);
                        $end[1] = str_replace(":", "", $end[1]);
                        $end = $end[0][2] . $end[0][1] . $end[0][0] . $end[1];
                        $entity->setEnd(!empty($data[$localeName]['date_end']) ? $end : '');
                    }else{
                        $end=NULL;
                        $entity->setEnd('');
                    }
                    //  Удалить все события из связанной таблицы
                    $event = $em->getRepository('AppBundle\Entity\EventToDate')->findBy( array( 'event'=> $entity) );
                    if($event){
                        foreach ($event as $value) {
                            $em->remove($value);
                        }
                        $em->flush();
                    }
                    $entity->setEventSchedule(null);
                    $schedule_array=array();
                    //  В любом случае создать текущее событие
                    $date = new EventToDate();
                    $date->setEvent($entity);
                    $date->setDateStart($start);
                    $date->setDateStop($end);
                    $em->persist($date);
                    $schedule_array[]=$date;
                    $entity->setRepeat(!empty($data[$localeName]['date_repeat']) ? 1 : 0);
                    if (!empty($data[$localeName]['date_repeat'])) {

                        /*if($data[$localeName]['date_repeat']==1){
                            //  сдесь мы разворачиваем даты в отдельную таблицу.
                            $date = new EventToDate();

                        }else{
                            //  Удалить все связи
                            $entity->setExcludeDatesList(null);
                            $entity->setIncludeDatesList(null);
                        }*/
                    }
                    $entity->setEventSchedule($schedule_array);
                    /**
                     * END DATE
                     */
                    if (!empty($data[$localeName]['event_type'])) {
                        $event_type = $em->getRepository('AppBundle\Entity\BookTypeEvent')->findOneBy(array('id' => $data[$localeName]['event_type']));
                        if ($event_type) {
                            $entity->setEventType($event_type);
                        } else {
                            $entity->setEventType(null);
                        }
                    }else {
                        $entity->setEventType(null);
                    }
                    /* TARIFS */
                    $entity->setPriceFlexibility(!empty($data[$localeName]['price_flexibility']) ? 1 : 0);
                    if (!empty($data[$localeName]['price_displayed'])) {
                        $entity->setPriceDisplayed(!empty($data[$localeName]['price_displayed']) ? $data[$localeName]['price_displayed'] : '');
                    }
                    /* END TARIFS */
                    if (!empty($data[$localeName]['destination'])) {
                        $destination = $em->getRepository('AppBundle\Entity\Destination')->findBy( array( 'id'=> $data[$localeName]['destination']) );
                        $entity->setDestination($destination);
                    } else {
                        $entity->setDestination(null);
                    }
                    if (!empty($data[$localeName]['email'])) {
                        $entity->setEmail(!empty($data[$localeName]['email']) ? $data[$localeName]['email'] : '');
                    }
                    if (!empty($data[$localeName]['extension'])) {
                        $destination = $em->getRepository('AppBundle\Entity\Extension')->findOneBy( array( 'id'=> $data[$localeName]['extension']) );
                        $entity->setExtension($destination);
                    } else {
                        $entity->setExtension(null);
                    }
                    if (!empty($data[$localeName]['location'])) {
                        $location = $em->getRepository('AppBundle\Entity\Location')->findOneBy( array( 'id'=> $data[$localeName]['location']) );
                        $entity->setLocation($location);
                    } else {
                        $entity->setLocation(null);
                    }
                    $event = $em->getRepository('AppBundle\Entity\EventToRelatedProduct')->findOneBy( array( 'event'=> $entity) );
                    if($event){
                        $em->remove($event);
                        $em->flush();
                    }
                    $entity->setRelatedProduct(null);
                    //print_r($data[$localeName]['related_product']); exit;
                    if (!empty($data[$localeName]['related_product'])) {
                        $class = 'AppBundle\Entity\\'.$data[$localeName]['related_product']['entity'];
                        $atribute_array=array('Voyage','Extension','Visit');
                        $eclass = $em->getRepository($class)->findOneBy( array( 'id'=> $data[$localeName]['related_product']['id']) );
                        $RelatedProduct = new EventToRelatedProduct();
                        $RelatedProduct->setEvent($entity);
                        foreach($atribute_array as $value){
                            $method = 'set'.$value;
                            if ($value==$data[$localeName]['related_product']['entity']){
                                $RelatedProduct->$method($eclass);
                            }else{
                                $RelatedProduct->$method(NULL);
                            }
                        }
                        $em->persist($RelatedProduct);
                        $entity->setRelatedProduct($RelatedProduct);
                    }else{
                        $entity->setRelatedProduct(null);
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
                    //$entity->setImageOthers(array());
                    if (!empty($data[$localeName]['image_others'])) {
                        $otherImages = array_filter($data[$localeName]['image_others'], function($value) { return $value !== ''; });
                        $otherImage = $em->getRepository('AppBundle\Entity\Files')->findBy( array( 'id'=> $otherImages ) );
                        $entity->setImageOthers($otherImage);
                    } else {
                        $entity->setImageOthers(array());
                    }
                    /* END IMAGE */

                    $day = $em->getRepository('AppBundle\Entity\EventToDay')->findBy( array( 'event'=> $entity) );
                    foreach($day as $value) {
                        $em->remove($value);
                    }
                    if (!empty($data[$localeName]['day'])) {
                        $array=array();
                        foreach($data[$localeName]['day'] as $key=>$value){
                            $day = $em->getRepository('AppBundle\Entity\Day')->findOneBy( array( 'id'=> $value) );
                            if($day){
                                $toDay = new EventToDay();
                                $toDay->setDay($day);
                                $toDay->setEvent($entity);
                                $toDay->setPosition($key);
                                $em->persist($toDay);
                                $array[]=$toDay;
                            }
                        }

                        $entity->setDay($array);
                    } else {
                        $entity->setDay(array());
                    }

                    /**
                     * MINIGROUP
                     */

					$entity->setFavorite(!empty($data[$localeName]['favorite']) ? 1 : 0);


                    //echo'<pre>'; print_r($data[$localeName]); //exit;
                    $entity->setMiniGroupe(!empty($data[$localeName]['mini_groupe']) ? 1 : 0);
                    //print_r($entity->getMiniGroupe()); exit;
                    if (!empty($data[$localeName]['minigroup_prix_euros'])) {
                        $entity->setMinigroupPrixEuros(intval($data[$localeName]['minigroup_prix_euros']));
                    }
                    if (!empty($data[$localeName]['minigroup_prix_rubles'])) {
                        $entity->setMinigroupPrixRubles(intval($data[$localeName]['minigroup_prix_rubles']));
                    }
                    //var_dump($entity->getEventMinigroup()->toArray()); exit;
                    $array=array();
                    if($entity->getEventMinigroup()->toArray()) {
                        foreach ($entity->getEventMinigroup()->toArray() as $value) {
                            $array[] = $value->getId();
                        }
                    }
                    if($array!=array()) {
                        $entity->setEventMinigroup(array());
                        foreach ($array as $value) {
                            $d = $em->getRepository('AppBundle\Entity\Minigroup')->findOneBy(array('id' => $value));
                            $em->remove($d);
                            $em->flush();
                        }
                    }
                    //print_r($data[$localeName]['minigroup']); exit;
                    if (!empty($data[$localeName]['minigroup'])) {
                        //echo'<pre>';
                        $array=array();
                        foreach($data[$localeName]['minigroup'] as $key=>$value){
                            if(strpos($key,'new')!==FALSE){ echo'123';
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
                        if($array!=array()) {
                            $entity->setEventMinigroup($array);
                        }
                    }
                    /**
                     * END MINIGROUP
                     */
                    /**
                     * CALENDAR SCHEDULE
                     */

                    /**
                     * END CALENDAR SCHEDULE
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
