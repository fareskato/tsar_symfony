<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\ProductPacks;
use AppBundle\Entity\ProductPacksToProduct;
use AppBundle\Entity\Train;
use AppBundle\Entity\Visa;
use AppBundle\Service\Locales;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ProductPacksController extends Controller
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
     * @Route("/productpacks", name="admin_productpacks")
     */
    public function indexAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

//		$data['data_list'] = $em->getRepository('AppBundle\Entity\ProductPacks')->findBy(array(), array('id' => 'DESC'));
		$data['data_list'] = $em->getRepository('AppBundle\Entity\ProductPacks')->findAllForAdminList($em,'AppBundle\Entity\ProductPacks',$request);


		$data['data_fields'] = array('id','label');
		$data['data_title'] = 'adm.productpacks';

		$link = $this->generateUrl('admin_productpacks_add', array(), UrlGeneratorInterface::ABSOLUTE_PATH);


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
					'link' => 'admin_productpacks_edit',
					'class' => '',
					'confirm' => false
				),
				array(
					'name' => 'delete',
					'link' => 'admin_productpacks_delete',
					'class' => 'danger',
					'confirm' => 'adm.action.delete.confirm'
				),
			);

		$page = !empty($request->query->get('page')) ? intval($request->query->get('page')) : 1;
		$paginator = array(
			'currentFilters' => $request->query->all(),
			'currentPage' => $page,
			'paginationPath' => 'admin_productpacks',
			'showAlwaysFirstAndLast' => true,
			'lastPage' => ceil(count($data['data_list']) / $this->_itemsOnPage),
		);

		$data['data_list'] = array_slice($data['data_list'], ($page * $this->_itemsOnPage - $this->_itemsOnPage), $this->_itemsOnPage);
		$data = array_merge($data,$paginator);

        return $this->render('AdminBundle:Default:list.html.twig',$data);
    }

    /**
     * @Route("/productpacks/add", name="admin_productpacks_add")
     * @Route("/productpacks/edit/{id}", defaults={"id" = 0}, name="admin_productpacks_edit")
     */
    public function editAction($id = 0, Request $request){
        $em = $this->getDoctrine()->getManager();



        $entity = new ProductPacks();
		$entity->setDefaultLocale($this->_defaultLocale);
		if (!empty($id)) {
			$entity = $em->getRepository('AppBundle\Entity\ProductPacks')->findOneBy(array('id'=>$id));
		}

        $data['entity'] = $entity;

		//TITLE FOR PAGE
		$data['data_title'] = (!empty($data['entity']->getLabel()) ? $data['entity']->getLabel() : $this->get('translator')->trans('adm.action.new'));

        $data['data_type'] =  'adm.productpacks.productpacks_name';

        $data['data_buttons'] = array(
            array(
                'name' => 'save',
                'link' => 'admin_productpacks_edit',
                'class' => 'primary',
                'button' => true,
                'button_type' => 'submit'
            ),
            array(
                'name' => 'cancel',
                'link' => !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->generateUrl('admin_productpacks', array(), UrlGeneratorInterface::ABSOLUTE_PATH),
                'class' => 'default',
            )
        );

        if ($request->isMethod('POST')) {

            $data = $request->request->all();

            $this->saveEntity($data,$entity,$em);
            return $this->redirectToRoute('admin_productpacks', array());
        }

        $data['form'] = $this->createEntityForm($entity,$id,$em);


        return $this->render('AdminBundle:Default:form.html.twig',$data);
    }

    /**
     * @Route("/productpacks/delete/{id}", name="admin_productpacks_delete")
     */
    public function deleteAction($id){
        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository('AppBundle\Entity\ProductPacks')->findOneBy(array('id' => intval($id)));
        if ($data) {
            $em->remove($data);
            $em->flush();
        }


        return !empty($_SERVER['HTTP_REFERER']) ? $this->redirect($_SERVER['HTTP_REFERER']) : $this->generateUrl('admin_productpacks', UrlGeneratorInterface::ABSOLUTE_PATH);

    }

	private function createEntityForm($entity, $id = 0, $em) {
		if (!empty($id)) {
			$form['action'] = $this->generateUrl('admin_productpacks_edit', array('id' => $id), UrlGeneratorInterface::ABSOLUTE_PATH);
		} else{
			$form['action'] = $this->generateUrl('admin_productpacks_add', array(), UrlGeneratorInterface::ABSOLUTE_PATH);
		}

		$form['id'] = $id;
		$form['form_id'] = 'form_'.md5('productpacks');
		$form['separator'] = true;
        //echo'<pre>'; print_r(get_class_methods($entity->getAjouterProduit()[0])); exit;

		foreach($this->_locales as $lng) {
			$fields = array(
				'label' => array(/* Будем считать, что это исходное поле Title*/
					'label' => 'adm.field.label',
					'type' => 'text',
					'name' => $lng.'[label]',
					'required' => false,
					'value' => $entity ? $entity->getLabel() : '',
					'translate' => false
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
                    'label' => 'adm.field.name',
                    'type' => 'text',
                    'name' => $lng.'[name]',
                    'required' => false,
                    'value' => $entity ? $entity->translate($lng,($lng == $this->_defaultLocale ? true : false))->getName() : '',
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
                'separator_product' => array(
                    'label' => 'adm.field.product',
                    'type' => 'separator',
                    'translate' => false
                ),
                'ajouter_produit' => array(
                    'label' => 'adm.field.ajouter_produit',
                    'type' => 'relation_many_entity',
                    'autocomplete' => 'extension',
                    'autocomplete_path' => 'admin_autocomplete_ajouter_produit',
                    'name' => $lng.'[ajouter_produit]',
                    'add' => false,
                    'field_rel' => array('id','entity','label'),
                    'values' => $entity->getAjouterProduit() ? $entity->getAjouterProduit() : array(),
                    'translate' => false,
                    'sortable' => 1,
                    'editLink' => array(
                        'type' =>  '', //$entity->getRelatedProduct() ? $entity->getRelatedProduct()->getClass() : '',
                        'path' => array(), //$entity->getRelatedProduct() ? array('id' => $entity->getRelatedProduct()->getId()) : array(),
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
				If(isset($data[$localeName]['active'])) {
                    $entity->translate($localeName, false)->setActive(!empty($data[$localeName]['active']) ? 1 : 0);
                }
                if (!empty($data[$localeName]['name'])) {
                    $entity->translate($localeName,false)->setName(trim($data[$localeName]['name']));
                }
				/* BLOCK FOR NON TRANSLATED VALUES*/
				if ($localeName == $this->_defaultLocale) {
                    $ajouter_produit = $em->getRepository('AppBundle\Entity\ProductPacksToProduct')->findBy( array( 'product_packs'=> $entity) );
                    foreach($ajouter_produit as $value) {
                        $em->remove($value);
                    }

                    if (!empty($data[$localeName]['ajouter_produit'])) {
                        $array=array();
                        $position=0;
                        $atribute_array=array('Visa','Train','Assurance');
                        foreach($data[$localeName]['ajouter_produit'] as $value){
                            $class = 'AppBundle\Entity\\'.$value['entity'];
                            $eclass = $em->getRepository($class)->findOneBy( array( 'id'=> $value['id']) );
                            if($eclass){
                                $ajouter_produit= new ProductPacksToProduct();
                                $ajouter_produit->setProductPacks($entity);
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
                    if (!empty($data[$localeName]['ville'])) {
                        $ville = $em->getRepository('AppBundle\Entity\Location')->findOneBy( array( 'id'=> $data[$localeName]['ville']) );
                        $entity->setVille($ville);
                    } else {
                        $entity->setVille(null);
                    }
                    if (!empty($data[$localeName]['label'])) {
                        $entity->setLabel(trim($data[$localeName]['label']));
                    }

				}
                $entity->mergeNewTranslations();
            }
            $em->persist($entity);
            $em->flush();
        }

        return true;
    }
}
