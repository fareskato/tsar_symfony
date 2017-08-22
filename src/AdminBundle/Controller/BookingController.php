<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\Booking;
use AppBundle\Entity\BookingToRelatedProduct;
use AppBundle\Service\Locales;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BookingController extends Controller {

  private $_itemsOnPage = 20;

  private $_locales;

  private $_defaultLocale;

  public function __construct() {
    $loc = new Locales();
    $this->_locales = $loc->getLocales();
    $this->_defaultLocale = $loc->getDefaultLocale();
  }

  /**
   * @Route("/booking/", name="admin_booking")
   */
  public function indexAction(Request $request) {

    $em = $this->getDoctrine()->getManager();

//    $data['data_list'] = $em->getRepository('AppBundle\Entity\Booking')
//      ->findBy([], ['id' => 'DESC']);
	  $data['data_list'] = $em->getRepository('AppBundle\Entity\Booking')->findAllForAdminList($em,'AppBundle\Entity\Booking',$request);


	  $data['data_fields'] = ['id', 'name'];
    $data['data_title'] = 'adm.booking';

    $link = $this->generateUrl('admin_booking_add', [], UrlGeneratorInterface::ABSOLUTE_PATH);


    $data['data_buttons'] = [
      [
        'name' => 'add',
        'link' => $link,
        'class' => 'primary',
      ],
    ];

    $data['data_actions'] = [
      [
        'name' => 'edit',
        'link' => 'admin_booking_edit',
        'class' => '',
        'confirm' => FALSE,
      ],
      [
        'name' => 'delete',
        'link' => 'admin_booking_delete',
        'class' => 'danger',
        'confirm' => 'adm.action.delete.confirm',
      ],
    ];

    $page = !empty($request->query->get('page')) ? intval($request->query->get('page')) : 1;
    $paginator = [
		'currentFilters' => $request->query->all(),
		'currentPage' => $page,
      'paginationPath' => 'admin_booking',
      'showAlwaysFirstAndLast' => TRUE,
      'lastPage' => ceil(count($data['data_list']) / $this->_itemsOnPage),
    ];

    $data['data_list'] = array_slice($data['data_list'], ($page * $this->_itemsOnPage - $this->_itemsOnPage), $this->_itemsOnPage);
    $data = array_merge($data, $paginator);

    return $this->render('AdminBundle:Default:list.html.twig', $data);
  }

  /**
   * @Route("/booking/add", name="admin_booking_add")
   * @Route("/booking/edit/{id}", defaults={"id" = 0},
   *   name="admin_booking_edit")
   */
  public function editAction($id = 0, Request $request) {
    $em = $this->getDoctrine()->getManager();

    $entity = new Booking();
    $entity->setDefaultLocale($this->_defaultLocale);
    if (!empty($id)) {
      $entity = $em->getRepository('AppBundle\Entity\Booking')
        ->findOneBy(['id' => $id]);
    }//var_dump($entity->isActive()); exit;
    $data['entity'] = $entity;
    //print_r(get_class_methods($data['entity']->translate())); exit;
    //TITLE FOR PAGE
    $data['data_title'] = $data['entity']->getName();

    $data['data_type'] = 'adm.booking';

    $data['data_buttons'] = [
      [
        'name' => 'save',
        'link' => 'admin_booking_edit',
        'class' => 'primary',
        'button' => TRUE,
        'button_type' => 'submit',
      ],
      [
        'name' => 'cancel',
        'link' => !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->generateUrl('admin_booking', [], UrlGeneratorInterface::ABSOLUTE_PATH),
        'class' => 'default',
      ],
    ];

    if ($request->isMethod('POST')) {

      $data = $request->request->all();

      $this->saveEntity($data, $entity, $em);
      return $this->redirectToRoute('admin_booking', []);
    }

    $data['form'] = $this->createEntityForm($entity, $id, $em);

    return $this->render('AdminBundle:Default:form.html.twig', $data);
  }

  /**
   * @Route("/booking/delete/{id}", name="admin_booking_delete")
   */
  public function deleteAction($id) {
    $em = $this->getDoctrine()->getManager();

    $data = $em->getRepository('AppBundle\Entity\Booking')
      ->findOneBy(['id' => intval($id)]);
    if ($data) {
      $em->remove($data);
      $em->flush();
    }


    return !empty($_SERVER['HTTP_REFERER']) ? $this->redirect($_SERVER['HTTP_REFERER']) : $this->generateUrl('admin_booking', UrlGeneratorInterface::ABSOLUTE_PATH);

  }

  private function createEntityForm($entity, $id = 0, $em) {
    if (!empty($id)) {
      $form['action'] = $this->generateUrl('admin_booking_edit', ['id' => $id], UrlGeneratorInterface::ABSOLUTE_PATH);
    }
    else {
      $form['action'] = $this->generateUrl('admin_booking_add', [], UrlGeneratorInterface::ABSOLUTE_PATH);
    }
    $form['id'] = $id;
    $form['form_id'] = 'form_' . md5('booking');
    $form['separator'] = TRUE;

    $otherImages = $entity->getImageOther() ? $entity->getImageOther() : [];
    $otherImagesValue = [];
    foreach ($otherImages as $i) {
      $otherImagesValue[] = [
        'path' => $i->getUrl(),
        'value' => $i->getId(),
      ];
    }
    $domains = $em->getRepository('AppBundle\Entity\BookDomain')->findAll();
    $domainValues = [];
    foreach ($domains as $domain) {
      $domainValues[$domain->getId()] = $domain->translate()->getName();
    }
    $domainCheckedValues = [];
    if (!empty($entity->getTypeDomain())) {
      foreach ($entity->getTypeDomain() as $domain) {
        $domainCheckedValues[] = $domain->getId();
      }
    }


    $booking_season = $em->getRepository('AppBundle\Entity\BookSeason')
      ->findAll();
    $booking_season_values = [];
    foreach ($booking_season as $season) {
      $booking_season_values[$season->getId()] = $season->translate()
        ->getName();
    }
    $booking_season_checed = [];
    if (!empty($entity->getSeason())) {
      foreach ($entity->getSeason() as $season) {
        $booking_season_checed[] = $season->getId();
      }
    }

    foreach ($this->_locales as $lng) {

      $assigned_user = [];
      $users = $em->getRepository('AppBundle\Entity\User')->findAll();
      foreach ($users as $auser) {
        $assigned_user[$auser->getId()] = $auser->getUsername();
      }

      $supp_service = [];
      $supp_services = $em->getRepository('AppBundle\Entity\BookSuppServices')
        ->findAll();
      foreach ($supp_services as $asupp_service) {
        $supp_service[$asupp_service->getId()] = $asupp_service->translate()
          ->getName();
      }

      $civilite = [];
      $civilites = $em->getRepository('AppBundle\Entity\BookCivilite')
        ->findAll();
      foreach ($civilites as $acivilite) {
        $civilite[$acivilite->getId()] = $acivilite->translate()->getName();
      }

      $offer_options = [];
      $offer_options_options = $em->getRepository('AppBundle\Entity\BookOfferOptions')
        ->findAll();
      foreach ($offer_options_options as $offer_option) {
        $offer_options[$offer_option->getId()] = $offer_option->translate()
          ->getName();
      }

      $send_docs = [];
      $send_docs_options = $em->getRepository('AppBundle\Entity\BookSendDocs')
        ->findAll();
      foreach ($send_docs_options as $send_doc) {
        $send_docs[$send_doc->getId()] = $send_doc->translate()->getName();
      }


      $stars = $em->getRepository('AppBundle\Entity\BookHotelStars')->findAll();
      $starsValues = [];
      foreach ($stars as $star) {
        $starsValues[$star->getId()] = $star->translate()->getName();
      }
      $starsCheckedValues = [];
      if (!empty($entity->getHotelStars())) {
        foreach ($entity->getHotelStars() as $star) {
          $starsCheckedValues[] = $star->getId();
        }
      }

      $fields = [
        'name' => [
          'label' => 'adm.field.title',
          'type' => 'text',
          'name' => $lng . '[name]',
          'required' => ($lng != $this->_defaultLocale ? FALSE : TRUE),
          'value' => $entity ? $entity->getName() : '',
          'translate' => FALSE,
        ],
        'booked_product' => [
          'label' => 'adm.field.booked_product',
          'type' => 'relation_one_entity',
          'autocomplete' => 'extension',
          'autocomplete_path' => 'admin_autocomplete_booked_product',
          'name' => $lng . '[booked_product]',
          'add' => 'admin_extension_add',
          'field_rel' => ['id', 'entity', 'name'],
          'values' => $entity->getBookedProduct() ? $entity->getBookedProduct() : [],
          'translate' => FALSE,
          'editLink' => [
            'type' => $entity->getBookedProduct() ? $entity->getBookedProduct()
              ->getClass() : '',
            'path' => $entity->getBookedProduct() ? [
              'id' => $entity->getBookedProduct()
                ->getId(),
            ] : [],
          ],
        ],
        'assigned_user' => [
          'label' => 'adm.field.assigned_user',
          'type' => 'select',
          'name' => $lng . '[assigned_user]',
          'required' => FALSE,
          'value' => $entity->getAssignedUser() ? $entity->getAssignedUser()
            ->getId() : NULL,
          'value_zero' => FALSE,
          'values' => $assigned_user,
          'translate' => FALSE,
        ],
        'supp_service' => [
          'label' => 'adm.field.supp_service',
          'type' => 'radio',
          'name' => $lng . '[supp_service]',
          'values' => $supp_service,
          'value_zero' => TRUE,
          'checked_value' => $entity->getEtiquette() ? $entity->getEtiquette()
            ->getId() : FALSE,
          'translate' => FALSE,
        ],
        'offer_options' => [
          'label' => 'adm.field.offer_options',
          'type' => 'select',
          'name' => $lng . '[offer_options]',
          'required' => FALSE,
          'value' => $entity->getOfferOptions() ? $entity->getOfferOptions()
            ->getId() : NULL,
          'value_zero' => FALSE,
          'values' => $offer_options,
          'translate' => FALSE,
        ],
        'send_docs' => [
          'label' => 'adm.field.send_docs',
          'type' => 'select',
          'name' => $lng . '[send_docs]',
          'required' => FALSE,
          'value' => $entity->getSendDocs() ? $entity->getSendDocs()
            ->getId() : NULL,
          'value_zero' => TRUE,
          'value_default' => $this->get('translator')
            ->trans('adm.field.do_nothing'),
          'values' => $send_docs,
          'translate' => FALSE,
        ],
        'hotel_stars' => [
          'label' => 'adm.field.hotel_stars',
          'type' => 'checkbox_multiple',
          'name' => $lng . '[hotel_stars]',
          'values' => $starsValues,
          'checked_values' => $starsCheckedValues, //only checked values array
          'translate' => FALSE,
        ],
        'key' => [
          'label' => 'adm.field.key',
          'type' => 'text',
          'name' => $lng . '[key]',
          'required' => FALSE,
          'value' => $entity ? $entity->getKey() : '',
          'translate' => FALSE,
        ],
        'website_version' => [
          'label' => 'adm.field.website_version',
          'type' => 'text',
          'name' => $lng . '[website_version]',
          'required' => FALSE,
          'value' => $entity ? $entity->getWebsiteVersion() : '',
          'translate' => FALSE,
        ],
        'comment' => [
          'label' => 'adm.field.comment',
          'type' => 'text',
          'name' => $lng . '[comment]',
          'required' => FALSE,
          'value' => $entity ? $entity->getComment() : '',
          'translate' => FALSE,
        ],
        'devis_booking_id' => [
          'label' => 'adm.field.devis_booking_id',
          'type' => 'text',
          'name' => $lng . '[devis_booking_id]',
          'required' => FALSE,
          'value' => $entity ? $entity->getDevisBookingId() : '',
          'translate' => FALSE,
        ],
        'excel_link' => [
          'label' => 'adm.field.excel_link',
          'type' => 'text',
          'name' => $lng . '[excel_link]',
          'required' => FALSE,
          'value' => $entity ? $entity->getExcelLink() : '',
          'translate' => FALSE,
        ],

        'separator_booking_details' => [
          'label' => 'adm.field.separator_booking_details',
          'type' => 'separator',
          'translate' => FALSE,
        ],
        'hotel' => [
          'label' => 'adm.field.hotel',
          'type' => 'text',
          'name' => $lng . '[hotel]',
          'required' => FALSE,
          'value' => $entity ? $entity->getHotel() : '',
          'translate' => FALSE,
        ],
        'amount_of_people' => [
          'label' => 'adm.field.amount_of_people',
          'type' => 'text',
          'name' => $lng . '[amount_of_people]',
          'required' => FALSE,
          'value' => $entity ? $entity->getAmountOfPeople() : '',
          'translate' => FALSE,
        ],
        'date' => [
          'label' => 'adm.field.date',
          'type' => 'date',
          'name' => $lng . '[date]',
          'required' => FALSE,
          'value' => $entity ? $entity->getDate() : '',
          'translate' => FALSE,
        ],
        'suppliment_single' => [
          'label' => 'adm.field.suppliment_single',
          'type' => 'text',
          'name' => $lng . '[suppliment_single]',
          'required' => FALSE,
          'value' => $entity ? $entity->getSupplimentSingle() : '',
          'translate' => FALSE,
        ],
        'numbre_de_jours' => [
          'label' => 'adm.field.numbre_de_jours',
          'type' => 'text',
          'name' => $lng . '[numbre_de_jours]',
          'required' => FALSE,
          'value' => $entity ? $entity->getNumbreDeJours() : '',
          'translate' => FALSE,
        ],
        'numbre_de_nuits' => [
          'label' => 'adm.field.numbre_de_nuits',
          'type' => 'text',
          'name' => $lng . '[numbre_de_nuits]',
          'required' => FALSE,
          'value' => $entity ? $entity->getNumbreDeNuits() : '',
          'translate' => FALSE,
        ],
        'numbre_de_chambers' => [
          'label' => 'adm.field.numbre_de_chambers',
          'type' => 'text',
          'name' => $lng . '[numbre_de_chambers]',
          'required' => FALSE,
          'value' => $entity ? $entity->getNumbreDeChambers() : '',
          'translate' => FALSE,
        ],
        'pdf_link' => [
          'label' => 'adm.field.pdf_link',
          'type' => 'text',
          'name' => $lng . '[pdf_link]',
          'required' => FALSE,
          'value' => $entity ? $entity->getPdfLink() : '',
          'translate' => FALSE,
        ],
        'blockjour_order' => [
          'label' => 'adm.field.blockjour_order',
          'type' => 'text',
          'name' => $lng . '[blockjour_order]',
          'required' => FALSE,
          'value' => $entity ? $entity->getBlockjourOrder() : '',
          'translate' => FALSE,
        ],
        'visa' => [
          'label' => 'adm.field.visa',
          'type' => 'checkbox',
          'name' => $lng . '[visa]',
          'value' => 1,
          'checked' => $entity ? ($entity->isVisa() ? 1 : 0) : 0,
          'translate' => FALSE,
        ],
        'flight_from' => [
          'label' => 'adm.field.flight_from',
          'type' => 'text',
          'name' => $lng . '[flight_from]',
          'required' => FALSE,
          'value' => $entity ? $entity->getFlightFrom() : '',
          'translate' => FALSE,
        ],
        'clarification' => [
          'label' => 'adm.field.clarification',
          'type' => 'text',
          'name' => $lng . '[clarification]',
          'required' => FALSE,
          'value' => $entity ? $entity->getClarification() : '',
          'translate' => FALSE,
        ],

        'separator_personal_details' => [
          'label' => 'adm.field.separator_personal_details',
          'type' => 'separator',
          'translate' => FALSE,
        ],
        'civilite' => [
          'label' => 'adm.field.civilite',
          'type' => 'select',
          'name' => $lng . '[civilite]',
          'required' => FALSE,
          'value' => $entity->getCivilite() ? $entity->getCivilite()
            ->getId() : NULL,
          'value_zero' => FALSE,
          'values' => $civilite,
          'translate' => FALSE,
        ],
        'nom' => [
          'label' => 'adm.field.nom',
          'type' => 'text',
          'name' => $lng . '[nom]',
          'required' => FALSE,
          'value' => $entity ? $entity->getNom() : '',
          'translate' => FALSE,
        ],
        'premon' => [
          'label' => 'adm.field.premon',
          'type' => 'text',
          'name' => $lng . '[premon]',
          'required' => FALSE,
          'value' => $entity ? $entity->getPrenom() : '',
          'translate' => FALSE,
        ],
        'phone' => [
          'label' => 'adm.field.phone',
          'type' => 'text',
          'name' => $lng . '[phone]',
          'required' => FALSE,
          'value' => $entity ? $entity->getPhone() : '',
          'translate' => FALSE,
        ],
        'email' => [
          'label' => 'adm.field.email',
          'type' => 'text',
          'name' => $lng . '[email]',
          'required' => FALSE,
          'value' => $entity ? $entity->getEmail() : '',
          'translate' => FALSE,
        ],

        'separator_booking_prices' => [
          'label' => 'adm.field.separator_booking_prices',
          'type' => 'separator',
          'translate' => FALSE,
        ],
        'prix' => [
          'label' => 'adm.field.prix',
          'type' => 'text',
          'name' => $lng . '[prix]',
          'required' => FALSE,
          'value' => $entity ? $entity->getPrix() : '',
          'translate' => FALSE,
        ],
        'supplement_single_price' => [
          'label' => 'adm.field.supplement_single_price',
          'type' => 'text',
          'name' => $lng . '[supplement_single_price]',
          'required' => FALSE,
          'value' => $entity ? $entity->getSupplementSinglePrice() : '',
          'translate' => FALSE,
        ],

      ];

      $fieldset = 'translate';
      if ($lng == $this->_defaultLocale) {
        $fieldset = 'default';
      }
      $form[$fieldset][$lng] = $fields;
    }
    return $form;
  }

  private function saveEntity($data, $entity, $em) {
    //echo'<pre>'; print_r($data); exit;
    //$em->persist($entity);
    //$em->flush();
    foreach ($data as $localeName => $locale) {
      if (in_array($localeName, $this->_locales)) {
        $entity->setTranslatableLocale($localeName);
        /* BLOCK FOR TRANSLABLE VALUES*/

        /* END BLOCK FOR TRANSLABLE VALUES*/

        /* BLOCK FOR NON TRANSLATED VALUES*/
        if ($localeName == $this->_defaultLocale) {

          if (!empty($data[$localeName]['name'])) {
            $entity->setName(trim($data[$localeName]['name']));
          }
          if (!empty($data[$localeName]['date'])) {
            $start = explode(" ", $data[$localeName]['date']);
            $start[0] = explode(".", $start[0]);
            $start[1] = str_replace(":", "", $start[1]);
            $start = $start[0][2] . $start[0][1] . $start[0][0] . $start[1];

            $entity->setDate(!empty($data[$localeName]['date']) ? $start : '');
          }

          if (!empty($data[$localeName]['hotel'])) {
            $entity->setHotel(trim($data[$localeName]['hotel']));
          }

          if (!empty($data[$localeName]['amount_of_people'])) {
            $entity->setAmountOfPeople(trim($data[$localeName]['amount_of_people']));
          }

          if (!empty($data[$localeName]['supplement_single'])) {
            $entity->setSupplementSingle(trim($data[$localeName]['supplement_single']));
          }

          if (!empty($data[$localeName]['numbre_de_jours'])) {
            $entity->setNumbreDeJours(trim($data[$localeName]['numbre_de_jours']));
          }

          if (!empty($data[$localeName]['numbre_de_nuits'])) {
            $entity->setNumbreDeNuits(trim($data[$localeName]['numbre_de_nuits']));
          }

          if (!empty($data[$localeName]['numbre_de_chambers'])) {
            $entity->setNumbreDeChambers(trim($data[$localeName]['numbre_de_chambers']));
          }

          if (!empty($data[$localeName]['prix'])) {
            $entity->setPrix(trim($data[$localeName]['prix']));
          }

          if (!empty($data[$localeName]['supplement_single_price'])) {
            $entity->setSupplementSinglePrice(trim($data[$localeName]['supplement_single_price']));
          }

          if (!empty($data[$localeName]['pdf_link'])) {
            $entity->setPdfLink(trim($data[$localeName]['pdf_link']));
          }

          if (!empty($data[$localeName]['blockjour_order'])) {
            $entity->setBlockjourOrder(trim($data[$localeName]['blockjour_order']));
          }

          $entity->setVisa(!empty($data[$localeName]['visa']) ? 1 : 0);

          if (!empty($data[$localeName]['flight_from'])) {
            $entity->setFlightFrom(trim($data[$localeName]['flight_from']));
          }

          if (!empty($data[$localeName]['clarification'])) {
            $entity->setClarification(trim($data[$localeName]['clarification']));
          }

          if (!empty($data[$localeName]['nom'])) {
            $entity->setNom(trim($data[$localeName]['nom']));
          }

          if (!empty($data[$localeName]['prenom'])) {
            $entity->setPrenom(trim($data[$localeName]['prenom']));
          }

          if (!empty($data[$localeName]['phone'])) {
            $entity->setPhone(trim($data[$localeName]['phone']));
          }

          if (!empty($data[$localeName]['email'])) {
            $entity->setEmail(trim($data[$localeName]['email']));
          }

          if (!empty($data[$localeName]['key'])) {
            $entity->setKey(trim($data[$localeName]['key']));
          }

          if (!empty($data[$localeName]['website_version'])) {
            $entity->setWebsiteVersion(trim($data[$localeName]['website_version']));
          }

          if (!empty($data[$localeName]['comment'])) {
            $entity->setComment(trim($data[$localeName]['comment']));
          }

          if (!empty($data[$localeName]['devis_booking_id'])) {
            $entity->setDevisBookingId(trim($data[$localeName]['devis_booking_id']));
          }

          if (!empty($data[$localeName]['excel_link'])) {
            $entity->setExcelLink(trim($data[$localeName]['excel_link']));
          }

          $assigned_user = $em->getRepository('AppBundle\Entity\User')
            ->findOneBy(['id' => $data[$localeName]['assigned_user']]);
          if ($assigned_user) {
            $entity->setAssignedUser($assigned_user);
          }
          else {
            $entity->setAssignedUser(NULL);
          }


          if (!empty($data[$localeName]['hotel_stars'])) {
            $selectedStars = [];
            foreach ($data[$localeName]['hotel_stars'] as $star => $value) {
              if (!empty($value)) {
                $selectedStars[] = $star;
              }
            }
            $stars = $em->getRepository('AppBundle\Entity\BookHotelStars')
              ->findBy(['id' => $selectedStars]);
            $entity->setHotelStars($stars);
          }
          else {
            $entity->setHotelStars(NULL);
          }

          $booking = $em->getRepository('AppBundle\Entity\BookingToRelatedProduct')
            ->findOneBy(['booking' => $entity]);
          if ($booking) {
            $em->remove($booking);
            $em->flush();
          }
          $entity->setBookedProduct(NULL);
          if (!empty($data[$localeName]['booked_product'])) {
            $class = 'AppBundle\Entity\\' . $data[$localeName]['booked_product']['entity'];
            $atribute_array = ['Voyage', 'Extension', 'Visit', 'Event'];
            $eclass = $em->getRepository($class)
              ->findOneBy(['id' => $data[$localeName]['booked_product']['id']]);
            $BookedProduct = new BookingToRelatedProduct();
            $BookedProduct->setBooking($entity);
            foreach ($atribute_array as $value) {
              $method = 'set' . $value;
              if ($value == $data[$localeName]['booked_product']['entity']) {
                $BookedProduct->$method($eclass);
              }
              else {
                $BookedProduct->$method(NULL);
              }
            }
            $em->persist($BookedProduct);
            $entity->setBookedProduct($BookedProduct);
          }
          else {
            $entity->setBookedProduct(NULL);
          }

          if (!empty($data[$localeName]['etiquette'])) {
            $entity->setEtiquette(trim($data[$localeName]['etiquette']));
          }

          $civilite = $em->getRepository('AppBundle\Entity\BookCivilite')
            ->findOneBy(['id' => $data[$localeName]['civilite']]);
          if ($civilite) {
            $entity->setCivilite($civilite);
          }
          else {
            $entity->setCivilite(NULL);
          }

          $offer_options = $em->getRepository('AppBundle\Entity\BookOfferOptions')
            ->findOneBy(['id' => $data[$localeName]['offer_options']]);
          if ($offer_options) {
            $entity->setOfferOptions($offer_options);
          }
          else {
            $entity->setOfferOptions(NULL);
          }

          $send_docs = $em->getRepository('AppBundle\Entity\BookSendDocs')
            ->findOneBy(['id' => $data[$localeName]['send_docs']]);
          if ($send_docs) {
            $entity->setSendDocs($send_docs);
          }
          else {
            $entity->setSendDocs(NULL);
          }
        }

        $entity->mergeNewTranslations();
      }
      //$entity->setTarifs($tarif);
      /* END BLOCK FOR NON TRANSLATED VALUES*/

    }
    $em->persist($entity);
    $em->flush();
    return TRUE;
  }
}
