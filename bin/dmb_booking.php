<?php
/**
 * Created by PhpStorm.
 * User: oalti
 * Date: 21/08/2017
 * Time: 10:52 AM
 */


use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\StreamOutput;

set_time_limit(0);

$host = '';
$user = 'dev';
$pass = '';


$db_old = 'tsar_drupal';
$db_new = 'tsar';

$dbh = new PDO('mysql:host', $user, $pass);


// BOOKING

$query = 'SELECT DISTINCT n.nid as id,
fdfuab.field_user_attributed_booking_target_id as assigned_user,
fdfsb.field_services_booking_value as supp_services,
fdfcb.field_civility_booking_value as civilite,
fdfor.field_offer_rating_value as offer_options,
fdfed.field_email_documents_value as send_docs,
fdfdb.field_date_booking_value as `date`,
fdfhb.field_hebergement_booking_value as hotel,
fdfnb.field_nbpersons_booking_value as amount_of_people,
fdfss.field_supplement_single_value as supplement_single,
dfdnd.field_number_days_value as numbre_de_jours,
fdfnn.field_number_nights_value as numbre_de_nuits,
fdfrh.field_rooms_hotel_value as numbre_de_chambers,
fdfpb1.field_price_booking_value as prix,
fdfpss.field_price_supplement_single_value as supplement_single_price,
fdfpfb.field_pdf_file_booking_value as pdf_link,
fdfbo.field_blockjours_order_value as blockjour_order,
fdfvb.field_visa_booking_value as visa,
fdffb1.field_flight_booking_value as flight_from,
fdfpb2.field_precisions_booking_value as clarification,
fbfnb.field_name_booking_value as nom,
fdffb2.field_firstname_booking_value as prenom,
fdfpb3.field_phone_booking_value as phone,
fdfem.field_email_booking_email as email,
fdfbk.field_base64_key_value as security_key,
fdftv.field_tsar_website_value as website_version,
fdfc.field_comments_value as comment,
fdfbi.field_devis_booking_id_value as devis_booking_id,
fdfdel.field_devis_excel_link_value as excel_link,
fdfclb.field_content_linked_booking_target_id as linked_booking,
n.title as `name`
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_user_attributed_booking fdfuab ON fdfuab.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_services_booking fdfsb ON fdfsb.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_civility_booking fdfcb ON fdfcb.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_offer_rating fdfor ON fdfuab.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_email_documents fdfed ON fdfed.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_date_booking fdfdb ON fdfdb.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_hebergement_booking fdfhb ON fdfhb.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_nbpersons_booking fdfnb ON fdfnb.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_supplement_single fdfss ON fdfss.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_number_days dfdnd ON dfdnd.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_number_nights fdfnn ON fdfnn.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_rooms_hotel fdfrh ON fdfrh.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_price_booking fdfpb1 ON fdfpb1.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_price_supplement_single fdfpss ON fdfpss.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_pdf_file_booking fdfpfb ON fdfpfb.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_blockjours_order fdfbo ON fdfbo.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_visa_booking fdfvb ON fdfvb.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_flight_booking fdffb1 ON fdffb1.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_precisions_booking fdfpb2 ON fdfpb2.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_name_booking fbfnb ON fbfnb.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_firstname_booking fdffb2 ON fdffb2.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_phone_booking fdfpb3 ON fdfpb3.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_email_booking fdfem ON fdfem.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_base64_key fdfbk ON fdfbk.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_tsar_website fdftv ON fdftv.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_comments fdfc ON fdfc.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_devis_booking_id fdfbi ON fdfbi.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_devis_excel_link fdfdel ON fdfdel.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_content_linked_booking fdfclb ON fdfclb.entity_id = n.nid
WHERE n.type = "booking"
';


foreach ($dbh->query($query, PDO::FETCH_ASSOC) as $row) {
  //print_r($row);
  //die();


  $q_civ = "SELECT translatable_id FROM " . $db_new . ".book_civilite_translation WHERE description = '" . $row['civilite'] . "'";
  foreach ($dbh->query($q_civ, PDO::FETCH_ASSOC) as $row_civ) {
    $row['civilite'] = $row_civ['translatable_id'];
    break;
  }

  $q_supp_services = "SELECT translatable_id FROM " . $db_new . ".book_supp_services_translation WHERE description = '" . $row['supp_services'] . "'";
  foreach ($dbh->query($q_supp_services, PDO::FETCH_ASSOC) as $row_supp_services) {
    $row['supp_services'] = $row_supp_services['translatable_id'];
    break;
  }


  $q_offer_options = "SELECT translatable_id FROM " . $db_new . ".book_offer_options_translation WHERE description = '" . $row['offer_options'] . "'";
  foreach ($dbh->query($q_offer_options, PDO::FETCH_ASSOC) as $row_offer_options) {
    $row['offer_options'] = $row_offer_options['translatable_id'];
    break;
  }

  $q_send_docs = "SELECT translatable_id FROM " . $db_new . ".book_send_docs_translation WHERE description = '" . $row['send_docs'] . "'";
  foreach ($dbh->query($q_send_docs, PDO::FETCH_ASSOC) as $row_send_docs) {
    $row['send_docs'] = $row_send_docs['translatable_id'];
    break;
  }


  $bdata = explode("T", $row['date']);
  $bdata[0] = explode("-", $bdata[0]);
  $bdata[1] = str_replace(":", "", $bdata[1]);
  $bdata = $bdata[0][0] . $bdata[0][1] . $bdata[0][2] . $bdata[1];
  $row['date'] = substr($bdata, 0, -2);

  $linked_booking = $row['linked_booking'];

  unset($row['linked_booking']);

  foreach ($row as $key => &$anelement) {

    if (empty($anelement)) {
      $anelement = 'null';
    }
    else {
      $anelement = "'" . addslashes($anelement) . "'";
    }
  }


  $q2 = '
REPLACE INTO ' . $db_new . '.booking
(id,assigned_user,supp_services,civilite,offer_options,send_docs,`date`,hotel,amount_of_people,supplement_single,numbre_de_jours,
numbre_de_nuits,numbre_de_chambers,prix,supplement_single_price,pdf_link,blockjour_order,visa,flight_from,
clarification,nom,prenom,phone,email,security_key,website_version,comment,devis_booking_id,excel_link,`name`) VALUES
(' . implode($row, ',') . ')';

  //die($q2);

  //echo $q2;

  //echo "\n";
  //echo "\n";

  $q_clean = "DELETE FROM " . $db_new . ".booking_to_related_product WHERE booking_to_related_product.id = " . $row['id'];
  $dbh->query($q_clean)->execute();


  $dbh->query($q2)->execute();



  $q_event = "SELECT id FROM " . $db_new . ".event WHERE id='" . $linked_booking . "'";
  foreach ($dbh->query($q_event, PDO::FETCH_ASSOC) as $row_event) {
    $q_event_incert = "INSERT INTO " . $db_new . ".booking_to_related_product (id, booking, event) VALUES (" . $row['id'] . "," . $row['id'] . ",'" . $linked_booking . "')";

    $dbh->query($q_event_incert)->execute();
    break;
  }

  $q_voyage = "SELECT id FROM " . $db_new . ".voyage WHERE id='" . $linked_booking . "'";
  foreach ($dbh->query($q_voyage, PDO::FETCH_ASSOC) as $row_voyage) {

    $q_voyage_incert = "INSERT INTO " . $db_new . ".booking_to_related_product (id, booking, voyage) VALUES (" . $row['id'] . "," . $row['id'] . ",'" . $linked_booking . "')";

    $dbh->query($q_voyage_incert)->execute();
    break;
  }


  $q_extension = "SELECT id FROM " . $db_new . ".extension WHERE id='" . $linked_booking . "'";
  foreach ($dbh->query($q_extension, PDO::FETCH_ASSOC) as $row_extension) {
    $q_extension_incert = "INSERT INTO " . $db_new . ".booking_to_related_product (id, booking, extension) VALUES (" . $row['id'] . "," . $row['id'] . ",'" . $linked_booking . "')";

    $dbh->query($q_extension_incert)->execute();
    break;
  }


  $q_visit = "SELECT id FROM " . $db_new . ".visit WHERE id='" . $linked_booking . "'";
  foreach ($dbh->query($q_visit, PDO::FETCH_ASSOC) as $row_visit) {
    $q_visit_incert = "INSERT INTO " . $db_new . ".booking_to_related_product (id, booking, visit) VALUES (" . $row['id'] . "," . $row['id'] . ",'" . $linked_booking . "')";

    $dbh->query($q_visit_incert)->execute();
    break;
  }


}