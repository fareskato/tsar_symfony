<?php
namespace AppBundle\Service;

class Locales {

	public function getLocales(){
		global $kernel;
		$locales = $kernel->getContainer('parameters')->getParameter('app.locales');
		return explode('|',$locales);
	}

	public function getDefaultLocale(){
		global $kernel;
		return $kernel->getContainer('parameters')->getParameter('locale');
	}

	public function getDomains(){
		global $kernel;
		$em = $kernel->getContainer()->get('doctrine')->getEntityManager();

		return $em->getRepository('AppBundle\Entity\BookDomain')->findAll();
	}
}