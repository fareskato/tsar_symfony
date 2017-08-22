<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\Hotel;
use AppBundle\Entity\HotelTarif;
use AppBundle\Service\Locales;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HotelController extends Controller
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
     * @Route("/hotel", name="admin_hotel")
     */
    public function indexAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

//		$data['data_list'] = $em->getRepository('AppBundle\Entity\Hotel')->findBy(array(), array('id' => 'DESC'));
		$data['data_list'] = $em->getRepository('AppBundle\Entity\Hotel')->findAllForAdminList($em,'AppBundle\Entity\Hotel',$request);


		$data['data_fields'] = array('id','image','label','name','active_domain','active_lang');
		$data['data_title'] = 'adm.hotel';

		$link = $this->generateUrl('admin_hotel_add', array(), UrlGeneratorInterface::ABSOLUTE_PATH);


		$data['data_buttons'] = array(
			array(
				'name' => 'add',
				'link' => $link,
				'class' => 'primary'
			),
			array(
				'name' => 'reset_filters',
				'link' => $this->generateUrl('admin_hotel'),
				'class' => 'default'
			)
		);

		$data['data_actions'] = array(
				array(
					'name' => 'edit',
					'link' => 'admin_hotel_edit',
					'class' => '',
					'confirm' => false
				),
				array(
					'name' => 'delete',
					'link' => 'admin_hotel_delete',
					'class' => 'danger',
					'confirm' => 'adm.action.delete.confirm'
				),
			);

		$page = !empty($request->query->get('page')) ? intval($request->query->get('page')) : 1;
		$paginator = array(
			'currentFilters' => $request->query->all(),
			'currentPage' => $page,
			'paginationPath' => 'admin_hotel',
			'showAlwaysFirstAndLast' => true,
			'lastPage' => ceil(count($data['data_list']) / $this->_itemsOnPage),
		);

		$data['data_list'] = array_slice($data['data_list'], ($page * $this->_itemsOnPage - $this->_itemsOnPage), $this->_itemsOnPage);
		$data = array_merge($data,$paginator);

        return $this->render('AdminBundle:Default:list.html.twig',$data);
    }

    /**
     * @Route("/hotel/add", name="admin_hotel_add")
     * @Route("/hotel/edit/{id}", defaults={"id" = 0}, name="admin_hotel_edit")
     */
    public function editAction($id = 0, Request $request){
        $em = $this->getDoctrine()->getManager();



        $entity = new Hotel();
		$entity->setDefaultLocale($this->_defaultLocale);
		if (!empty($id)) {
			$entity = $em->getRepository('AppBundle\Entity\Hotel')->findOneBy(array('id'=>$id));
		}

        $data['entity'] = $entity;

		//TITLE FOR PAGE
		$data['data_title'] = (!empty($data['entity']->translate()->getName()) ? $data['entity']->translate()->getName() : $this->get('translator')->trans('adm.action.new'));

        $data['data_type'] =  'adm.hotel.hotel_name';

        $data['data_buttons'] = array(
            array(
                'name' => 'save',
                'link' => 'admin_hotel_edit',
                'class' => 'primary',
                'button' => true,
                'button_type' => 'submit'
            ),
            array(
                'name' => 'cancel',
                'link' => !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->generateUrl('admin_hotel', array(), UrlGeneratorInterface::ABSOLUTE_PATH),
                'class' => 'default',
            )
        );

        if ($request->isMethod('POST')) {

            $data = $request->request->all();

            $this->saveEntity($data,$entity,$em);
            return $this->redirectToRoute('admin_hotel', array());
        }

        $data['form'] = $this->createEntityForm($entity,$id,$em);


        return $this->render('AdminBundle:Default:form.html.twig',$data);
    }

    /**
     * @Route("/hotel/delete/{id}", name="admin_hotel_delete")
     */
    public function deleteAction($id){
        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository('AppBundle\Entity\Hotel')->findOneBy(array('id' => intval($id)));
        if ($data) {
            $em->remove($data);
            $em->flush();
        }


        return !empty($_SERVER['HTTP_REFERER']) ? $this->redirect($_SERVER['HTTP_REFERER']) : $this->generateUrl('admin_hotel', UrlGeneratorInterface::ABSOLUTE_PATH);

    }

	private function createEntityForm($entity, $id = 0, $em) {
		if (!empty($id)) {
			$form['action'] = $this->generateUrl('admin_hotel_edit', array('id' => $id), UrlGeneratorInterface::ABSOLUTE_PATH);
		} else{
			$form['action'] = $this->generateUrl('admin_hotel_add', array(), UrlGeneratorInterface::ABSOLUTE_PATH);
		}

		$form['id'] = $id;
		$form['form_id'] = 'form_'.md5('hotel');
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

		$stars = $em->getRepository('AppBundle\Entity\BookHotelStars')->findAll();
		$starsValues = array();
		foreach ($stars as $star ) {
			$starsValues[$star->getId()] = $star->translate()->getName();
		}
		$starsCheckedValues = array();
		if (!empty($entity->getHotelStars())) {
			foreach ($entity->getHotelStars() as $star) {
				$starsCheckedValues[] = $star->getId();
			}
		}

		$internet = $em->getRepository('AppBundle\Entity\BookAccessInternet')->findAll();
		$internetValues = array();
		foreach ($internet as $i ) {
			$internetValues[$i->getId()] = $i->translate()->getName();
		}

		$etiquette = $em->getRepository('AppBundle\Entity\BookEtiquette')->findAll();
		$etiquetteValues = array();
		foreach ($etiquette as $i ) {
			$etiquetteValues[$i->getId()] = $i->translate()->getName();
		}

        $service = $em->getRepository('AppBundle\Entity\BookServices')->findAll();
        $serviceValues = array();
        foreach ($service as $value ) {
            $serviceValues[$value->getId()] = $value->translate()->getName();
        }
        $serviceCheckedValues = array();
        if (!empty($entity->getHotelService())) {
            foreach ($entity->getHotelService() as $service) {
                $serviceCheckedValues[] = $service->getId();
            }
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
				'name' => array(
					'label' => 'adm.field.name',
					'type' => 'text',
					'name' => $lng.'[name]',
					'required' => ($lng != $this->_defaultLocale ? false : true),
					'value' => $entity ? $entity->translate($lng,($lng == $this->_defaultLocale ? true : false))->getName() : '',
					'translate' => true
				),
                'categorie' => array(
                    'label' => 'adm.field.categorie',
                    'type' => 'text',
                    'name' => $lng.'[categorie]',
                    'required' => false,
                    'value' => $entity ? $entity->getCategorie() : '',
                    'translate' => false
                ),
				'headline' => array(
					'label' => 'adm.field.headline',
					'type' => 'text',
					'name' => $lng.'[headline]',
					'required' => false,
					'value' => $entity ? $entity->translate($lng,($lng == $this->_defaultLocale ? true : false))->getHeadline() : '',
					'translate' => true
				),
				'body_summary' => array(
					'label' => 'adm.field.body_summary',
					'type' => 'textarea',
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
				'etiquette' => array(
					'label' => 'adm.field.etiquette',
					'type' => 'radio',
					'name' => $lng.'[etiquette]',
					'values' => $etiquetteValues,
					'value_zero' => true,
					'checked_value' =>  $entity->getEtiquette() ? $entity->getEtiquette()->getId() : false,
					'translate' => false
				),
				'separator_location' => array(
					'label' => 'adm.field.location',
					'type' => 'separator',
					'translate' => false
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
					'translate' => false,
					'editLink' => array(
						'type' => 'destination',
						'path' => array(),
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
				'metro' => array(
					'label' => 'adm.field.metro',
					'type' => 'relation_many',
					'autocomplete' => 'metro',
					'autocomplete_path' => 'admin_autocomplete_metro',
					'name' => $lng.'[metro][]',
					'add' => 'admin_book_add',
					'add_arguments' => array('book_name'=>'book_metro'),
					'field_rel' => array('id','name'),
					'values' => $entity->getMetro() ? $entity->getMetro() : array(),
					'translate' => false,
					'editLink' => array(
						'type' => 'book',
						'path' => array('book_name' => 'book_metro'),
					),
				),

				'separator_services' => array(
					'label' => 'adm.field.services',
					'type' => 'separator',
					'translate' => false
				),
				'type_of_hotel' => array(
					'label' => 'adm.field.type_of_hotel',
					'type' => 'text',
					'name' => $lng.'[type_of_hotel]',
					'required' => false,
					'value' => $entity ? $entity->translate($lng,($lng == $this->_defaultLocale ? true : false))->getTypeOfHotel() : '',
					'translate' => true
				),
				'hotel_stars' => array(
					'label' => 'adm.field.hotel_stars',
					'type' => 'checkbox_multiple',
					'name' => $lng.'[hotel_stars]',
					'values' => $starsValues,
					'checked_values' => $starsCheckedValues, //only checked values array
					'translate' => false
				),
				'hotel_internet' => array(
					'label' => 'adm.field.hotel_internet',
					'type' => 'radio',
					'name' => $lng.'[hotel_internet]',
					'values' => $internetValues,
					'value_zero' => true,
					'checked_value' =>  $entity->getHotelInternet() ? $entity->getHotelInternet()->getId() : false,
					'translate' => false
				),
				'number_of_rooms' => array(
					'label' => 'adm.field.number_of_rooms',
					'type' => 'text',
					'name' => $lng.'[number_of_rooms]',
					'required' => false,
					'value' => $entity ? $entity->getNumberOfRooms() : '',
					'translate' => false
				),
                'hotel_service' => array(
                    'label' => 'adm.field.hotel_service',
                    'type' => 'relation_many',
                    'autocomplete' => 'hotel_service',
                    'autocomplete_path' => 'admin_autocomplete_service',
                    'name' => $lng.'[hotel_service][]',
                    'add' => 'admin_book_add',
                    'add_arguments' => array('book_name'=>'book_service'),
                    'field_rel' => array('id','name'),
                    'values' => $entity->getHotelService() ? $entity->getHotelService() : array(),
                    'translate' => false,
					'editLink' => array(
						'type' => 'book',
						'path' => array('book_name' => 'book_services'),
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

				'separator_seo' => array(
					'label' => 'adm.field.seo',
					'type' => 'separator',
					'translate' => false
				),
				'keywords' => array(
					'label' => 'adm.field.keywords',
					'type' => 'text',
					'name' => $lng.'[keywords]',
					'required' => false,
					'value' => $entity ? $entity->translate($lng,($lng == $this->_defaultLocale ? true : false))->getKeywords() : '',
					'translate' => true
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
                    'value' => $entity ? $entity->getExcelCustomId() : '',
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
				if (!empty($data[$localeName]['body_summary'])) {
					$entity->translate($localeName,false)->setBodySummary(trim($data[$localeName]['body_summary']));
				}
				if (!empty($data[$localeName]['body'])) {
					$entity->translate($localeName,false)->setBody(trim($data[$localeName]['body']));
				}
				if (!empty($data[$localeName]['keywords'])) {
					$entity->translate($localeName,false)->setKeywords(trim($data[$localeName]['keywords']));
				}
				if (!empty($data[$localeName]['headline'])) {
					$entity->translate($localeName,false)->setHeadline(trim($data[$localeName]['headline']));
				}
				if (!empty($data[$localeName]['type_of_hotel'])) {
					$entity->translate($localeName,false)->setTypeOfHotel(trim($data[$localeName]['type_of_hotel']));
				}
				if (!empty($data[$localeName]['slug'])) {
					$entity->translate($localeName,false)->setSlug($data[$localeName]['slug']);
				}
                if (!empty($data[$localeName]['name'])) {
                    $entity->translate($localeName, false)->setActive(!empty($data[$localeName]['active']) ? 1 : 0);
                }



				/* BLOCK FOR NON TRANSLATED VALUES*/
				if ($localeName == $this->_defaultLocale) {
                    if (!empty($data[$localeName]['categorie'])) {
                        $entity->setCategorie($data[$localeName]['categorie']);
                    }
					if (isset($data[$localeName]['favorite'])) {
						$entity->setFavorite(!empty($data[$localeName]['favorite']) ? 1 : 0);
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


					if (!empty($data[$localeName]['destination'])) {
						$destination = $em->getRepository('AppBundle\Entity\Destination')->findBy( array( 'id'=> $data[$localeName]['destination']) );
						$entity->setDestination($destination);
					} else {
						$entity->setDestination(null);
					}

					if (!empty($data[$localeName]['metro'])) {
						$metro = $em->getRepository('AppBundle\Entity\BookMetro')->findBy( array( 'id'=> $data[$localeName]['metro']) );
						$entity->setMetro($metro);
					} else {
						$entity->setMetro(null);
					}

                    if (!empty($data[$localeName]['hotel_service'])) {
                        $metro = $em->getRepository('AppBundle\Entity\BookServices')->findBy( array( 'id'=> $data[$localeName]['hotel_service']) );
                        $entity->setHotelService($metro);
                    } else {
                        $entity->setHotelService(null);
                    }


					if (!empty($data[$localeName]['location'])) {
						$location = $em->getRepository('AppBundle\Entity\Location')->findOneBy( array( 'id'=> $data[$localeName]['location']) );
						$entity->setLocation($location);
					} else {
						$entity->setLocation(null);
					}

					if (!empty($data[$localeName]['hotel_stars'])) {
						$selectedStars = array();
						foreach($data[$localeName]['hotel_stars'] as $star => $value){
							if (!empty($value)) {
								$selectedStars[] = $star;
							}
						}
						$stars = $em->getRepository('AppBundle\Entity\BookHotelStars')->findBy( array( 'id'=> $selectedStars) );
						$entity->setHotelStars($stars);
					} else {
						$entity->setHotelStars(null);
					}

					if (!empty($data[$localeName]['hotel_internet'])) {
						$internet = $em->getRepository('AppBundle\Entity\BookAccessInternet')->findOneBy( array( 'id'=> $data[$localeName]['hotel_internet']) );
						$entity->setHotelInternet($internet);
					} else {
						$entity->setHotelInternet(null);
					}

					if (!empty($data[$localeName]['etiquette'])) {
						$internet = $em->getRepository('AppBundle\Entity\BookEtiquette')->findOneBy( array( 'id'=> $data[$localeName]['etiquette']) );
						$entity->setEtiquette($internet);
					} else {
						$entity->setEtiquette(null);
					}

					$entity->setNumberOfRooms(!empty($data[$localeName]['number_of_rooms']) ? intval($data[$localeName]['number_of_rooms']) : null);


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

                    if (!empty($data[$localeName]['custom_id'])) {
                        $entity->setExcelCustomId(intval($data[$localeName]['custom_id']));
                    }
                    if (!empty($data[$localeName]['excel_title'])) {
                        $entity->setExcelTitle(trim($data[$localeName]['excel_title']));
                    }
                    if (!empty($data[$localeName]['excel_description'])) {
                        $entity->setExcelDescription(trim($data[$localeName]['excel_description']));
                    }

                    $label = 'Hotel_';
                    if($entity->getLocation()){//print_r(gettype($entity->getDestination()[0])); exit;
                        $label=$label.substr($entity->getLocation()->translate($localeName, true)->getCity(), 0, 3).'_';
                    }
                    if($entity->translate($localeName, true)->getName()){
                        $label=$label.ucfirst(strtolower(str_replace("-", "", str_replace("_", "", str_replace(" ", "", $entity->translate($localeName, true)->getName()))))).'_';
                    }

                    if($entity->getHotelStars()){
                        $label=$label.substr(str_replace(" ", "", $entity->getHotelStars()[0]->translate($localeName, true)->getName()), 0, 3).'_';
                    }
                    if($entity->getCategorie()){
                        $label=$label.ucfirst($entity->getCategorie());
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
