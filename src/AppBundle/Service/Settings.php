<?php
namespace AppBundle\Service;


class Settings {

	private $em;

	public function __construct()
	{
		global $kernel;
		$this->em = $kernel->getContainer()->get('doctrine')->getEntityManager();
	}

	public function get ($key) {
		return $this->em->getRepository('AppBundle\Entity\Settings')->findOneBy(array('name'=>$key));
	}

}