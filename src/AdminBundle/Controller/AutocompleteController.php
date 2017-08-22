<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\Menu;
use AppBundle\Service\Locales;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Query;
use Gedmo\Translatable\Query\TreeWalker\TranslationWalker;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AutocompleteController extends Controller
{
	
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
	 * @Route("/autocomplete/destination", name="admin_autocomplete_destination")
	 */
	public function autocompleteDestination(Request $request){
		$return = array();
		if ($request->isMethod('POST')) {




			$em = $this->getDoctrine()->getManager();

			$data = $request->request->all();
			$search = trim($data['search']);


			// FIRST SEARCH IN DEFAULT VALUES
			$parentsDefault = $em->getRepository('AppBundle\Entity\Destination')->createQueryBuilder('u')
                ->join('u.translations','t','WITH','t.locale = :locale_default')
                ->leftJoin('u.translations','t2','WITH','t2.locale = :locale')
				->Where('t.name LIKE :name')
                ->setParameters(
                    array(
                        'locale_default'=> $this->_defaultLocale,
                        'locale'=> $request->getLocale(),
                        'name'=> '%'.$search.'%',
                    )
                )
				->orderBy('u.id', 'ASC')
				->getQuery()
				->getResult();
            foreach($parentsDefault as $loc) {
                $div = array();
                foreach($data['fields'] as $f) {
                    $div[] = $this->get('twig')->render('@Admin/Default/form_field/field_autocomplete_td.html.twig', array('entity' => $loc,'field' => $f));
                }
                $return[] = array(
                    'data' => json_encode($div),
                    'id' => $loc->getId(),
                    'value'=> $loc->getId() . ' - ' . $loc->translate()->getName(),
					'link' => $this->generateUrl('admin_destination_edit', array('id' => $loc->getId()))
                );
            }

		}

		echo json_encode($return);
		die();

	}

	/**
	 * @Route("/autocomplete/hotel", name="admin_autocomplete_hotel")
	 */
	public function autocompleteHotel(Request $request){
		/*$return = array();
		if ($request->isMethod('POST')) {

			$em = $this->getDoctrine()->getManager();

			$data = $request->request->all();
			$search = trim($data['search']);

			$parents = array();

			// FIRST SEARCH IN DEFAULT VALUES
			$parentsDefault = $em->getRepository('AppBundle\Entity\Hotel')->createQueryBuilder('u')
				->where('u.id = :id')
				->orWhere('u.name LIKE :name')
				->setParameter('id', $search)
				->setParameter('name', '%'.$search.'%')
				->orderBy('u.id', 'ASC')
				->getQuery()
				->getResult();
			foreach($parentsDefault as $l) {
				$parents[$l->getId()] = $l;
			}


			// THEN SEARCH IN TRANSLATED VALUES
			$config = $this->container->get('doctrine')->getManager()->getConfiguration();
			if ($config->getCustomHydrationMode(TranslationWalker::HYDRATE_OBJECT_TRANSLATION) === null) {
				$config->addCustomHydrationMode(
					TranslationWalker::HYDRATE_OBJECT_TRANSLATION,
					'Gedmo\\Translatable\\Hydrator\\ORM\\ObjectHydrator'
				);
			}

			$parentsTranslated = $em->getRepository('AppBundle\Entity\Hotel')->createQueryBuilder('u')
				->where('u.name LIKE :name')
				->setParameter('name', '%'.$search.'%')
				->orderBy('u.id', 'ASC')
				->getQuery()
				->setHint(
					\Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
					'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
				)
				->setHint(
					\Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE,
					$request->getLocale() // take locale from session or request etc.
				)
				->setHydrationMode(TranslationWalker::HYDRATE_OBJECT_TRANSLATION)
				->setHint(Query::HINT_REFRESH, true)
				->execute(null, 1);

			foreach($parentsTranslated as $l) {
				$parents[$l->getId()] = $l;
			}
			ksort($parents);

			// GET MERGED ARRAY FOR ANSWER!!!!!
			foreach($parents as $loc) {
				$div = array();
				foreach($data['fields'] as $f) {
					$div[] = $this->get('twig')->render('@Admin/Default/form_field/field_autocomplete_td.html.twig', array('entity' => $loc,'field' => $f));
				}
				$return[] = array(
					'data' => json_encode($div),
					'id' => $loc->getId(),
					'value'=> $loc->getId() . ' - ' . $loc->getName(),
					'link' => $this->generateUrl('admin_hotel_edit', array('id' => $loc->getId()))
				);
			}

		}

		echo json_encode($return);
		die();*/
        $return = array();
        if ($request->isMethod('POST')) {

            $em = $this->getDoctrine()->getManager();

            $data = $request->request->all();
            $search = trim($data['search']);

            $locations = $em->getRepository('AppBundle\Entity\Hotel')->createQueryBuilder('u')
                ->join('u.translations','t','WITH','t.locale = :locale_default')
                ->leftJoin('u.translations','t2','WITH','t2.locale = :locale')
                ->where('t.name LIKE :name')
                ->orWhere('t2.name LIKE :name')
                ->setParameters(
                    array(
                        'locale_default'=> $this->_defaultLocale,
                        'locale'=> $request->getLocale(),
                        'name'=> '%'.$search.'%',
                    )
                )->orderBy('u.id', 'ASC')
                ->getQuery()
                ->getResult();

            // GET MERGED ARRAY FOR ANSWER!!!!!
            foreach($locations as $loc) {
                $div = array();
                foreach($data['fields'] as $f) {
                    $div[] = $this->get('twig')->render('@Admin/Default/form_field/field_autocomplete_td.html.twig', array('entity' => $loc,'field' => $f));
                }
                $return[] = array(
                    'data' => json_encode($div),
                    'id' => $loc->getId(),
                    'value'=> $loc->getId() . ' - ' . $loc->translate()->getName(),
                    'link' => $this->generateUrl('admin_hotel_edit', array('id' => $loc->getId()))
                );
            }
        }
        echo json_encode($return);
        die();

	}

	/**
	 * @Route("/autocomplete/location", name="admin_autocomplete_location")
	 */
	public function autocompleteLocation(Request $request){

		$return = array();
		if ($request->isMethod('POST')) {

			$em = $this->getDoctrine()->getManager();

			$data = $request->request->all();
			$search = trim($data['search']);

			$locations = $em->getRepository('AppBundle\Entity\Location')->createQueryBuilder('u')
				->join('u.translations','t','WITH','t.locale = :locale_default')
				->leftJoin('u.translations','t2','WITH','t2.locale = :locale')
				//->where('t.locale = :locale_default')
				//->andWhere('t2.locale = :locale')

				->where('t.name LIKE :name')
				->orWhere('t.street LIKE :street')
				->orWhere('t.city LIKE :city')
				->orWhere('t2.name LIKE :name')
				->orWhere('t2.street LIKE :street')
				->orWhere('t2.city LIKE :city')

				->setParameters(
					array(
						'locale_default'=> $this->_defaultLocale,
						'locale'=> $request->getLocale(),
						'name'=> '%'.$search.'%',
						'street'=> '%'.$search.'%',
						'city'=> '%'.$search.'%'
					)
				)->orderBy('u.id', 'ASC')
				->getQuery()
				->getResult();

			// GET MERGED ARRAY FOR ANSWER!!!!!
			foreach($locations as $loc) {
				$div = array();
				foreach($data['fields'] as $f) {
					$div[] = $this->get('twig')->render('@Admin/Default/form_field/field_autocomplete_td.html.twig', array('entity' => $loc,'field' => $f));
				}
				$return[] = array(
					'data' => json_encode($div),
					'id' => $loc->getId(),
					'value'=> $loc->getId() . ' - ' . $loc->translate()->getName() . ' - ' . $loc->translate()->getStreet() . ' - ' . $loc->translate()->getCity(),
					'link' => $this->generateUrl('admin_location_edit', array('id' => $loc->getId()))
				);
			}


		}


		echo json_encode($return);
		die();
	}

	/**
	 * @Route("/autocomplete/metro", name="admin_autocomplete_metro")
	 */
	public function autocompleteMetro(Request $request){

        $return = array();
        if ($request->isMethod('POST')) {

            $em = $this->getDoctrine()->getManager();

            $data = $request->request->all();
            $search = trim($data['search']);

            $locations = $em->getRepository('AppBundle\Entity\BookMetro')->createQueryBuilder('u')
                ->join('u.translations','t','WITH','t.locale = :locale_default')
                ->leftJoin('u.translations','t2','WITH','t2.locale = :locale')
                ->where('t.name LIKE :name')
                ->orWhere('t2.name LIKE :name')
                ->setParameters(
                    array(
                        'locale_default'=> $this->_defaultLocale,
                        'locale'=> $request->getLocale(),
                        'name'=> '%'.$search.'%',
                    )
                )->orderBy('u.id', 'ASC')
                ->getQuery()
                ->getResult();

            // GET MERGED ARRAY FOR ANSWER!!!!!
            foreach($locations as $loc) {
                $div = array();
                foreach($data['fields'] as $f) {
                    $div[] = $this->get('twig')->render('@Admin/Default/form_field/field_autocomplete_td.html.twig', array('entity' => $loc,'field' => $f));
                }
                $return[] = array(
                    'data' => json_encode($div),
                    'id' => $loc->getId(),
                    'value'=> $loc->getId() . ' - ' . $loc->translate()->getName(),
					'link' => $this->generateUrl('admin_book_edit', array('book_name'=>'book_metro','id' => $loc->getId()))
                );
            }
        }
        echo json_encode($return);
        die();
	}

    /**
     * @Route("/autocomplete/service", name="admin_autocomplete_service")
     */
    public function autocompleteService(Request $request){
        $return = array();
        if ($request->isMethod('POST')) {

            $em = $this->getDoctrine()->getManager();

            $data = $request->request->all();
            $search = trim($data['search']);

            $locations = $em->getRepository('AppBundle\Entity\BookServices')->createQueryBuilder('u')
                ->join('u.translations','t','WITH','t.locale = :locale_default')
                ->leftJoin('u.translations','t2','WITH','t2.locale = :locale')
                ->where('t.name LIKE :name')
                ->orWhere('t2.name LIKE :name')
                ->setParameters(
                    array(
                        'locale_default'=> $this->_defaultLocale,
                        'locale'=> $request->getLocale(),
                        'name'=> '%'.$search.'%',
                    )
                )->orderBy('u.id', 'ASC')
                ->getQuery()
                ->getResult();

            // GET MERGED ARRAY FOR ANSWER!!!!!
            foreach($locations as $loc) {
                $div = array();
                foreach($data['fields'] as $f) {
                    $div[] = $this->get('twig')->render('@Admin/Default/form_field/field_autocomplete_td.html.twig', array('entity' => $loc,'field' => $f));
                }
                $return[] = array(
                    'data' => json_encode($div),
                    'id' => $loc->getId(),
                    'value'=> $loc->getId() . ' - ' . $loc->translate()->getName(),
					'link' => $this->generateUrl('admin_book_edit', array('book_name'=>'book_services','id' => $loc->getId()))
                );
            }
        }
        echo json_encode($return);
        die();
    }

    /**
 * @Route("/autocomplete/recreation", name="admin_autocomplete_recreation")
 */
    public function autocompleteRecreation(Request $request){
        $return = array();
        if ($request->isMethod('POST')) {

            $em = $this->getDoctrine()->getManager();

            $data = $request->request->all();
            $search = trim($data['search']);

            $locations = $em->getRepository('AppBundle\Entity\BookTypeRecreation')->createQueryBuilder('u')
                ->join('u.translations','t','WITH','t.locale = :locale_default')
                ->leftJoin('u.translations','t2','WITH','t2.locale = :locale')
                ->where('t.name LIKE :name')
                ->orWhere('t2.name LIKE :name')
                ->setParameters(
                    array(
                        'locale_default'=> $this->_defaultLocale,
                        'locale'=> $request->getLocale(),
                        'name'=> '%'.$search.'%',
                    )
                )->orderBy('u.id', 'ASC')
                ->getQuery()
                ->getResult();

            // GET MERGED ARRAY FOR ANSWER!!!!!
            foreach($locations as $loc) {
                $div = array();
                foreach($data['fields'] as $f) {
                    $div[] = $this->get('twig')->render('@Admin/Default/form_field/field_autocomplete_td.html.twig', array('entity' => $loc,'field' => $f));
                }
                $return[] = array(
                    'data' => json_encode($div),
                    'id' => $loc->getId(),
                    'value'=> $loc->getId() . ' - ' . $loc->translate()->getName(),
					'link' => $this->generateUrl('admin_book_edit', array('book_name'=>'book_type_recreation','id' => $loc->getId()))
                );
            }
        }
        echo json_encode($return);
        die();

    }

    /**
     * @Route("/autocomplete/day", name="admin_autocomplete_day")
     */
    public function autocompleteDay(Request $request){
        $return = array();
        if ($request->isMethod('POST')) {

            $em = $this->getDoctrine()->getManager();

            $data = $request->request->all();
            $search = trim($data['search']);

            $locations = $em->getRepository('AppBundle\Entity\Day')->createQueryBuilder('u')
                ->join('u.translations','t','WITH','t.locale = :locale_default')
                ->leftJoin('u.translations','t2','WITH','t2.locale = :locale')
                ->where('t.name LIKE :name')
                ->orWhere('t2.name LIKE :name')
                ->setParameters(
                    array(
                        'locale_default'=> $this->_defaultLocale,
                        'locale'=> $request->getLocale(),
                        'name'=> '%'.$search.'%',
                    )
                )->orderBy('u.id', 'ASC')
                ->getQuery()
                ->getResult();

            // GET MERGED ARRAY FOR ANSWER!!!!!
            foreach($locations as $loc) {
                $div = array();
                foreach($data['fields'] as $f) {
                    $div[] = $this->get('twig')->render('@Admin/Default/form_field/field_autocomplete_td.html.twig', array('entity' => $loc,'field' => $f));
                }
                $return[] = array(
                    'data' => json_encode($div),
                    'id' => $loc->getId(),
                    'value'=> $loc->getId() . ' - ' . $loc->translate()->getName(),
					'link' => $this->generateUrl('admin_day_edit', array('id' => $loc->getId()))
                );
            }
        }
        echo json_encode($return);
        die();
    }

    /**
 * @Route("/autocomplete/extension", name="admin_autocomplete_extension")
 */
    public function autocompleteExtension(Request $request){
        $return = array();
        if ($request->isMethod('POST')) {

            $em = $this->getDoctrine()->getManager();

            $data = $request->request->all();
            $search = trim($data['search']);

            $locations = $em->getRepository('AppBundle\Entity\Extension')->createQueryBuilder('u')
                ->join('u.translations','t','WITH','t.locale = :locale_default')
                ->leftJoin('u.translations','t2','WITH','t2.locale = :locale')
                ->where('t.name LIKE :name')
                ->orWhere('t2.name LIKE :name')
                ->setParameters(
                    array(
                        'locale_default'=> $this->_defaultLocale,
                        'locale'=> $request->getLocale(),
                        'name'=> '%'.$search.'%',
                    )
                )->orderBy('u.id', 'ASC')
                ->getQuery()
                ->getResult();

            // GET MERGED ARRAY FOR ANSWER!!!!!
            foreach($locations as $loc) {
                $div = array();
                foreach($data['fields'] as $f) {
                    $div[] = $this->get('twig')->render('@Admin/Default/form_field/field_autocomplete_td.html.twig', array('entity' => $loc,'field' => $f));
                }
                $return[] = array(
                    'data' => json_encode($div),
                    'id' => $loc->getId(),
                    'value'=> $loc->getId() . ' - ' . $loc->translate()->getName(),
					'link' => $this->generateUrl('admin_extension_edit', array('id' => $loc->getId()))
                );
            }
        }
        echo json_encode($return);
        die();
    }

  /**
   * @Route("/autocomplete/bookedproduct", name="admin_autocomplete_booked_product")
   */
  public function autocompleteBookedproduct(Request $request){
    $return = array();
    if ($request->isMethod('POST')) {

      $em = $this->getDoctrine()->getManager();

      $data = $request->request->all();
      $search = trim($data['search']);

      $extension = $em->getRepository('AppBundle\Entity\Extension')->createQueryBuilder('u')
        ->join('u.translations','t','WITH','t.locale = :locale_default')
        ->leftJoin('u.translations','t2','WITH','t2.locale = :locale')
        ->where('t.name LIKE :name')
        ->orWhere('t2.name LIKE :name')
        ->setParameters(
          array(
            'locale_default'=> $this->_defaultLocale,
            'locale'=> $request->getLocale(),
            'name'=> '%'.$search.'%',
          )
        )->orderBy('u.id', 'ASC')
        ->getQuery()
        ->getResult();
      //print_r(count($extension)); echo'<hr>';
      $visit = $em->getRepository('AppBundle\Entity\Visit')->createQueryBuilder('u')
        ->join('u.translations','t','WITH','t.locale = :locale_default')
        ->leftJoin('u.translations','t2','WITH','t2.locale = :locale')
        ->where('t.name LIKE :name')
        ->orWhere('t2.name LIKE :name')
        ->setParameters(
          array(
            'locale_default'=> $this->_defaultLocale,
            'locale'=> $request->getLocale(),
            'name'=> '%'.$search.'%',
          )
        )->orderBy('u.id', 'ASC')
        ->getQuery()
        ->getResult();
      //print_r(count($visit)); echo'<hr>';
      $voyage = $em->getRepository('AppBundle\Entity\Voyage')->createQueryBuilder('u')
        ->join('u.translations','t','WITH','t.locale = :locale_default')
        ->leftJoin('u.translations','t2','WITH','t2.locale = :locale')
        ->where('t.name LIKE :name')
        ->orWhere('t2.name LIKE :name')
        ->setParameters(
          array(
            'locale_default'=> $this->_defaultLocale,
            'locale'=> $request->getLocale(),
            'name'=> '%'.$search.'%',
          )
        )->orderBy('u.id', 'ASC')
        ->getQuery()
        ->getResult();
      //print_r(count($voyage)); echo'<hr>'; exit;
      $event = $em->getRepository('AppBundle\Entity\Event')->createQueryBuilder('u')
        ->join('u.translations','t','WITH','t.locale = :locale_default')
        ->leftJoin('u.translations','t2','WITH','t2.locale = :locale')
        ->where('t.name LIKE :name')
        ->orWhere('t2.name LIKE :name')
        ->setParameters(
          array(
            'locale_default'=> $this->_defaultLocale,
            'locale'=> $request->getLocale(),
            'name'=> '%'.$search.'%',
          )
        )->orderBy('u.id', 'ASC')
        ->getQuery()
        ->getResult();
      //print_r(count($voyage)); echo'<hr>'; exit;
      $summ_array=array();
      $summ_array=array_merge($extension, $visit, $voyage, $event);
      // GET MERGED ARRAY FOR ANSWER!!!!!
      foreach($summ_array as $loc) {
        $div = array();
        foreach($data['fields'] as $f) {
          $div[] = $this->get('twig')->render('@Admin/Default/form_field/field_autocomplete_td.html.twig', array('entity' => $loc,'field' => $f));
        }
        $return[] = array(
          'data' => json_encode($div),
          'id' => $loc->getId(),
          'entity' => $loc->getClass(),
          'value'=> $loc->getId() . ' - ' . $loc->getClass() . ' - ' . $loc->translate()->getName(),
			'link' => $this->generateUrl('admin_'.strtolower($loc->getClass()).'_edit', array('id' => $loc->getId()))
        );
      }
    }
    echo json_encode($return);
    die();
  }

    /**
     * @Route("/autocomplete/relatedproduct", name="admin_autocomplete_related_product")
     */
    public function autocompleteRelatedproduct(Request $request){
        $return = array();
        if ($request->isMethod('POST')) {

            $em = $this->getDoctrine()->getManager();

            $data = $request->request->all();
            $search = trim($data['search']);

            $extension = $em->getRepository('AppBundle\Entity\Extension')->createQueryBuilder('u')
                ->join('u.translations','t','WITH','t.locale = :locale_default')
                ->leftJoin('u.translations','t2','WITH','t2.locale = :locale')
                ->where('t.name LIKE :name')
                ->orWhere('t2.name LIKE :name')
                ->setParameters(
                    array(
                        'locale_default'=> $this->_defaultLocale,
                        'locale'=> $request->getLocale(),
                        'name'=> '%'.$search.'%',
                    )
                )->orderBy('u.id', 'ASC')
                ->getQuery()
                ->getResult();
            //print_r(count($extension)); echo'<hr>';
            $visit = $em->getRepository('AppBundle\Entity\Visit')->createQueryBuilder('u')
                ->join('u.translations','t','WITH','t.locale = :locale_default')
                ->leftJoin('u.translations','t2','WITH','t2.locale = :locale')
                ->where('t.name LIKE :name')
                ->orWhere('t2.name LIKE :name')
                ->setParameters(
                    array(
                        'locale_default'=> $this->_defaultLocale,
                        'locale'=> $request->getLocale(),
                        'name'=> '%'.$search.'%',
                    )
                )->orderBy('u.id', 'ASC')
                ->getQuery()
                ->getResult();
            //print_r(count($visit)); echo'<hr>';
            $voyage = $em->getRepository('AppBundle\Entity\Voyage')->createQueryBuilder('u')
                ->join('u.translations','t','WITH','t.locale = :locale_default')
                ->leftJoin('u.translations','t2','WITH','t2.locale = :locale')
                ->where('t.name LIKE :name')
                ->orWhere('t2.name LIKE :name')
                ->setParameters(
                    array(
                        'locale_default'=> $this->_defaultLocale,
                        'locale'=> $request->getLocale(),
                        'name'=> '%'.$search.'%',
                    )
                )->orderBy('u.id', 'ASC')
                ->getQuery()
                ->getResult();


            $hotel = $em->getRepository('AppBundle\Entity\Hotel')->createQueryBuilder('u')
                ->join('u.translations','t','WITH','t.locale = :locale_default')
                ->leftJoin('u.translations','t2','WITH','t2.locale = :locale')
                ->where('t.name LIKE :name')
                ->orWhere('t2.name LIKE :name')
                ->setParameters(
                    array(
                        'locale_default'=> $this->_defaultLocale,
                        'locale'=> $request->getLocale(),
                        'name'=> '%'.$search.'%',
                    )
                )->orderBy('u.id', 'ASC')
                ->getQuery()
                ->getResult();
            //print_r(count($voyage)); echo'<hr>'; exit;
            $summ_array=array();
            $summ_array=array_merge($extension, $visit, $voyage, $hotel);
            // GET MERGED ARRAY FOR ANSWER!!!!!
            foreach($summ_array as $loc) {
                $div = array();
                foreach($data['fields'] as $f) {
                    $div[] = $this->get('twig')->render('@Admin/Default/form_field/field_autocomplete_td.html.twig', array('entity' => $loc,'field' => $f));
                }
                $return[] = array(
                    'data' => json_encode($div),
                    'id' => $loc->getId(),
                    'entity' => $loc->getClass(),
                    'value'=> $loc->getId() . ' - ' . $loc->getClass() . ' - ' . $loc->translate()->getName(),
					'link' => $this->generateUrl('admin_'.strtolower($loc->getClass()).'_edit', array('id' => $loc->getId()))
                );
            }
        }
        echo json_encode($return);
        die();
    }

    /**
     * @Route("/autocomplete/ajouterproduit", name="admin_autocomplete_ajouter_produit")
     */
    public function autocompleteAjouterproduit(Request $request){
        $return = array();
        if ($request->isMethod('POST')) {

            $em = $this->getDoctrine()->getManager();

            $data = $request->request->all();
            $search = trim($data['search']);

            $Assurance = $em->getRepository('AppBundle\Entity\Assurance')->createQueryBuilder('u')
                ->where('u.label LIKE :name')
                 ->setParameters(
                    array(
                        'name' => '%' . $search . '%',
                    )
                )->orderBy('u.id', 'ASC')
                ->getQuery()
                ->getResult();

            $Visa = $em->getRepository('AppBundle\Entity\Visa')->createQueryBuilder('u')
                ->where('u.label LIKE :name')
                ->setParameters(
                    array(
                        'name' => '%' . $search . '%',
                    )
                )->orderBy('u.id', 'ASC')
                ->getQuery()
                ->getResult();

            $Train= $em->getRepository('AppBundle\Entity\Train')->createQueryBuilder('u')
                ->where('u.label LIKE :name')
                ->setParameters(
                    array(
                        'name' => '%' . $search . '%',
                    )
                )->orderBy('u.id', 'ASC')
                ->getQuery()
                ->getResult();
            $TicketsDeMusee= $em->getRepository('AppBundle\Entity\TicketsDeMusee')->createQueryBuilder('u')
                ->where('u.label LIKE :name')
                ->setParameters(
                    array(
                        'name' => '%' . $search . '%',
                    )
                )->orderBy('u.id', 'ASC')
                ->getQuery()
                ->getResult();

            $GuideTouristique= $em->getRepository('AppBundle\Entity\GuideTouristique')->createQueryBuilder('u')
                ->where('u.label LIKE :name')
                ->setParameters(
                    array(
                        'name' => '%' . $search . '%',
                    )
                )->orderBy('u.id', 'ASC')
                ->getQuery()
                ->getResult();

            /*$AutreProduct= $em->getRepository('AppBundle\Entity\AutreProduct')->createQueryBuilder('u')
                ->where('u.label LIKE :name')
                ->setParameters(
                    array(
                        'name' => '%' . $search . '%',
                    )
                )->orderBy('u.id', 'ASC')
                ->getQuery()//->getSQL();
                ->getResult();*/
//print_r($AutreProduct[0]); exit;
            $summ_array=array();
            //print_r($data['fields']); exit;
            $summ_array=array_merge($Assurance, $Visa, $Train, $TicketsDeMusee, $GuideTouristique/*, $AutreProduct*/);
            foreach($summ_array as $loc) {
                $div = array();
                foreach($data['fields'] as $f) {
                    if($f=='name'){$f='label';}
                    $div[] = $this->get('twig')->render('@Admin/Default/form_field/field_autocomplete_td.html.twig', array('entity' => $loc,'field' => $f));
                }
                $return[] = array(
                    'data' => json_encode($div),
                    'id' => $loc->getId(),
                    'entity' => $loc->getClass(),
                    'value'=> $loc->getId() . ' - ' . $loc->getClass() . ' - ' . $loc->getLabel(),
                    'link' => $this->generateUrl('admin_'.strtolower($loc->getClass()).'_edit', array('id' => $loc->getId()))
                );
            }
        }
        echo json_encode($return);
        die();
    }

    /**
     * @Route("/autocomplete/transferproduit", name="admin_autocomplete_transfer_produit")
     */
    public function autocompleteTransferproduit(Request $request){
        $return = array();
        if ($request->isMethod('POST')) {

            $em = $this->getDoctrine()->getManager();

            $data = $request->request->all();
            $search = trim($data['search']);

            $Transferts= $em->getRepository('AppBundle\Entity\Transferts')->createQueryBuilder('u')
                ->where('u.label LIKE :name')
                ->setParameters(
                    array(
                        'name' => '%' . $search . '%',
                    )
                )->orderBy('u.id', 'ASC')
                ->getQuery()
                ->getResult();

            $summ_array=array();
            //print_r($data['fields']); exit;
            $summ_array=array_merge($Transferts);
            foreach($summ_array as $loc) {
                $div = array();
                foreach($data['fields'] as $f) { //print_r($f); echo"\n";
                    if($f=='name'){$f='label';}
                    $div[] = $this->get('twig')->render('@Admin/Default/form_field/field_autocomplete_td.html.twig', array('entity' => $loc,'field' => $f));
                }
                $return[] = array(
                    'data' => json_encode($div),
                    'id' => $loc->getId(),
                    'entity' => $loc->getClass(),
                    'value'=> $loc->getId() . ' - ' . $loc->getClass() . ' - ' . $loc->getLabel(),
                    'link' => $this->generateUrl('admin_'.strtolower($loc->getClass()).'_edit', array('id' => $loc->getId()))
                );
            }
        }//exit;
        echo json_encode($return);
        die();
    }

    /**
     * @Route("/autocomplete/combinationhotel", name="admin_autocomplete_combination_hotel")
     */
    public function autocompleteCombinationHotel(Request $request){
        $return = array();
        if ($request->isMethod('POST')) {

            $em = $this->getDoctrine()->getManager();

            $data = $request->request->all();
            $search = trim($data['search']);

            $Transferts= $em->getRepository('AppBundle\Entity\CombinationHotels')->createQueryBuilder('u')
                ->where('u.label LIKE :name')
                ->setParameters(
                    array(
                        'name' => '%' . $search . '%',
                    )
                )->orderBy('u.id', 'ASC')
                ->getQuery()
                ->getResult();

            $summ_array=array();
            //print_r($data['fields']); exit;
            $summ_array=array_merge($Transferts);
            foreach($summ_array as $loc) {
                $div = array();
                foreach($data['fields'] as $f) { //print_r($f); echo"\n";
                    if($f=='name'){$f='label';}
                    $div[] = $this->get('twig')->render('@Admin/Default/form_field/field_autocomplete_td.html.twig', array('entity' => $loc,'field' => $f));
                }
                $return[] = array(
                    'data' => json_encode($div),
                    'id' => $loc->getId(),
                    'entity' => $loc->getClass(),
                    'value'=> $loc->getId() . ' - ' . $loc->getClass() . ' - ' . $loc->getLabel(),
                    'link' => $this->generateUrl('admin_'.strtolower($loc->getClass()).'_edit', array('id' => $loc->getId()))
                );
            }
        }//exit;
        echo json_encode($return);
        die();
    }
}
