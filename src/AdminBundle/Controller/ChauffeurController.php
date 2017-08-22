<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\Chauffeur;
use AppBundle\Entity\ChauffeurToRelatedProduct;
use AppBundle\Service\Locales;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ChauffeurController extends Controller {

  private $_itemsOnPage = 20;

  private $_locales;

  private $_defaultLocale;

  public function __construct() {
    $loc = new Locales();
    $this->_locales = $loc->getLocales();
    $this->_defaultLocale = $loc->getDefaultLocale();
  }

  /**
   * @Route("/chauffeur/", name="admin_chauffeur")
   */
  public function indexAction(Request $request) {

    $em = $this->getDoctrine()->getManager();

//    $data['data_list'] = $em->getRepository('AppBundle\Entity\Chauffeur')
//      ->findBy([], ['id' => 'DESC']);
	  $data['data_list'] = $em->getRepository('AppBundle\Entity\Chauffeur')->findAllForAdminList($em,'AppBundle\Entity\Chauffeur',$request);


	  $data['data_fields'] = ['id', 'label'];
    $data['data_title'] = 'adm.chauffeur';

    $link = $this->generateUrl('admin_chauffeur_add', [], UrlGeneratorInterface::ABSOLUTE_PATH);


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
        'link' => 'admin_chauffeur_edit',
        'class' => '',
        'confirm' => FALSE,
      ],
      [
        'name' => 'delete',
        'link' => 'admin_chauffeur_delete',
        'class' => 'danger',
        'confirm' => 'adm.action.delete.confirm',
      ],
    ];

    $page = !empty($request->query->get('page')) ? intval($request->query->get('page')) : 1;
    $paginator = [
		'currentFilters' => $request->query->all(),
      'currentPage' => $page,
      'paginationPath' => 'admin_chauffeur',
      'showAlwaysFirstAndLast' => TRUE,
      'lastPage' => ceil(count($data['data_list']) / $this->_itemsOnPage),
    ];

    $data['data_list'] = array_slice($data['data_list'], ($page * $this->_itemsOnPage - $this->_itemsOnPage), $this->_itemsOnPage);
    $data = array_merge($data, $paginator);

    return $this->render('AdminBundle:Default:list.html.twig', $data);
  }

  /**
   * @Route("/chauffeur/add", name="admin_chauffeur_add")
   * @Route("/chauffeur/edit/{id}", defaults={"id" = 0},
   *   name="admin_chauffeur_edit")
   */
  public function editAction($id = 0, Request $request) {
    $em = $this->getDoctrine()->getManager();

    $entity = new Chauffeur();
    if (!empty($id)) {
      $entity = $em->getRepository('AppBundle\Entity\Chauffeur')
        ->findOneBy(['id' => $id]);
    }//var_dump($entity->isActive()); exit;
    $data['entity'] = $entity;
    //print_r(get_class_methods($data['entity']->translate())); exit;
    //TITLE FOR PAGE
    $data['data_title'] = $data['entity']->getName();

    $data['data_type'] = 'adm.chauffeur';

    $data['data_buttons'] = [
      [
        'name' => 'save',
        'link' => 'admin_chauffeur_edit',
        'class' => 'primary',
        'button' => TRUE,
        'button_type' => 'submit',
      ],
      [
        'name' => 'cancel',
        'link' => !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->generateUrl('admin_chauffeur', [], UrlGeneratorInterface::ABSOLUTE_PATH),
        'class' => 'default',
      ],
    ];

    if ($request->isMethod('POST')) {

      $data = $request->request->all();

      $this->saveEntity($data, $entity, $em);
      return $this->redirectToRoute('admin_chauffeur', []);
    }

    $data['form'] = $this->createEntityForm($entity, $id, $em);

    return $this->render('AdminBundle:Default:form.html.twig', $data);
  }

  /**
   * @Route("/chauffeur/delete/{id}", name="admin_chauffeur_delete")
   */
  public function deleteAction($id) {
    $em = $this->getDoctrine()->getManager();

    $data = $em->getRepository('AppBundle\Entity\Chauffeur')
      ->findOneBy(['id' => intval($id)]);
    if ($data) {
      $em->remove($data);
      $em->flush();
    }


    return !empty($_SERVER['HTTP_REFERER']) ? $this->redirect($_SERVER['HTTP_REFERER']) : $this->generateUrl('admin_chauffeur', UrlGeneratorInterface::ABSOLUTE_PATH);

  }

  private function createEntityForm($entity, $id = 0, $em) {
    if (!empty($id)) {
      $form['action'] = $this->generateUrl('admin_chauffeur_edit', ['id' => $id], UrlGeneratorInterface::ABSOLUTE_PATH);
    }
    else {
      $form['action'] = $this->generateUrl('admin_chauffeur_add', [], UrlGeneratorInterface::ABSOLUTE_PATH);
    }
    $form['id'] = $id;
    $form['form_id'] = 'form_' . md5('chauffeur');
    $form['separator'] = TRUE;

    $domains = $em->getRepository('AppBundle\Entity\BookDomain')->findAll();
    $domainValues = [];
    foreach ($domains as $domain) {
      $domainValues[$domain->getId()] = $domain->translate()->getName();
    }


    $chauffeur_season = $em->getRepository('AppBundle\Entity\BookSeason')
      ->findAll();
    $chauffeur_season_values = [];
    foreach ($chauffeur_season as $season) {
      $chauffeur_season_values[$season->getId()] = $season->translate()
        ->getName();
    }

    foreach ($this->_locales as $lng) {

      $type_de_vehicle = [];
      $type_de_vehicles = $em->getRepository('AppBundle\Entity\BookVehicleTypes')
        ->findAll();
      foreach ($type_de_vehicles as $vehicle_type) {
        $type_de_vehicle[$vehicle_type->getId()] = $vehicle_type->translate()
          ->getName();
      }

      $duree = [];
      $durees = $em->getRepository('AppBundle\Entity\BookDriverDuration')->findAll();
      foreach ($durees as $adurees) {
        $duree[$adurees->getId()] = $adurees->translate()->getName();
      }

      $currency = [];
      $currency_array = $em->getRepository('AppBundle\Entity\BookCurrency')
        ->findAll();
      foreach ($currency_array as $acurrency) {
        $currency[$acurrency->getId()] = $acurrency->translate()->getName();
      }

      $group_type = [];
      $group_types = $em->getRepository('AppBundle\Entity\BookGroupType')
        ->findAll();
      foreach ($group_types as $agroup_type) {
        $group_type[$agroup_type->getId()] = $agroup_type->translate()
          ->getName();
      }


      $fields = [
          'label' => array(
              'label' => 'adm.field.label',
              'type' => 'text',
              'name' => $lng.'[label]',
              'required' => false,
              'value' => $entity ? $entity->getLabel() : '',
              'translate' => false
          ),
        /*'name' => [
          'label' => 'adm.field.title',
          'type' => 'text',
          'name' => $lng . '[name]',
          'required' => ($lng != $this->_defaultLocale ? FALSE : TRUE),
          'value' => $entity ? $entity->getName() : '',
          'translate' => FALSE,
        ],*/
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
        'type_de_vehicle' => [
          'label' => 'adm.field.type_de_vehicle',
          'type' => 'select',
          'name' => $lng . '[type_de_vehicle]',
          'required' => FALSE,
          'value' => $entity->getTypeDeVehicle() ? $entity->getTypeDeVehicle()
            ->getId() : NULL,
          'value_zero' => FALSE,
          'values' => $type_de_vehicle,
          'translate' => FALSE,
        ],
        'duree' => [
          'label' => 'adm.field.duree',
          'type' => 'select',
          'name' => $lng . '[duree]',
          'required' => FALSE,
          'value' => $entity->getDuree() ? $entity->getDuree()
            ->getId() : NULL,
          'value_zero' => FALSE,
          'values' => $duree,
          'translate' => FALSE,
        ],
        'comments' => [
          'label' => 'adm.field.comments',
          'type' => 'text',
          'name' => $lng . '[comments]',
          'required' => FALSE,
          'value' => $entity ? $entity->getComments() : '',
          'translate' => FALSE,
        ],
        'nom_en_latin' => [
          'label' => 'adm.field.nom_en_latin',
          'type' => 'text',
          'name' => $lng . '[nom_en_latin]',
          'required' => FALSE,
          'value' => $entity ? $entity->getNomEnLatin() : '',
          'translate' => FALSE,
        ],
        'nom_en_latin' => [
          'label' => 'adm.field.nom_en_latin',
          'type' => 'text',
          'name' => $lng . '[nom_en_latin]',
          'required' => FALSE,
          'value' => $entity ? $entity->getNomEnLatin() : '',
          'translate' => FALSE,
        ],
        'nom_en_cyrillique' => [
          'label' => 'adm.field.nom_en_cyrillique',
          'type' => 'text',
          'name' => $lng . '[nom_en_cyrillique]',
          'required' => FALSE,
          'value' => $entity ? $entity->getNomEnCyrillique() : '',
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
        'supp_info' => [
          'label' => 'adm.field.supp_info',
          'type' => 'text',
          'name' => $lng . '[supp_info]',
          'required' => FALSE,
          'value' => $entity ? $entity->getSuppInfo() : '',
          'translate' => FALSE,
        ],
        'info_utile' => [
          'label' => 'adm.field.info_utile',
          'type' => 'text',
          'name' => $lng . '[info_utile]',
          'required' => FALSE,
          'value' => $entity ? $entity->getInfoUtile() : '',
          'translate' => FALSE,
        ],
        'currency' => [
          'label' => 'adm.field.currency',
          'type' => 'select',
          'name' => $lng . '[currency]',
          'required' => FALSE,
          'value' => $entity->getDuree() ? $entity->getDuree()
            ->getId() : NULL,
          'value_zero' => FALSE,
          'values' => $currency,
          'translate' => FALSE,
        ],
        'group_type' => [
          'label' => 'adm.field.group_type',
          'type' => 'radio',
          'name' => $lng . '[group_type]',
          'values' => $group_type,
          'value_zero' => FALSE,
          'checked_value' => $entity->getGroupType() ? $entity->getGroupType()
            ->getId() : FALSE,
          'translate' => FALSE,
        ],
          'separator_excel' => array(
              'label' => 'adm.field.excel',
              'type' => 'separator',
              'translate' => false
          ),
        'excel_custom_id' => [
          'label' => 'adm.field.excel_custom_id',
          'type' => 'text',
          'name' => $lng . '[excel_custom_id]',
          'required' => FALSE,
          'value' => $entity ? $entity->getExcelCustomId() : '',
          'translate' => FALSE,
        ],
        'excel_title' => [
          'label' => 'adm.field.excel_title',
          'type' => 'text',
          'name' => $lng . '[excel_title]',
          'required' => FALSE,
          'value' => $entity ? $entity->getExcelTitle() : '',
          'translate' => FALSE,
        ],
        'excel_description' => [
          'label' => 'adm.field.excel_description',
          'type' => 'text',
          'name' => $lng . '[excel_description]',
          'required' => FALSE,
          'value' => $entity ? $entity->getExcelDescription() : '',
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

          if (!empty($data[$localeName]['ville'])) {
            $location = $em->getRepository('AppBundle\Entity\Location')->findOneBy( array( 'id'=> $data[$localeName]['ville']) );
            $entity->setVille($location);
          } else {
            $entity->setVille(null);
          }
          if (!empty($data[$localeName]['extension'])) {
            $entity->setExtension(trim($data[$localeName]['extension']));
          }
          if (!empty($data[$localeName]['comments'])) {
            $entity->setComments(trim($data[$localeName]['comments']));
          }
          if (!empty($data[$localeName]['nom_en_latin'])) {
            $entity->setNomEnLatin(trim($data[$localeName]['nom_en_latin']));
          }
          if (!empty($data[$localeName]['nom_en_cyrillique'])) {
            $entity->setNomEnCyrillique(trim($data[$localeName]['nom_en_cyrillique']));
          }
          if (!empty($data[$localeName]['phone'])) {
            $entity->setPhone(trim($data[$localeName]['phone']));
          }
          if (!empty($data[$localeName]['supp_info'])) {
            $entity->setSuppInfo(trim($data[$localeName]['supp_info']));
          }
          if (!empty($data[$localeName]['info_utile'])) {
            $entity->setInfoUtile(trim($data[$localeName]['info_utile']));
          }
          if (!empty($data[$localeName]['excel_custom_id'])) {
            $entity->setExcelCustomId(trim($data[$localeName]['excel_custom_id']));
          }
          if (!empty($data[$localeName]['excel_title'])) {
            $entity->setExcelTitle(trim($data[$localeName]['excel_title']));
          }
          if (!empty($data[$localeName]['excel_description'])) {
            $entity->setExcelDescription(trim($data[$localeName]['excel_description']));
          }


          $type_de_vehicle = $em->getRepository('AppBundle\Entity\BookVehicleTypes')
            ->findOneBy(['id' => $data[$localeName]['type_de_vehicle']]);
          if ($type_de_vehicle) {
            $entity->setTypeDeVehicle($type_de_vehicle);
          }
          else {
            $entity->setTypeDeVehicle(NULL);
          }


          $duree = $em->getRepository('AppBundle\Entity\BookDriverDuration')
            ->findOneBy(['id' => $data[$localeName]['duree']]);
          if ($duree) {
            $entity->setDuree($duree);
          }
          else {
            $entity->setDuree(NULL);
          }


          $currency = $em->getRepository('AppBundle\Entity\BookCurrency')
            ->findOneBy(['id' => $data[$localeName]['currency']]);
          if ($currency) {
            $entity->setCurrency($currency);
          }
          else {
            $entity->setCurrency(NULL);
          }


          $group_type = $em->getRepository('AppBundle\Entity\BookGroupType')
            ->findOneBy(['id' => $data[$localeName]['group_type']]);
          if ($group_type) {
            $entity->setGroupType($group_type);
          }
          else {
            $entity->setGroupType(NULL);
          }
            $label = 'Chau_';
            if($entity->getVille()){
                $label=$label.substr($entity->getVille()->translate($localeName, true)->getCity(), 0, 3).'_';
            }
            if($type_de_vehicle){
                $label=$label.$type_de_vehicle->translate($localeName, true)->getName();
            }
            if($duree){
                $label=$label.substr(str_replace(" ", "", $duree->translate($localeName, true)->getName()), 0, 3).'_';
                if($entity->getExtension()){
                    $label=$label.'_';
                }
            }

            if($entity->getExtension()){
                $label=$label.ucfirst(strtolower(str_replace("-", "", str_replace("_", "", str_replace(" ", "", $entity->getExtension())))));
            }
            $entity->setLabel($label);

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
