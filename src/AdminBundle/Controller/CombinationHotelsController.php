<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\CombinationHotels;
use AppBundle\Service\Locales;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CombinationHotelsController extends Controller
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
     * @Route("/combination_hotels", name="admin_combinationhotels")
     */
    public function indexAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

//		$data['data_list'] = $em->getRepository('AppBundle\Entity\CombinationHotels')->findBy(array(), array('id' => 'DESC'));
		$data['data_list'] = $em->getRepository('AppBundle\Entity\CombinationHotels')->findAllForAdminList($em,'AppBundle\Entity\CombinationHotels',$request);


		$data['data_fields'] = array('id','label');
		$data['data_title'] = 'adm.combinationhotels';

		$link = $this->generateUrl('admin_combinationhotels_add', array(), UrlGeneratorInterface::ABSOLUTE_PATH);


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
					'link' => 'admin_combinationhotels_edit',
					'class' => '',
					'confirm' => false
				),
				array(
					'name' => 'delete',
					'link' => 'admin_combinationhotels_delete',
					'class' => 'danger',
					'confirm' => 'adm.action.delete.confirm'
				),
			);

		$page = !empty($request->query->get('page')) ? intval($request->query->get('page')) : 1;
		$paginator = array(
			'currentFilters' => $request->query->all(),
			'currentPage' => $page,
			'paginationPath' => 'admin_combinationhotels',
			'showAlwaysFirstAndLast' => true,
			'lastPage' => ceil(count($data['data_list']) / $this->_itemsOnPage),
		);

		$data['data_list'] = array_slice($data['data_list'], ($page * $this->_itemsOnPage - $this->_itemsOnPage), $this->_itemsOnPage);
		$data = array_merge($data,$paginator);

        return $this->render('AdminBundle:Default:list.html.twig',$data);
    }

    /**
     * @Route("/combination_hotels/add", name="admin_combinationhotels_add")
     * @Route("/combination_hotels/edit/{id}", defaults={"id" = 0}, name="admin_combinationhotels_edit")
     */
    public function editAction($id = 0, Request $request){
        $em = $this->getDoctrine()->getManager();



        $entity = new CombinationHotels();
		$entity->setDefaultLocale($this->_defaultLocale);
		if (!empty($id)) {
			$entity = $em->getRepository('AppBundle\Entity\CombinationHotels')->findOneBy(array('id'=>$id));
		}

        $data['entity'] = $entity;

		//TITLE FOR PAGE
		$data['data_title'] = (!empty($data['entity']->translate()->getName()) ? $data['entity']->translate()->getName() : $this->get('translator')->trans('adm.action.new'));

        $data['data_type'] =  'adm.combinationhotels.combinationhotels_name';

        $data['data_buttons'] = array(
            array(
                'name' => 'save',
                'link' => 'admin_combinationhotels_edit',
                'class' => 'primary',
                'button' => true,
                'button_type' => 'submit'
            ),
            array(
                'name' => 'cancel',
                'link' => !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->generateUrl('admin_combinationhotels', array(), UrlGeneratorInterface::ABSOLUTE_PATH),
                'class' => 'default',
            )
        );

        if ($request->isMethod('POST')) {

            $data = $request->request->all();

            $this->saveEntity($data,$entity,$em);
            return $this->redirectToRoute('admin_combinationhotels', array());
        }

        $data['form'] = $this->createEntityForm($entity,$id,$em);


        return $this->render('AdminBundle:Default:form.html.twig',$data);
    }

    /**
     * @Route("/combination_hotels/delete/{id}", name="admin_combinationhotels_delete")
     */
    public function deleteAction($id){
        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository('AppBundle\Entity\CombinationHotels')->findOneBy(array('id' => intval($id)));
        if ($data) {
            $em->remove($data);
            $em->flush();
        }


        return !empty($_SERVER['HTTP_REFERER']) ? $this->redirect($_SERVER['HTTP_REFERER']) : $this->generateUrl('admin_combinationhotels', UrlGeneratorInterface::ABSOLUTE_PATH);

    }

	private function createEntityForm($entity, $id = 0, $em) {
		if (!empty($id)) {
			$form['action'] = $this->generateUrl('admin_combinationhotels_edit', array('id' => $id), UrlGeneratorInterface::ABSOLUTE_PATH);
		} else{
			$form['action'] = $this->generateUrl('admin_combinationhotels_add', array(), UrlGeneratorInterface::ABSOLUTE_PATH);
		}

		$form['id'] = $id;
		$form['form_id'] = 'form_'.md5('combinationhotels');
		$form['separator'] = true;



		foreach($this->_locales as $lng) {
			$fields = array(
				/*'label' => array(
					'label' => 'adm.field.label',
					'type' => 'text',
					'name' => $lng.'[label]',
					'required' => false,
					'value' => $entity ? $entity->getLabel() : '',
					'translate' => true
				),*/
                'special_offer' => array(
                    'label' => 'adm.field.special_offer',
                    'type' => 'checkbox',
                    'name' => $lng.'[special_offer]',
                    'value' => 1,
                    'checked' => $entity ? ($entity->isSpecialOffer() ? 1 : 0) : 0,
                    'translate' => true
                ),

                'name' => array(
                    'label' => 'adm.field.name',
                    'type' => 'text',
                    'name' => $lng.'[name]',
                    'required' => ($lng != $this->_defaultLocale ? false : true),
                    'value' => $entity ? $entity->translate($lng,($lng == $this->_defaultLocale ? true : false))->getName() : '',
                    'translate' => true
                ),

                'separator_hotel' => array(
                    'label' => 'adm.field.hotel',
                    'type' => 'separator',
                    'translate' => false
                ),
                'hotel' => array(
                    'label' => 'adm.field.hotel',
                    'type' => 'relation_many',
                    'autocomplete' => 'hotel',
                    'autocomplete_path' => 'admin_autocomplete_hotel',
                    'name' => $lng.'[hotel][]',
                    'add' => 'admin_hotel_add',
                    'field_rel' => array('id','name'),
                    'values' => $entity->getCombinationHotel() ? $entity->getCombinationHotel() : array(),
                    'sortable' => false,
                    'translate' => false,
                    'editLink' => array(
                        'type' => 'destination',
                        'path' => array(),
                    ),
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



				/* BLOCK FOR NON TRANSLATED VALUES*/
				if ($localeName == $this->_defaultLocale) {

                    $entity->setSpecialOffer(!empty($data[$localeName]['special_offer']) ? 1 : 0);

                    if (!empty($data[$localeName]['name'])) {
                        $entity->setLabel(trim($data[$localeName]['name']));
                    }

                    if (!empty($data[$localeName]['hotel'])) {
                        $related_content = $em->getRepository('AppBundle\Entity\Hotel')->findBy( array( 'id'=> $data[$localeName]['hotel']) );
                        $entity->setCombinationHotel($related_content);
                    } else {
                        $entity->setCombinationHotel(null);
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

				}
                $entity->mergeNewTranslations();
            }
            $em->persist($entity);
            $em->flush();
        }

        return true;
    }
}
