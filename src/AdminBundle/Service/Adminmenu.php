<?php
namespace AdminBundle\Service;

class Adminmenu {

	public function getBooks() {
		global $kernel;
		$em = $kernel->getContainer()->get('doctrine')->getEntityManager();
		$entities = array();
		$meta = $em->getMetadataFactory()->getAllMetadata();
		foreach ($meta as $m) {
			if(preg_match('/Book/', $m->getName()) && !preg_match('/Translation/', $m->getName())){
				$entities[] = $em->getClassMetadata($m->getName())->getTableName();
			}
		}
		return $entities;
	}

    public function getHotels() {
        global $kernel;
        $em = $kernel->getContainer()->get('doctrine')->getEntityManager();
        $entities = array();
        $meta = $em->getMetadataFactory()->getAllMetadata();
        foreach ($meta as $m) {//echo $em->getClassMetadata($m->getName())->getTableName(); echo'<hr>';
            if(preg_match('/Hotel/', $m->getName()) && !preg_match('/Translation/', $m->getName()) && strpos($m->getName(), 'Book')===FALSE && strpos($m->getName(), 'Tarif')===FALSE){
                $entities[] = $em->getClassMetadata($m->getName())->getTableName();
            }
        }
        //print_r($entities); exit;
        return $entities;
    }
}