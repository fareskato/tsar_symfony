<?php
namespace AppBundle\Service;


class Search {

	private $em;
	private $translator;
	private $sphinx;


	public function __construct($sphinx,$em,$translator)
	{
		$this->em = $em;
		$this->translator = $translator;

		$this->sphinx = $sphinx;
		$this->sphinx->setLimits(0, 1000000);
	}

	public function search($str = '', $index = ''){
		if (!is_array($index)) {
			$index = array($index);
		}

		$result =  $this->sphinx->search($str, $index);

		return array();
	}

	public function searchAll($str = '') {
		$index = array('voyage','extension','visit','hotel','event');
		$allSearch = array();
		foreach($index as $type) {

			$result =  $this->sphinx->search($str, array('tsar_'.$type));
			foreach($result['matches'] as $id => $value) {
				$entity = null;
				if ($entity = $this->getEntity($id,$type)) {
					$allSearch[] = array(
						'type' => $type,
						'id' => $id,
						'weight' => $value['weight'],
						'entity' => $entity
					);
				}
			}
		}


		usort($allSearch, function($a, $b) {
			return $b['weight'] - $a['weight'];
		});

		return $allSearch;
	}

	private function getEntity($id,$type) {
		$entity = $this->em->getRepository('AppBundle\Entity\\'.ucfirst($type))->findOneBy(array('id'=>$id));
		return $entity;
	}
}
