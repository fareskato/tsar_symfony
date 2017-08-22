<?php
/**
 * Created by PhpStorm.
 * User: oalti
 * Date: 07/08/2017
 * Time: 12:07 PM
 */


use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\StreamOutput;

set_time_limit(0);

$host = '';
$user = 'dev';
$pass = '';


$db_old = 'tsar_devis';
$db_new = 'tsar';


$dbh = new PDO('mysql:host', $user, $pass);

//REPLACE INTO '.$db_new.'.chauffeur
//(id, currency, name, titre, ville, description, comments, nom_en_latin, nom_en_cyrillique, phone, supp_info, info_utile, excel_title, excel_description, excel_custom_id)

$query = '
(SELECT n.nid,

n.title as name,

fdfvh.field_ville_hotel_value as ville,
fdfldp.field_label_du_produit_value as label,
fdfc1.field_commentaires_value as commentaries,
fdftcm.field_type_clientele_musee_value as client_type,

fdfnl.field_nom_latin_value as name_en_latin,
fdfnc.field_nom_cyrillique_value as name_en_cyrillique,
fdfph.field_telephone_value as phone,
fdfis.field_informations_sup_value as info_sup,

fdfiu.field_information_utile_value as extra_info,
fdfd.field_devise_value as currency,
fdftf.field_type_formule_value as group_type,

fdfet.field_excel_title_value as excel_title,
fdfed.field_excel_description_value as excel_description,
fdfci.field_custom_id_value as excel_custom_id

FROM   ' . $db_old . '.node n

LEFT JOIN ' . $db_old . '.field_data_field_excel_title fdfet ON fdfet.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_excel_description fdfed ON fdfed.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_custom_id fdfci ON fdfci.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_autre_produit fdfap ON fdfap.entity_id = n.nid


LEFT JOIN ' . $db_old . '.field_data_field_tickets_de_musee fdftdm ON fdftdm.entity_id = n.nid

LEFT JOIN ' . $db_old . '.field_data_field_ville_hotel fdfvh ON fdfvh.entity_id = fdftdm.field_tickets_de_musee_value
LEFT JOIN ' . $db_old . '.field_data_field_label_du_produit fdfldp ON fdfldp.entity_id = fdftdm.field_tickets_de_musee_value
LEFT JOIN ' . $db_old . '.field_data_field_type_clientele_musee fdftcm ON fdftcm.entity_id = fdftdm.field_tickets_de_musee_value
LEFT JOIN ' . $db_old . '.field_data_field_commentaires fdfc1 ON fdfc1.entity_id = fdftdm.field_tickets_de_musee_value


LEFT JOIN ' . $db_old . '.field_data_field_info_sup_vouchers fdfisv ON fdfisv.entity_id = n.nid

LEFT JOIN ' . $db_old . '.field_data_field_nom_latin fdfnl ON fdfnl.entity_id = fdfisv.field_info_sup_vouchers_value
LEFT JOIN ' . $db_old . '.field_data_field_nom_cyrillique fdfnc ON fdfnc.entity_id = fdfisv.field_info_sup_vouchers_value
LEFT JOIN ' . $db_old . '.field_data_field_informations_sup fdfis ON fdfis.entity_id = fdfisv.field_info_sup_vouchers_value
LEFT JOIN ' . $db_old . '.field_data_field_telephone fdfph ON fdfph.entity_id = fdfisv.field_info_sup_vouchers_value


LEFT JOIN ' . $db_old . '.field_data_field_info_sup_agents fdfisa ON fdfisa.entity_id = n.nid

LEFT JOIN ' . $db_old . '.field_data_field_information_utile fdfiu ON fdfiu.entity_id = fdfisa.field_info_sup_agents_value
LEFT JOIN ' . $db_old . '.field_data_field_devise fdfd ON fdfd.entity_id = fdfisa.field_info_sup_agents_value
LEFT JOIN ' . $db_old . '.field_data_field_type_formule fdftf ON fdftf.entity_id = fdfisa.field_info_sup_agents_value



WHERE n.`type` = "tickets_musee")';

foreach ($dbh->query($query, PDO::FETCH_ASSOC) as $row) {


  $city = $row["ville"];

  $q1 = "SELECT * FROM " . $db_new . ".`location_translation` nt WHERE nt.`city` LIKE '%" . $city . "%' LIMIT 50";
  foreach ($dbh->query($q1, PDO::FETCH_ASSOC) as $value) {
    break;
  }
  $city = NULL;
  if ($value['translatable_id']) {
    $city = $value['translatable_id'];
  }


  if ($city == '') {
    $row["ville"] = "null";
  }
  else {
    $row["ville"] = "'" . $city . "'";
  }


  $query = '
  REPLACE INTO ' . $db_new . '.tickets_de_musee
  (id,client_type,currency,group_type,label,`name`,ville,musee,comments,nom_en_latin,nom_en_cyrillique,phone,supp_info,info_utile,excel_custom_id,excel_title,excel_description)
  VALUES
  (\'' . $row["nid"] . '\',\'' . $row["client_type"] . '\',\'' . $row["currency"] . '\',\'' . $row["group_type"] . '\',\'' . $row["name"] . '\',\'' . $row["name"] . '\',' . $row["ville"] . ',\'' . addslashes($row["label"]) . '\',
  \'' . $row["commentaries"] . '\',\'' . $row["name_en_latin"] . '\',\'' . $row["name_en_cyrillique"] . '\',\'' . $row["phone"] . '\',\'' . $row["info_sup"] . '\',\'' . $row["extra_info"] . '\',
  \'' . $row["excel_custom_id"] . '\',\'' . addslashes($row["excel_title"]) . '\',\'' . addslashes($row["excel_description"]) . '\')
  ';


  $dbh->query($query)->execute();

}


echo "\n";
print_r('DONE');
echo "\n";
die();

