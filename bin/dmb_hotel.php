<?


use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\StreamOutput;

set_time_limit(0);

$host = '';
$user = 'dev';
$pass = '';


$db_old = 'tsar_drupal';
$db_old_devis = 'tsar_devis';
//$db_old = 'website';
$db_new = 'tsar';

$dbh = new PDO('mysql:host', $user, $pass);

/**
 * HOTEL
 */
// MAIN TABLE
$query = '
REPLACE INTO ' . $db_new . '.hotel
(id,image,image_background,image_miniature, location,
number_of_rooms, hotel_internet, etiquette)
(
SELECT n.nid,
fdfcf.field_central_photo_fid as image,
fdfbp.field_background_photo_fid as image_background,
fdflf.field_list_photo_fid as image_miniature,
fdfl.field_location_lid as location,
fdfrh.field_rooms_hotel_value as number_of_rooms,
fdfih.field_internet_hotel_tid as hotel_internet,
fdfe.field_etiquette_tid as etiquette
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_background_photo fdfbp  ON fdfbp.entity_id = n.nid 
LEFT JOIN ' . $db_old . '.field_data_field_central_photo fdfcf ON fdfcf.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_list_photo fdflf ON fdflf.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_location fdfl ON fdfl.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_list_headline fdflh ON fdflh.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_type_hotel fdfth ON fdfth.entity_id = n.nid
LEFT JOIN ' . $db_old . '.url_alias ua ON ua.source = CONCAT("node/",n.nid) 
LEFT JOIN ' . $db_old . '.field_data_body fdb ON fdb.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_rooms_hotel fdfrh ON fdfrh.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_internet_hotel fdfih ON fdfih.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_etiquette fdfe ON fdfe.entity_id = n.nid

WHERE n.type = "hebergement"
)
';
$dbh->query($query)->execute();


$query = '
REPLACE INTO ' . $db_new . '.hotel_translation
(id,
translatable_id,
locale,
body_summary,
name,
slug,
headline,
body,
type_of_hotel,
active, 
keywords)
(
SELECT 
n.nid, 
n.nid, 
"fr" as locale,
fdb.body_summary AS summary, 
n.title,
REPLACE(ua.alias, "hébergement\/", "") AS slug,
fdflh.field_list_headline_value as headline,
fdb.body_value,
fdfth.field_type_hotel_value as type_of_hotel,
n.status as active,
"" as keywords
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_background_photo fdfbp  ON fdfbp.entity_id = n.nid 
LEFT JOIN ' . $db_old . '.field_data_field_central_photo fdfcf ON fdfcf.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_list_photo fdflf ON fdflf.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_location fdfl ON fdfl.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_list_headline fdflh ON fdflh.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_type_hotel fdfth ON fdfth.entity_id = n.nid
LEFT JOIN ' . $db_old . '.url_alias ua ON ua.source = CONCAT("node/",n.nid) 
LEFT JOIN ' . $db_old . '.field_data_body fdb ON fdb.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_rooms_hotel fdfrh ON fdfrh.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_internet_hotel fdfih ON fdfih.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_etiquette fdfe ON fdfe.entity_id = n.nid

WHERE n.type = "hebergement"
)
';

$dbh->query($query)->execute();

// HOTEL TO DOMAIN
$query = '
REPLACE INTO ' . $db_new . '.hotel_to_domain
(hotel_id,domain_id)
(SELECT n.nid, fdfd.field_domaine_tid
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_domaine fdfd ON fdfd.entity_id = n.nid
WHERE n.type = "hebergement")
';
$dbh->query($query)->execute();

// HOTEL TO FILES
$query = '
REPLACE INTO ' . $db_new . '.hotel_to_files
(hotel_id,files_id)
(SELECT n.nid, fp.field_photos_fid
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_photos fp ON fp.entity_id = n.nid
WHERE n.type = "hebergement" AND fp.field_photos_fid IS NOT NULL
)
';
$dbh->query($query)->execute();

// HOTEL TO HOTELSTARS
$query = '
REPLACE INTO ' . $db_new . '.hotel_to_hotelstars
(hotel_id,hotelstars_id)
(SELECT n.nid, fdfns.field_number_stars_tid
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_number_stars fdfns ON fdfns.entity_id = n.nid
WHERE n.type = "hebergement" AND fdfns.field_number_stars_tid IS NOT NULL )
';
$dbh->query($query)->execute();

// HOTEL TO METRO
$query = '
REPLACE INTO ' . $db_new . '.hotel_to_metro
(hotel_id,metro_id)
(SELECT n.nid, fdfmsh.field_metro_station_hotel_target_id
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_metro_station_hotel fdfmsh ON fdfmsh.entity_id = n.nid
WHERE n.type = "hebergement" AND fdfmsh.field_metro_station_hotel_target_id IS NOT NULL )
';
$dbh->query($query)->execute();


// HOTEL TO DESTINATION
$query = '
REPLACE INTO ' . $db_new . '.hotel_to_destination
(hotel_id,destination_id)
(SELECT n.nid, fdfld.field_link_destinations_target_id
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_link_destinations fdfld ON fdfld.entity_id = n.nid
LEFT JOIN ' . $db_old . '.node n2 ON fdfld.field_link_destinations_target_id = n2.nid
WHERE n.type = "hebergement" AND fdfld.field_link_destinations_target_id IS NOT NULL AND n2.nid IS NOT NULL )
';
$dbh->query($query)->execute();

//HOTEL TO SERVICE
$query = 'SELECT field_services_hotel_value as service FROM ' . $db_old . '.field_data_field_services_hotel GROUP BY field_services_hotel_value';
$services = [];

foreach ($dbh->query($query, PDO::FETCH_ASSOC) as $row) {
  $q = 'SELECT s.* FROM ' . $db_new . '.book_services_translation s WHERE s.name = "' . trim($row['service']) . '" and s.locale="fr" ';
  if ($dbh->query($q)->rowCount() == 0) {
    $qMax = 'SELECT MAX(ID) as maximum  FROM ' . $db_new . '.book_services';
    $max = $dbh->query($qMax)->fetch(PDO::FETCH_ASSOC);
    $max = $max['maximum'] + 1;
    $q2 = 'INSERT INTO ' . $db_new . '.book_services  (id) VALUES (' . $max . ') ';
    $q3 = 'INSERT INTO ' . $db_new . '.book_services_translation  (id,translatable_id,name,description,locale) VALUES (' . $max . ',' . $max . ',"' . trim($row['service']) . '","","fr") ';
    $dbh->query($q2);
    $dbh->query($q3);
  }
}
$query = '
SELECT n.nid, fdfsh.field_services_hotel_value as value
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_services_hotel fdfsh on fdfsh.entity_id = n.nid
WHERE n.type = "hebergement"';
foreach ($dbh->query($query, PDO::FETCH_ASSOC) as $row) {
  $nid = $row['nid'];
  $term = $row['value'];
  $q2 = ' SELECT translatable_id as id FROM  ' . $db_new . '.book_services_translation WHERE name = "' . trim($row['value']) . '" and locale="fr" ';
  $service = $dbh->query($q2, PDO::FETCH_ASSOC)->fetch();
  $srvId = $service['id'];

  $q3 = 'REPLACE INTO ' . $db_new . '.hotel_to_service (hotel_id,service_id) VALUES (' . $nid . ',' . $srvId . ')';
  $dbh->query($q3);
}


//  HOTEL LABEL & EXEL

$query = 'SELECT n.nid,
fdtf.title_field_value as label,
fdfet.field_excel_title_value as excel_title,
fdfed.field_excel_description_value as excel_description,
fdfci.field_custom_id_value as custom_id,
fdfmh.field_nom_hotel_value as mom_hotel,
fdfcdc.field_categorie_de_chambre_value as categorie,
fdfsmh.field_stations_metro_hotel_value as metro,
fdfc.field_chambres_value as number_of_rooms,
fdfeh.field_etoile_hotel_value as hotel_stars,
fdfaddr.field_l_adresse_de_l_h_tel_lat as lat,
fdfaddr.field_l_adresse_de_l_h_tel_lng as lng,
fdfwh.field_wifi_hotel_value as wifi
FROM   '.$db_old_devis.'.node n
LEFT JOIN '.$db_old_devis.'.field_data_title_field fdtf ON fdtf.entity_id=n.nid
LEFT JOIN '.$db_old_devis.'.field_data_field_excel_title fdfet ON fdfet.entity_id = n.nid
LEFT JOIN '.$db_old_devis.'.field_data_field_excel_description fdfed ON fdfed.entity_id = n.nid
LEFT JOIN '.$db_old_devis.'.field_data_field_custom_id fdfci ON fdfci.entity_id = n.nid

LEFT JOIN '.$db_old_devis.'.field_data_field_description_hotel_group fdfdhg ON fdfdhg.entity_id=n.nid
LEFT JOIN '.$db_old_devis.'.field_data_field_nom_hotel fdfmh ON fdfmh.entity_id = fdfdhg.field_description_hotel_group_value
LEFT JOIN '.$db_old_devis.'.field_data_field_categorie_de_chambre fdfcdc ON fdfcdc.entity_id = fdfdhg.field_description_hotel_group_value
LEFT JOIN '.$db_old_devis.'.field_data_field_stations_metro_hotel fdfsmh ON fdfsmh.entity_id = fdfdhg.field_description_hotel_group_value
LEFT JOIN '.$db_old_devis.'.field_data_field_chambres fdfc ON fdfc.entity_id = fdfdhg.field_description_hotel_group_value
LEFT JOIN '.$db_old_devis.'.field_data_field_etoile_hotel fdfeh ON fdfeh.entity_id = fdfdhg.field_description_hotel_group_value
LEFT JOIN '.$db_old_devis.'.field_data_field_l_adresse_de_l_h_tel fdfaddr ON fdfaddr.entity_id = fdfdhg.field_description_hotel_group_value
LEFT JOIN '.$db_old_devis.'.field_data_field_wifi_hotel fdfwh ON fdfwh.entity_id = fdfdhg.field_description_hotel_group_value

WHERE n.`type` = "hotel" AND n.nid!=2675';
$array=array();
$i=0;
$n=0;
foreach ($dbh->query($query, PDO::FETCH_ASSOC) as $row) {
    $q1="SELECT * FROM " . $db_new . ".hotel h JOIN " . $db_new . ".hotel_translation ht ON ht.translatable_id=h.id WHERE ht.locale='fr' AND ht.name LIKE '%".$row['mom_hotel']."%'";
    $value='';
    foreach ($dbh->query($q1, PDO::FETCH_ASSOC) as $value) {
        break;
    }
    if($value!=''){
        if($row['hotel_stars']==1){$hotel_stars=17;}
        if($row['hotel_stars']==2){$hotel_stars=18;}
        if($row['hotel_stars']==3){$hotel_stars=19;}
        $wifi='NULL';
        if($row['wifi']==1){$wifi=23;}
        if($row['wifi']==2){$wifi=24;}
        if($row['wifi']==3){$wifi='NULL';}

        //  Добавляем данные.
        $q = "UPDATE ".$db_new.".hotel SET 
        label=?, excel_custom_id=?, excel_title=?, excel_description=?, `categorie`=?, `number_of_rooms`=?, `hotel_internet`=".$wifi."
        WHERE id=?";
        $dbh->prepare($q)->execute(array($row['label'],$row['custom_id'],$row['excel_title'],$row['excel_description'],$row['categorie'], $row['number_of_rooms'], $value['id']));
        /*print_r($q); echo"\n"; exit;
        $dbh->query($q)->execute();*/
        $q1="SELECT * FROM ".$db_new.".hotel_to_hotelstars WHERE hotel_id=".$value['id']." AND hotelstars_id=".$hotel_stars;
        $value1='';
        foreach ($dbh->query($q1, PDO::FETCH_ASSOC) as $value1) {
            break;
        }
        if($value1=='') {
            $q = "INSERT INTO " . $db_new . ".hotel_to_hotelstars (hotel_id, hotelstars_id) VALUES (?,?)";
            $dbh->prepare($q)->execute(array($value['id'], $hotel_stars));
        }
        $n++;
    }else{
        $array[]=$row;
    }
    $i++;
    //print_r($row); echo"\n"; print_r($query); echo"\n"; exit;
}
echo '$i='.$i.' $n='.$n."\n";
foreach($array as $row){
    //  Создаем новые отели....
    if($row['hotel_stars']==1){$hotel_stars=17;}
    if($row['hotel_stars']==2){$hotel_stars=18;}
    if($row['hotel_stars']==3){$hotel_stars=19;}
    $wifi='NULL';
    if($row['wifi']==1){$wifi=23;}
    if($row['wifi']==2){$wifi=24;}
    if($row['wifi']==3){$wifi='NULL';}
    $q="INSERT INTO ".$db_new.".hotel (label, excel_custom_id, excel_title, excel_description, categorie, number_of_rooms,hotel_internet) VALUES (?,?,?,?,?,?,".$wifi.")";
    $dbh->prepare($q)->execute(array($row['label'],$row['custom_id'],$row['excel_title'],$row['excel_description'],$row['categorie'], $row['number_of_rooms']));
    $id=$dbh->lastInsertId();
    $q="INSERT INTO ".$db_new.".hotel_translation (id, translatable_id, locale, `name`) VALUES (?,?,?,?)";
    $dbh->prepare($q)->execute(array($id,$id,'fr',$row['mom_hotel']));
    $q="INSERT INTO ".$db_new.".hotel_to_hotelstars (hotel_id, hotelstars_id) VALUES (?,?)";
    $dbh->prepare($q)->execute(array($value['id'],$hotel_stars));
    $q12="SELECT * FROM " . $db_new . ".book_metro_translation WHERE locale='fr' AND name LIKE '%".$row['metro']."%'";
    $value12='';
    foreach ($dbh->query($q12, PDO::FETCH_ASSOC) as $value12) {
        break;
    }
    if($value12!=''){
        $q = "INSERT INTO " . $db_new . ".hotel_to_metro (hotel_id, metro_id) VALUES (?,?)";
        $dbh->prepare($q)->execute(array($value['id'], $value12['translatable_id']));
    }
}
echo "\n";
print_r('DONE');
echo "\n";
die();

