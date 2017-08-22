<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\Day;
use AppBundle\Entity\DayToProduct;
use AppBundle\Entity\Menu;
use AppBundle\Service\Locales;
use Doctrine\Common\Annotations\AnnotationReader;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DayController extends Controller
{

	private $_itemsOnPage = 20;

	private $_locales;
	private $_defaultLocale;

	public function __construct()
	{
		//We need locales everywhere in code
		//Проставляем локали
		$loc = new Locales();
		$this->_locales = $loc->getLocales();
		$this->_defaultLocale = $loc->getDefaultLocale();
	}

	/**
     * @Route("/day", name="admin_day")
     */
    public function indexAction(Request $request)
    {

		$em = $this->getDoctrine()->getManager();

		// Get list of all entities
//		$data['data_list'] = $em->getRepository('AppBundle\Entity\Day')->findBy(array(),array('id'=>'desc'));
		$data['data_list'] = $em->getRepository('AppBundle\Entity\Day')->findAllForAdminList($em,'AppBundle\Entity\Day',$request);


		//Get field to display
		$data['data_fields'] = array('id','label','name','slug');

		//Tranlate in AdminBundle/Resources/translations/messages.en.yml
		$data['data_title'] = 'adm.day';

		//Buttons in top and bottom
		$data['data_buttons'] = array(
			array(
				'name' => 'add',
				'link' => $this->generateUrl('admin_day_add', array(), UrlGeneratorInterface::ABSOLUTE_PATH),
				'class' => 'primary'
			)
		);

		//Buttons of action on each entity
		$data['data_actions'] = array(
				array(
					'name' => 'edit',
					'link' => 'admin_day_edit',
					'class' => '',
					'confirm' => false
				),
				array(
					'name' => 'delete',
					'link' => 'admin_day_delete',
					'class' => 'danger',
					'confirm' => 'adm.action.delete.confirm'
				),
			);

		$page = !empty($request->query->get('page')) ? intval($request->query->get('page')) : 1;
		$paginator = array(
			'currentFilters' => $request->query->all(),
			'currentPage' => $page,
			'paginationPath' => 'admin_day',
			'showAlwaysFirstAndLast' => true,
			'lastPage' => ceil(count($data['data_list']) / $this->_itemsOnPage),
		);

		$data['data_list'] = array_slice($data['data_list'], ($page * $this->_itemsOnPage - $this->_itemsOnPage), $this->_itemsOnPage);
		$data = array_merge($data,$paginator);

		//RENDER TEMPLATE
        return $this->render('AdminBundle:Default:list.html.twig',$data);
    }

	/**
	 * @Route("/day/add", name="admin_day_add")
	 * @Route("/day/edit/{id}", defaults={"id" = 0}, name="admin_day_edit")
	 */
	public function editAction($id = 0, Request $request)
	{

		$em = $this->getDoctrine()->getManager();

		//ALWAYS CREATE NEW ONE
		$entity = new Day();
		$entity->setDefaultLocale($this->_defaultLocale);
		if (!empty($id)) {
			$entity = $em->getRepository('AppBundle\Entity\Day')->findOneBy(array('id'=>$id));
		}

		//MOVE ENTITY TO FRONT
		$data['entity'] = $entity;

		//TITLE FOR PAGE
		$data['data_title'] = (!empty($data['entity']->translate()->getName()) ? $data['entity']->translate()->getName() : $this->get('translator')->trans('adm.action.new'));

		//TYPE FOR PAGE
		$data['data_type'] =  'adm.day.day_name';

		//BUTTONS
		$data['data_buttons'] = array(
			array(
				'name' => 'save',
				'link' => 'admin_day_edit',
				'class' => 'primary',
				'button' => true,
				'button_type' => 'submit'
			),
			array(
				'name' => 'cancel',
				'link' => !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->generateUrl('admin_day', array(), UrlGeneratorInterface::ABSOLUTE_PATH),
				'class' => 'default',
			)
		);

		//IF SAVE
		if ($request->isMethod('POST')) {
			$data = $request->request->all();

			$this->saveEntity($data,$entity,$em);

			return $this->redirectToRoute('admin_day', array());
		}

		//FORM CREATION
		$data['form'] = $this->createEntityForm($entity,$id,$em);


		return $this->render('AdminBundle:Default:form.html.twig',$data);

	}

	/**
	 * @Route("/day/delete/{id}", name="admin_day_delete")
	 */
	public function deleteAction($id)
	{
		$em = $this->getDoctrine()->getManager();

		$data = $em->getRepository('AppBundle\Entity\Day')->findOneBy(array('id' => intval($id)));
		if ($data) {
			$em->remove($data);
			$em->flush();
		}

		return !empty($_SERVER['HTTP_REFERER']) ? $this->redirect($_SERVER['HTTP_REFERER']) : $this->generateUrl('admin_day', array(), UrlGeneratorInterface::ABSOLUTE_PATH);
	}

		//CREATE FORM FOR ENTITY
	private function createEntityForm($entity, $id =0, $em) {

		//WHERE TO SAVE
		if (!empty($id)) {
			$form['action'] = $this->generateUrl('admin_day_edit', array('id' => $id), UrlGeneratorInterface::ABSOLUTE_PATH);
		} else{
			$form['action'] = $this->generateUrl('admin_day_add', array(), UrlGeneratorInterface::ABSOLUTE_PATH);
		}

		//ID OF ENTITY
		$form['id'] = $id;
		$form['form_id'] = 'form_'.md5('day');

		$form['separator'] = true; // true/false;

		$images = $entity->getImages() ? $entity->getImages() : array();
		$imagesValue = array();
		foreach($images as $i) {
			$imagesValue[] = array(
				'path' => $i->getUrl(),
				'value' => $i->getId(),
			);
		}

		foreach($this->_locales as $lng) {

			$fields = array(
				'name' => array(
					'label' => 'adm.field.name',
					'type' => 'text',
					'name' => $lng.'[name]',
					'required' => ($lng != $this->_defaultLocale ? false : true),
					'value' => $entity ? $entity->translate($lng,($lng == $this->_defaultLocale ? true : false))->getName() : '',
					'translate' => true
				),
				'title' => array(
					'label' => 'adm.field.title',
					'type' => 'text',
					'name' => $lng.'[title]',
					'required' => false,
					'value' => $entity ? $entity->translate($lng,($lng == $this->_defaultLocale ? true : false))->getTitle() : '',
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
                'separator_product' => array(
                    'label' => 'adm.field.product',
                    'type' => 'separator',
                    'translate' => false
                ),
                'day_produit' => array(
                    'label' => 'adm.field.day_produit',
                    'type' => 'relation_many_entity',
                    'autocomplete' => 'extension',
                    'autocomplete_path' => 'admin_autocomplete_ajouter_produit',
                    'name' => $lng.'[day_produit]',
                    'add' => false,
                    'field_rel' => array('id','entity','label'),
                    'values' => $entity->getDayProduit() ? $entity->getDayProduit() : array(),
                    'translate' => false,
                    'sortable' => 1,
                    'editLink' => array(
                        'type' =>  '', //$entity->getRelatedProduct() ? $entity->getRelatedProduct()->getClass() : '',
                        'path' => array(), //$entity->getRelatedProduct() ? array('id' => $entity->getRelatedProduct()->getId()) : array(),
                    ),
                ),
                'alternative_day' => array(
                    'label' => 'adm.field.alternative_day',
                    'type' => 'relation_many',
                    'autocomplete' => 'product',
                    'autocomplete_path' => 'admin_autocomplete_day',
                    'name' => $lng.'[alternative_day][]',
                    'add' => 'admin_destination_add',
                    'field_rel' => array('id','label','name'),
                    'values' => $entity->getAlternativeDay() ? $entity->getAlternativeDay() : array(),
                    'translate' => false,
                    'editLink' => array(
                        'type' => 'day',
                        'path' => $entity->getAlternativeDay() ? array('id' => $entity->getAlternativeDay()) : array(),
                    ),
                ),
                'separator_transfer' => array(
                    'label' => 'adm.field.transfer',
                    'type' => 'separator',
                    'translate' => false
                ),

                'transfer_one' => array(
                    'label' => 'adm.field.transfer_one',
                    'type' => 'relation_one',
                    'autocomplete' => 'product',
                    'autocomplete_path' => 'admin_autocomplete_transfer_produit',
                    'name' => $lng.'[transfer_one]',
                    'add' => 'admin_destination_add',
                    'field_rel' => array('id','label'),
                    'values' => $entity->getTransferOne() ? $entity->getTransferOne() : array(),
                    'translate' => false,
                    'editLink' => array(
                        'type' => 'destination',
                        'path' => $entity->getTransferOne() ? array('id' => $entity->getTransferOne()->getId()) : array(),
                    ),
                ),
                'transfer_two' => array(
                    'label' => 'adm.field.transfer_two',
                    'type' => 'relation_one',
                    'autocomplete' => 'product',
                    'autocomplete_path' => 'admin_autocomplete_transfer_produit',
                    'name' => $lng.'[transfer_two]',
                    'add' => 'admin_destination_add',
                    'field_rel' => array('id','label'),
                    'values' => $entity->getTransferTwo() ? $entity->getTransferTwo() : array(),
                    'translate' => false,
                    'editLink' => array(
                        'type' => 'destination',
                        'path' => $entity->getTransferTwo() ? array('id' => $entity->getTransferTwo()->getId()) : array(),
                    ),
                ),
                'transfer_three' => array(
                    'label' => 'adm.field.transfer_three',
                    'type' => 'relation_one',
                    'autocomplete' => 'product',
                    'autocomplete_path' => 'admin_autocomplete_transfer_produit',
                    'name' => $lng.'[transfer_three]',
                    'add' => 'admin_destination_add',
                    'field_rel' => array('id','label'),
                    'values' => $entity->getTransferThree() ? $entity->getTransferThree() : array(),
                    'translate' => false,
                    'editLink' => array(
                        'type' => 'destination',
                        'path' => $entity->getTransferThree() ? array('id' => $entity->getTransferThree()->getId()) : array(),
                    ),
                ),
				'separator_places' => array(
					'label' => 'adm.field.places',
					'type' => 'separator',
					'translate' => false
				),
				// MANY TO MANY
				'destination' => array(
					'label' => 'adm.field.destination',
					'type' => 'relation_many',
					'autocomplete' => 'destination',
					'autocomplete_path' => 'admin_autocomplete_destination',
					'name' => $lng.'[destination][]',
					'add' => 'admin_destination_add',
					'field_rel' => array('id','image','name'),
					'values' => $entity->getDestination() ? $entity->getDestination() : array(),
					'translate' => false,
					'editLink' => array(
						'type' => 'destination',
						'path' => array(),
					),
					//'sortable' => true
				),
				'hotel' => array(
					'label' => 'adm.field.hotel',
					'type' => 'relation_many',
					'autocomplete' => 'hotel',
					'autocomplete_path' => 'admin_autocomplete_hotel',
					'name' => $lng.'[hotel][]',
					'add' => 'admin_destination_add',
					'field_rel' => array('id','image','name'),
					'values' => $entity->getHotel() ? $entity->getHotel() : array(),
					'translate' => false,
					'editLink' => array(
						'type' => 'hotel',
						'path' => array(),
					),
				),
				'separator_images' => array(
					'label' => 'adm.field.images',
					'type' => 'separator',
					'translate' => false
				),
				'images' => array(
					'label' => 'adm.field.images',
					'type' => 'images',
					'name' => $lng.'[images][]',
					'values' => $imagesValue,
					'maximum' => false,
					'required' => false,
					'translate' => false
				)
			);

			//TECHNICAL INFORMATION
			$fieldset = 'translate';
			if ($lng == $this->_defaultLocale) {
				$fieldset = 'default';
			}
			$form[$fieldset][$lng] = $fields;
		}
		return $form;
	}


	//SAVE ENTITY
	private function saveEntity($data,$entity,$em){

		//SAVE ENTITY ACCORDING TO LOCALE
		foreach ($data as $localeName => $locale) {
			if (in_array($localeName,$this->_locales)) {
				$entity->setTranslatableLocale($localeName);

				/* BLOCK FOR TRANSLABLE VALUES*/
				if (!empty($data[$localeName]['name'])) {
                    $entity->translate($localeName,false)->setName(trim($data[$localeName]['name']));
				}
                if (!empty($data[$localeName]['title'])) {
                    $entity->translate($localeName,false)->setTitle(trim($data[$localeName]['title']));
                }
				if (!empty($data[$localeName]['body'])) {
                    $entity->translate($localeName,false)->setBody(trim($data[$localeName]['body']));
				}
				if (!empty($data[$localeName]['slug'])) {
                    $entity->translate($localeName,false)->setSlug(trim($data[$localeName]['slug']));
				}

				/* BLOCK FOR NON TRANSLATED VALUES*/
				if ($localeName == $this->_defaultLocale) {
					// NON TRANSLATED STRING
                    //print_r($data[$localeName]['name']); exit;
                    if (!empty($data[$localeName]['name'])) {
                        $entity->setLabel($data[$localeName]['name']);
                    }
                    $day_produit = $em->getRepository('AppBundle\Entity\DayToProduct')->findBy( array( 'day'=> $entity) );
                    foreach($day_produit as $value) {
                        $em->remove($value);
                    }
                    if (!empty($data[$localeName]['day_produit'])) {
                        $array=array();
                        $position=0;
                        $atribute_array=array('Visa','Train','Assurance','TicketsDeMusee');
                        foreach($data[$localeName]['day_produit'] as $value){
                            $class = 'AppBundle\Entity\\'.$value['entity'];
                            $eclass = $em->getRepository($class)->findOneBy( array( 'id'=> $value['id']) );
                            if($eclass){
                                $ajouter_produit= new DayToProduct();
                                $ajouter_produit->setDay($entity);
                                $ajouter_produit->setPosition($position);
                                foreach($atribute_array as $item){
                                    $method = 'set'.$item;
                                    if ($item==$value['entity']){
                                        $ajouter_produit->$method($eclass);
                                    }else{
                                        $ajouter_produit->$method(NULL);
                                    }
                                }
                                $em->persist($ajouter_produit);
                                $array[]=$ajouter_produit;
                            }
                            $position=$position+1;
                        }
                        $entity->setAjouterProduit($array);
                    } else {
                        $entity->setAjouterProduit(array());
                    }

                    if (!empty($data[$localeName]['alternative_day'])) {
                        $alternative_day = $em->getRepository('AppBundle\Entity\Day')->findBy( array( 'id'=> $data[$localeName]['alternative_day']) );
                        $entity->setAlternativeDay($alternative_day);
                    } else {
                        $entity->setAlternativeDay(null);
                    }

					// IMAGE
					if (!empty($data[$localeName]['images'])) {
						$otherImages = array_filter($data[$localeName]['images'], function($value) { return $value !== ''; });
						$otherImage = $em->getRepository('AppBundle\Entity\Files')->findBy( array( 'id'=> $otherImages ) );
						$entity->setImages($otherImage);
					} else {
						$entity->setImages(null);
					}

					if (!empty($data[$localeName]['destination'])) {
						$destinations = $em->getRepository('AppBundle\Entity\Destination')->findBy( array( 'id'=> $data[$localeName]['destination'] ) );
						$entity->setDestination($destinations);
					} else {
						$entity->setDestination(null);
					}

					if (!empty($data[$localeName]['hotel'])) {
						$hotels = $em->getRepository('AppBundle\Entity\Hotel')->findBy( array( 'id'=> $data[$localeName]['hotel'] ) );
						$entity->setHotel($hotels);
					} else {
						$entity->setHotel(null);
					}

                    if (!empty($data[$localeName]['transfer_one'])) {
                        $transfer_one = $em->getRepository('AppBundle\Entity\Transferts')->findOneBy( array( 'id'=> $data[$localeName]['transfer_one']) );
                        $entity->setTransferOne($transfer_one);
                    } else {
                        $entity->setTransferOne(null);
                    }
                    if (!empty($data[$localeName]['transfer_two'])) {
                        $transfer_two = $em->getRepository('AppBundle\Entity\Transferts')->findOneBy( array( 'id'=> $data[$localeName]['transfer_two']) );
                        $entity->setTransferTwo($transfer_two);
                    } else {
                        $entity->setTransferTwo(null);
                    }
                    if (!empty($data[$localeName]['transfer_three'])) {
                        $transfer_three = $em->getRepository('AppBundle\Entity\Transferts')->findOneBy( array( 'id'=> $data[$localeName]['transfer_three']) );
                        $entity->setTransferThree($transfer_three);
                    } else {
                        $entity->setTransferThree(null);
                    }
                    /*$label = 'Trai_';

                    if($entity->getDestination()){
                        $label=$label.substr($entity->getDestination()->translate($localeName, true)->getCity(), 0, 3).'_';
                    }*/
                    //$entity->setLabel($label);
				}
                $entity->mergeNewTranslations();

				$em->persist($entity);
				$em->flush();
			}
		}


		return true;
	}
}
