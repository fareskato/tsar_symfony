<?php
namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RequestStack;

class Menu {

	private $session;
	private $em;

	private $_topMenuId = 32;
	private $_mainMenuId = 38;
	private $_bottomMenuId1 = 17;
	private $_bottomMenuId2 = 21;
	private $_bottomMenuId3 = 25;


	public function __construct(Session $session, RequestStack $requestStack)
	{
		global $kernel;
		$this->em = $kernel->getContainer()->get('doctrine')->getEntityManager();
		$this->session = $session;

		$this->requestStack = $requestStack;
	}

	public function getMenutop(){
		return $this->em->getRepository('AppBundle\Entity\Menu')->findOneBy(array('id'=>$this->_topMenuId));
	}

	public function getMenumain(){
		$domain_id = $this->session->get('domain');

		$menu = $this->em->createQueryBuilder()
			->select('h')
			->from('AppBundle\Entity\Menutop', 'h')
			->innerJoin('h.type_domain','td')
			->innerJoin('h.translations','tr','WITH','tr.locale = :locale')
			->where('td.id = :domain')
			->andWhere('tr.active = :active')
			->andWhere('h.lvl = :lvl')
			->setParameters( array(
				'domain' => $domain_id,
				'active' => 1,
				'locale'=> $this->requestStack->getCurrentRequest()->getLocale(),
				'lvl'=> 0,
			))->orderBy('h.reorder','asc')
			->getQuery()
			->getResult();
		return $menu;
	}

	public function getMenubottomline1(){
		return $this->em->getRepository('AppBundle\Entity\Menu')->findOneBy(array('id'=>$this->_bottomMenuId1));
	}

	public function getMenubottomline2(){
		return $this->em->getRepository('AppBundle\Entity\Menu')->findOneBy(array('id'=>$this->_bottomMenuId2));
	}

	public function getMenubottomline3(){
		return $this->em->getRepository('AppBundle\Entity\Menu')->findOneBy(array('id'=>$this->_bottomMenuId3));
	}


}