<?php
namespace AppBundle\Service;


use Symfony\Component\HttpFoundation\RequestStack;

class Slider {

	private $em;

	protected $requestStack;

	public function __construct(RequestStack $requestStack)
	{
		global $kernel;
		$this->em = $kernel->getContainer()->get('doctrine')->getEntityManager();

		$this->requestStack = $requestStack;

	}

	public function getDesktop(){
		$locale = $this->requestStack->getCurrentRequest()->getLocale();
		$sl = $this->em->getRepository('AppBundle\Entity\FrontSlider')->createQueryBuilder('u')
			->where('u.position = :position')
			->andWhere('t.locale = :locale')
			->andWhere('t.active = :active')
			->join('u.translations','t')
			->setParameters(
				array(
					'position'=> 'desktop',
					'locale'=> $locale,
					'active'=> 1
				)
			)->orderBy('u.reorder', 'ASC')
			->getQuery()
			->getResult();

		return $sl;
	}

	public function getMobile(){
		$locale = $this->requestStack->getCurrentRequest()->getLocale();
		$sl = $this->em->getRepository('AppBundle\Entity\FrontSlider')->createQueryBuilder('u')
			->where('u.position = :position')
			->andWhere('t.locale = :locale')
			->andWhere('t.active = :active')
			->join('u.translations','t')
			->setParameters(
				array(
					'position'=> 'mobile',
					'locale'=> $locale,
					'active'=> 1
				)
			)->orderBy('u.reorder', 'ASC')
			->getQuery()
			->getResult();

		return $sl;
	}

}