<?php
namespace AppBundle\Service;


class Forms {

	private $em;
	private $translator;
	private $locale;
	private $user;

	public function __construct($em,$translator,$locale,$user)
	{
		$this->user = $user;
		$this->em = $em;
		$this->translator = $translator;
		$this->locale = $locale;
	}

	public function getBookingForm($entity,$include = array()){
		$form = array(
			'id' => 'bookingForm',
			'class' => 'booking bookingForm booking'.$entity->getClass(),
			'method' => 'post',
			//'onsubmit' => 'return tsar.Subscribe.checkForm( $(this) );',
			'onsubmit' => 'return false',
			'multipart' => 0
		);

		$dateStart = new \DateTime();

		$form['fields'] = array();

		$form['fields']['type'] = array(
			'type' => 'hidden',
			'name' => 'entity_type',
			'value' => strtolower($entity->getClass())
		);

		$form['fields']['fieldset_dates'] = array(
			'type' => 'fieldset',
			'class' => 'block_dates'
		);

		$form['fields']['title_1'] = array(
			'type' => 'title',
			'class' => 'bookingTitle title_1',
			'name' => $this->translator->trans('front.booking.title1'),
			'prefix' => 1
		);

		if (in_array('date_apart',$include)) {
			$form['fields']['date_apart'] = array(
				'field_class'=>'cl1',
				'type'=> 'date',
				'format'=> 'dd/mm/yy',
				'min_date'=> $dateStart->modify('+1 day')->format('d/m/Y'),
				'default_date'=> $dateStart->format('d/m/Y'),
				'value' => $dateStart->format('d/m/Y'),
				'max_date'=> $dateStart->modify('+3 months')->format('d/m/Y'),
				'label'=> $this->translator->trans('front.booking.date_apart'),
				'name'=> 'date_booking',
				'description'=> '',
				'error' => $this->translator->trans('front.booking.date_apart.error')
			);
		}

		if (in_array('date_apart',$include) && method_exists($entity,'getDay')) {
			$hotels = array();
			foreach($entity->getDay() as $day) {
				if($day) {
					if ($day->getHotel()) {
						foreach ($day->getHotel() as $hotel) {
							$stars = '';
							if ($hotel->getHotelStars()) {
								$s = $hotel->getHotelStars()[0];
								$stars = intval($s->translate()->getName());
							}
							$hotels[$hotel->getid()] = $hotel->translate()->getName() . ($stars ? ' ' . $stars . '*' : '');
						}
					}
				}
			}
			if ($hotels) {
				$form['fields']['hebergement'] = array(
					'field_class'=>'cl1',
					'type'=> 'select',
					'label'=> $this->translator->trans('front.booking.hebergement'),
					'name'=> 'nbpersons',
					'values' => $hotels,
					'error' => $this->translator->trans('front.booking.hebergement.error')
				);
			}
		}

		if (in_array('nights',$include)) {
			$nights = array();
			for ($i = 1; $i <= 8; $i++) {
				$nights[$i] = $i;
			}
			$nights[9] = '9 ' . $this->translator->trans('front.booking.more');
			$form['fields']['nights'] = array(
				'field_class'=>'cl1',
				'type' => 'select',
				'label' => $this->translator->trans('front.booking.nights'),
				'name' => 'nights',
				'values' => $nights,
				'value' => 1,
				'error' => $this->translator->trans('front.booking.nights.error')
			);
		}

		if (in_array('combien',$include)) {
			$combien = array();
			for ($i = 1; $i <= 8; $i++) {
				$combien[$i] = $i;
			}
			$combien[9] = '9 ' . $this->translator->trans('front.booking.more');
			$form['fields']['combien'] = array(
				'field_class'=>'cl1',
				'type' => 'select',
				'label' => $this->translator->trans('front.booking.combien'),
				'name' => 'nbpersons',
				'values' => $combien,
				'value' => 4,
				'error' => $this->translator->trans('front.booking.combien.error')
			);
		}

		if (in_array('supplement',$include)) {
			$supplement = array();
			for ($i = 0; $i <= 8; $i++) {
				$supplement[$i] = $i;
			}
			$form['fields']['supplement'] = array(
				'field_class' => 'cl1',
				'type' => 'select',
				'label' => $this->translator->trans('front.booking.supplement'),
				'name' => 'supplement',
				'values' => $supplement,
				'value' => 0,
				'error' => $this->translator->trans('front.booking.supplement.error')
			);
		}

		if (in_array('number',$include)) {
			$combien = array();
			for ($i = 1; $i <= 8; $i++) {
				$combien[$i] = $i;
			}
			$combien[9] = '9 ' . $this->translator->trans('front.booking.more');
			$form['fields']['number'] = array(
				'field_class' => 'cl1',
				'type' => 'select',
				'label' => $this->translator->trans('front.booking.number'),
				'name' => 'nbpersons',
				'values' => $combien,
				'value' => 4,
				'error' => $this->translator->trans('front.booking.number.error')
			);
		}

		if (in_array('rooms',$include)) {
			$rooms = array();
			for ($i = 1; $i <= 8; $i++) {
				$rooms[$i] = $i;
			}
			$rooms[9] = '9 ' . $this->translator->trans('front.booking.more');
			$form['fields']['rooms'] = array(
				'field_class' => 'cl1',
				'type' => 'select',
				'label' => $this->translator->trans('front.booking.rooms'),
				'name' => 'rooms',
				'values' => $rooms,
				'value' => 1,
				'error' => $this->translator->trans('front.booking.rooms.error')
			);
		}

		if (in_array('supplement',$include)) {
			$supplement = array();
			for ($i = 0; $i <= 8; $i++) {
				$supplement[$i] = $i;
			}
			$form['fields']['supplement'] = array(
				'field_class' => 'cl1',
				'type' => 'select',
				'label' => $this->translator->trans('front.booking.supplement'),
				'name' => 'supplement',
				'values' => $supplement,
				'value' => 0,
				'error' => $this->translator->trans('front.booking.supplement.error')
			);
		}

		$form['fields']['fieldset_options'] = array(
			'type' => 'fieldset',
			'class' => 'block_options'
		);

		if (in_array('visa',$include) || in_array('services',$include) || in_array('precisions',$include) || in_array('flight',$include)) {
			$form['fields']['title_1_subtitle'] = array(
				'type' => 'title',
				'class' => 'bookingTitle title_1_subtitle',
				'name' => $this->translator->trans('front.booking.title_1_subtitle'),
			);
			$form['fields']['block_options_add'] = array(
				'type' => 'fieldset',
				'class' => 'block_options_add'
			);

			if (in_array('visa', $include)) {
				$form['fields']['visa'] = array(
					'type' => 'checkbox',
					'label' => $this->translator->trans('front.booking.visa'),
					'name' => 'visa',
					'value' => '1',
					'checked' => false,
					'error' => $this->translator->trans('front.booking.visa.error'),
					'label_before' => false
				);
			}

			if (in_array('services', $include)) {
				$servicesAll = $this->em->getRepository('AppBundle\Entity\BookBookingservices')->findAll();
				$services = array();
				foreach ($servicesAll as $s) {
					$services[$s->getId()] = $s->translate()->getName();
				}
				$form['fields']['services'] = array(
					'type' => 'radio',
					'label' => '',//$this->translator->trans('front.booking.services'),
					'name' => 'services',
					'values' => $services,
					'value' => '',
					'error' => $this->translator->trans('front.booking.services.error'),
					'label_before' => true,
					'label_before_field' => false
				);
			}

			$form['fields']['block_options_flight'] = array(
				'type' => 'fieldset',
				'class' => 'block_options_flight'
			);
			if (in_array('flight', $include)) {
				$form['fields']['flight'] = array(
					'field_class'=>'cl1',
					'type' => 'text',
					'label' => $this->translator->trans('front.booking.flight'),
					'name' => 'flight',
					'maxlength' => 128,
					'value' => '',
					'placeholder' => '',
					'error' => $this->translator->trans('front.booking.flight.error')
				);
			}
			if (in_array('precisions', $include)) {
				$form['fields']['precisions'] = array(
					'field_class'=>'cl2',
					'type' => 'textarea',
					'label' => $this->translator->trans('front.booking.precisions'),
					'name' => 'precisions',
					'value' => '',
					'placeholder' => '',
					'error' => $this->translator->trans('front.booking.precisions.error')
				);
			}
		}

		$form['fields']['fieldset_contacts'] = array(
			'type' => 'fieldset',
			'class' => 'block_contacts'
		);

		$form['fields']['title_2'] = array(
			'type' => 'title',
			'class' => 'bookingTitle title_2',
			'name' => $this->translator->trans('front.booking.title2'),
			'prefix' => 2
		);

		$civilityAll = $this->em->getRepository('AppBundle\Entity\BookCivilite')->findAll();
		$civility = array();
		foreach($civilityAll as $s) {
			$civility[$s->getId()] = $s->translate()->getName();
		}
		$form['fields']['civility'] = array(
			'field_class'=>'cl1',
			'type'=> 'select',
			'label'=> $this->translator->trans('front.booking.civility'),
			'name'=> 'civility',
			'values' => $civility,
			'value' => 0,
			'error' => $this->translator->trans('front.booking.civility.error'),
			'required' => true,
		);
		$form['fields']['name'] = array(
			'field_class'=>'cl1',
			'type'=> 'text',
			'label'=> $this->translator->trans('front.booking.name'),
			'name'=> 'name',
			'maxlength' => 128,
			'value' => '',
			'placeholder' => '',
			'error' => $this->translator->trans('front.booking.name.error'),
			'required' => true,
		);
		$form['fields']['firstname'] = array(
			'field_class'=>'cl1',
			'type'=> 'text',
			'label'=> $this->translator->trans('front.booking.firstname'),
			'name'=> 'firstname',
			'maxlength' => 128,
			'value' => '',
			'placeholder' => '',
			'error' => $this->translator->trans('front.booking.firstname.error')
		);
		$form['fields']['phone'] = array(
			'field_class'=>'cl1',
			'type'=> 'text',
			'label'=> $this->translator->trans('front.booking.phone'),
			'name'=> 'phone',
			'maxlength' => 20,
			'value' => '',
			'placeholder' => '',
			'error' => $this->translator->trans('front.booking.phone.error'),
			'required' => true,
		);

		$form['fields']['email'] = array(
			'field_class'=>'cl1',
			'type'=> 'text',
			'label'=> $this->translator->trans('front.booking.mail'),
			'name'=> 'email',
			'maxlength' => 64,
			'value' => $this->user ? $this->user->getEmail() : '',
			'placeholder' => '',
			'error' => $this->translator->trans('front.booking.mail.error'),
			'required' => true,
		);

		$ratingAll = $this->em->getRepository('AppBundle\Entity\BookOfferOptions')->findAll();
		$rating = array();
		foreach($ratingAll as $s) {
			$rating[$s->getId()] = $s->translate()->getName();
		}
		$form['fields']['offer-rating'] = array(
			'field_class'=>'cl1',
			'type'=> 'select',
			'label'=> $this->translator->trans('front.booking.offer-rating'),
			'name'=> 'offer-rating',
			'values' => $rating,
			'value' => 0,
			'error' => $this->translator->trans('front.booking.offer-rating.error'),
		);

		$form['fields']['required'] = array(
			'field_class'=> 'required',
			'type'=> 'description',
			'label'=> '',
			'name'=> '',
			'text' => '<span class="required">*</span> '.$this->translator->trans('front.booking.required'),
			'required' => true,
		);

		global $kernel;
		$policy = $this->em->getRepository('AppBundle\Entity\Article')->findOneBy(array('id'=>80));
		$slug = $policy->translate()->getSlug();
		$url = $kernel->getContainer()->get('router')->generate('home_static_'.$this->locale, array('slug'=>$slug), true);
		$url = str_replace('/page-','/',$url);

		$form['fields']['policy'] = array(
			'field_class'=> 'policy_link',
			'type'=> 'description',
			'label'=> '',
			'name'=> '',
			'text' => '<a href="'.$url.'" target="_blank">'.$this->translator->trans('front.booking.policy').'</a>',
			'required' => true,
		);

		$form['fields']['button'] =  array(
			'type' => 'button',
			'class' => 'bookingTitle',
			'button_type' => 'submit',
			'label' => '',
			'name' => '',
			'prefix' => 3,
			'value' => $this->translator->trans('front.booking.title3')
		);

		return $form;
	}

	public function getSubscribeForm(){
		$form = array(
			'id' => 'subscribe',
			'class' => 'subscribe',
			'method' => 'post',
			'onsubmit' => 'return tsar.Subscribe.checkForm( $(this) );',
			'multipart' => 0
		);

		$form['fields'] = array(
			'mail' => array(
				'type'=> 'text',
				'label'=> '',
				'name'=> 'mail',
				'maxlength' => 128,
				'value' => $this->user ? $this->user->getEmail() : '',
				'placeholder' => $this->translator->trans('front.subscribe.placeholder'),
				'error' => $this->translator->trans('front.subscribe.error')
			),
			'button' => array(
				'type' => 'button',
				'button_type' => 'submit',
				'label' => '',
				'name' => '',
				'value' => ''
			)
		);
		return $form;
	}
}