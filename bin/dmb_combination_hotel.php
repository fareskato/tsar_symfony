<?


use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\StreamOutput;

set_time_limit(0);

$host = '';
$user = 'dev';
$pass = '';


$db_old = 'tsar_drupal';
$db_old_devis = 'tsar_devis';
$db_new = 'tsar';

$dbh = new PDO('mysql:host', $user, $pass);



$query='SELECT n.nid as id,
fdtf.title_field_value as label,
fdfet.field_excel_title_value as excel_title,
fdfed.field_excel_description_value as excel_description,
fdfci.field_custom_id_value as custom_id,
fdfos.field_offre_speciale_value as special_offer,
fdfhvad.field_hotel_ville_a_double_target_id as hotel_a,
fdfnh.field_nom_hotel_value as hotrl_a_name,
fdfhvbd.field_hotel_ville_b_double_target_id as hotel_b,
fdfnhb.field_nom_hotel_value as hotrl_b_name,
fdfhvcd.field_hotel_ville_c_double_target_id as hotel_c,
fdfnhc.field_nom_hotel_value as hotrl_c_name,
fdfhvdd.field_hotel_ville_d_double_target_id as hotel_d,
fdfnhd.field_nom_hotel_value as hotrl_d_name,
fdfhved.field_hotel_ville_e_double_target_id as hotel_e,
fdfnhe.field_nom_hotel_value as hotrl_e_name,
fdfhvfd.field_hotel_ville_f_double_target_id as hotel_f,
fdfnhf.field_nom_hotel_value as hotrl_f_name,
fdfhvgd.field_hotel_ville_g_double_target_id as hotel_g,
fdfnhg.field_nom_hotel_value as hotrl_g_name,
fdfhvhd.field_hotel_ville_h_double_target_id as hotel_h,
fdfnhh.field_nom_hotel_value as hotrl_h_name
FROM   '.$db_old_devis.'.node n
LEFT JOIN '.$db_old_devis.'.field_data_title_field fdtf ON fdtf.entity_id=n.nid
LEFT JOIN '.$db_old_devis.'.field_data_field_excel_title fdfet ON fdfet.entity_id = n.nid
LEFT JOIN '.$db_old_devis.'.field_data_field_excel_description fdfed ON fdfed.entity_id = n.nid
LEFT JOIN '.$db_old_devis.'.field_data_field_custom_id fdfci ON fdfci.entity_id = n.nid
LEFT JOIN '.$db_old_devis.'.field_data_field_offre_speciale fdfos ON fdfos.entity_id = n.nid
LEFT JOIN '.$db_old_devis.'.field_data_field_hotel_ville_a_double fdfhvad ON fdfhvad.entity_id = n.nid
LEFT JOIN '.$db_old_devis.'.field_data_field_hotel_ville_b_double fdfhvbd ON fdfhvbd.entity_id = n.nid
LEFT JOIN '.$db_old_devis.'.field_data_field_hotel_ville_c_double fdfhvcd ON fdfhvcd.entity_id = n.nid
LEFT JOIN '.$db_old_devis.'.field_data_field_hotel_ville_d_double fdfhvdd ON fdfhvdd.entity_id = n.nid
LEFT JOIN '.$db_old_devis.'.field_data_field_hotel_ville_e_double fdfhved ON fdfhved.entity_id = n.nid
LEFT JOIN '.$db_old_devis.'.field_data_field_hotel_ville_f_double fdfhvfd ON fdfhvfd.entity_id = n.nid
LEFT JOIN '.$db_old_devis.'.field_data_field_hotel_ville_g_double fdfhvgd ON fdfhvgd.entity_id = n.nid
LEFT JOIN '.$db_old_devis.'.field_data_field_hotel_ville_h_double fdfhvhd ON fdfhvhd.entity_id = n.nid

LEFT JOIN '.$db_old_devis.'.field_data_field_description_hotel_group fdfdhg ON fdfdhg.entity_id=fdfhvad.field_hotel_ville_a_double_target_id
LEFT JOIN '.$db_old_devis.'.field_data_field_nom_hotel fdfnh ON fdfnh.entity_id=fdfdhg.field_description_hotel_group_value

LEFT JOIN '.$db_old_devis.'.field_data_field_description_hotel_group fdfdhgb ON fdfdhgb.entity_id=fdfhvbd.field_hotel_ville_b_double_target_id
LEFT JOIN '.$db_old_devis.'.field_data_field_nom_hotel fdfnhb ON fdfnhb.entity_id=fdfdhgb.field_description_hotel_group_value

LEFT JOIN '.$db_old_devis.'.field_data_field_description_hotel_group fdfdhgc ON fdfdhgc.entity_id=fdfhvcd.field_hotel_ville_c_double_target_id
LEFT JOIN '.$db_old_devis.'.field_data_field_nom_hotel fdfnhc ON fdfnhc.entity_id=fdfdhgc.field_description_hotel_group_value

LEFT JOIN '.$db_old_devis.'.field_data_field_description_hotel_group fdfdhgd ON fdfdhgd.entity_id=fdfhvdd.field_hotel_ville_d_double_target_id
LEFT JOIN '.$db_old_devis.'.field_data_field_nom_hotel fdfnhd ON fdfnhd.entity_id=fdfdhgd.field_description_hotel_group_value

LEFT JOIN '.$db_old_devis.'.field_data_field_description_hotel_group fdfdhge ON fdfdhge.entity_id=fdfhved.field_hotel_ville_e_double_target_id
LEFT JOIN '.$db_old_devis.'.field_data_field_nom_hotel fdfnhe ON fdfnhe.entity_id=fdfdhge.field_description_hotel_group_value

LEFT JOIN '.$db_old_devis.'.field_data_field_description_hotel_group fdfdhgf ON fdfdhgf.entity_id=fdfhvfd.field_hotel_ville_f_double_target_id
LEFT JOIN '.$db_old_devis.'.field_data_field_nom_hotel fdfnhf ON fdfnhf.entity_id=fdfdhgf.field_description_hotel_group_value

LEFT JOIN '.$db_old_devis.'.field_data_field_description_hotel_group fdfdhgg ON fdfdhgg.entity_id=fdfhvgd.field_hotel_ville_g_double_target_id
LEFT JOIN '.$db_old_devis.'.field_data_field_nom_hotel fdfnhg ON fdfnhg.entity_id=fdfdhgg.field_description_hotel_group_value

LEFT JOIN '.$db_old_devis.'.field_data_field_description_hotel_group fdfdhgh ON fdfdhgh.entity_id=fdfhvhd.field_hotel_ville_h_double_target_id
LEFT JOIN '.$db_old_devis.'.field_data_field_nom_hotel fdfnhh ON fdfnhh.entity_id=fdfdhgh.field_description_hotel_group_value



WHERE n.`type` = "combinaison_double"';
$array=array();
$i=0;
$n=0;
foreach ($dbh->query($query, PDO::FETCH_ASSOC) as $row) {
    if(!$row['label']){$row['label']='NULL';}
    if(!$row['custom_id']){$row['custom_id']='NULL';}
    if(!$row['excel_title']){$row['excel_title']='NULL';}
    if(!$row['excel_description']){$row['excel_description']='NULL';}
    $q="INSERT INTO ".$db_new.".combination_hotels (id, label, custom_id, excel_title, excel_description, special_offer) VALUES (?,?,?,?,?,?)";
    //print_r($q); echo"\n"; print_r(array($row['id'],$row['label'],$row['custom_id'],$row['excel_title'],$row['excel_description'],$row['special_offer'])); echo"\n"; exit;
    $dbh->prepare($q)->execute(array($row['id'],$row['label'],$row['custom_id'],$row['excel_title'],$row['excel_description'],$row['special_offer']));
    $id=$dbh->lastInsertId();
    $q="INSERT INTO ".$db_new.".combination_hotels_translation (id, translatable_id, locale, `name`) VALUES (?,?,?,?)";
    $dbh->prepare($q)->execute(array($id,$id,'fr',$row['label']));

    if($row['hotrl_a_name']) {
        $q1 = "SELECT * FROM " . $db_new . ".hotel h JOIN " . $db_new . ".hotel_translation ht ON ht.translatable_id=h.id WHERE ht.locale='fr' AND ht.name LIKE '%" . $row['hotrl_a_name'] . "%'";
        $value = '';
        foreach ($dbh->query($q1, PDO::FETCH_ASSOC) as $value) {
            break;
        }
        if ($value != '') {
            $q1 = "INSERT INTO " . $db_new . ".combination_hotel_to_hotel (combination_hotel_id, hotel_id) VALUES (" . $id . ", " . $value['id'] . ")";
            $dbh->prepare($q1)->execute();
        }
    }

    if($row['hotrl_b_name']) {
        $q1 = "SELECT * FROM " . $db_new . ".hotel h JOIN " . $db_new . ".hotel_translation ht ON ht.translatable_id=h.id WHERE ht.locale='fr' AND ht.name LIKE '%" . $row['hotrl_b_name'] . "%'";
        $value = '';
        foreach ($dbh->query($q1, PDO::FETCH_ASSOC) as $value) {
            break;
        }
        if ($value != '') {
            $q1 = "INSERT INTO " . $db_new . ".combination_hotel_to_hotel (combination_hotel_id, hotel_id) VALUES (" . $id . ", " . $value['id'] . ")";
            $dbh->prepare($q1)->execute();
        }
    }

    if($row['hotrl_c_name']) {
        $q1 = "SELECT * FROM " . $db_new . ".hotel h JOIN " . $db_new . ".hotel_translation ht ON ht.translatable_id=h.id WHERE ht.locale='fr' AND ht.name LIKE '%" . $row['hotrl_c_name'] . "%'";
        $value = '';
        foreach ($dbh->query($q1, PDO::FETCH_ASSOC) as $value) {
            break;
        }
        if ($value != '') {
            $q1 = "INSERT INTO " . $db_new . ".combination_hotel_to_hotel (combination_hotel_id, hotel_id) VALUES (" . $id . ", " . $value['id'] . ")";
            $dbh->prepare($q1)->execute();
        }
    }

    if($row['hotrl_d_name']) {
        $q1 = "SELECT * FROM " . $db_new . ".hotel h JOIN " . $db_new . ".hotel_translation ht ON ht.translatable_id=h.id WHERE ht.locale='fr' AND ht.name LIKE '%" . $row['hotrl_d_name'] . "%'";
        $value = '';
        foreach ($dbh->query($q1, PDO::FETCH_ASSOC) as $value) {
            break;
        }
        if ($value != '') {
            $q1 = "INSERT INTO " . $db_new . ".combination_hotel_to_hotel (combination_hotel_id, hotel_id) VALUES (" . $id . ", " . $value['id'] . ")";
            $dbh->prepare($q1)->execute();
        }
    }

    if($row['hotrl_e_name']) {
        $q1 = "SELECT * FROM " . $db_new . ".hotel h JOIN " . $db_new . ".hotel_translation ht ON ht.translatable_id=h.id WHERE ht.locale='fr' AND ht.name LIKE '%" . $row['hotrl_e_name'] . "%'";
        $value = '';
        foreach ($dbh->query($q1, PDO::FETCH_ASSOC) as $value) {
            break;
        }
        if ($value != '') {
            $q1 = "INSERT INTO " . $db_new . ".combination_hotel_to_hotel (combination_hotel_id, hotel_id) VALUES (" . $id . ", " . $value['id'] . ")";
            $dbh->prepare($q1)->execute();
        }
    }

    if($row['hotrl_f_name']) {
        $q1 = "SELECT * FROM " . $db_new . ".hotel h JOIN " . $db_new . ".hotel_translation ht ON ht.translatable_id=h.id WHERE ht.locale='fr' AND ht.name LIKE '%" . $row['hotrl_f_name'] . "%'";
        $value = '';
        foreach ($dbh->query($q1, PDO::FETCH_ASSOC) as $value) {
            break;
        }
        if ($value != '') {
            $q1 = "INSERT INTO " . $db_new . ".combination_hotel_to_hotel (combination_hotel_id, hotel_id) VALUES (" . $id . ", " . $value['id'] . ")";
            $dbh->prepare($q1)->execute();
        }
    }

    if($row['hotrl_g_name']) {
        $q1 = "SELECT * FROM " . $db_new . ".hotel h JOIN " . $db_new . ".hotel_translation ht ON ht.translatable_id=h.id WHERE ht.locale='fr' AND ht.name LIKE '%" . $row['hotrl_g_name'] . "%'";
        $value = '';
        foreach ($dbh->query($q1, PDO::FETCH_ASSOC) as $value) {
            break;
        }
        if ($value != '') {
            $q1 = "INSERT INTO " . $db_new . ".combination_hotel_to_hotel (combination_hotel_id, hotel_id) VALUES (" . $id . ", " . $value['id'] . ")";
            $dbh->prepare($q1)->execute();
        }
    }

    if($row['hotrl_h_name']) {
        $q1 = "SELECT * FROM " . $db_new . ".hotel h JOIN " . $db_new . ".hotel_translation ht ON ht.translatable_id=h.id WHERE ht.locale='fr' AND ht.name LIKE '%" . $row['hotrl_h_name'] . "%'";
        $value = '';
        foreach ($dbh->query($q1, PDO::FETCH_ASSOC) as $value) {
            break;
        }
        if ($value != '') {
            $q1 = "INSERT INTO " . $db_new . ".combination_hotel_to_hotel (combination_hotel_id, hotel_id) VALUES (" . $id . ", " . $value['id'] . ")";
            $dbh->prepare($q1)->execute();
        }
    }






}


echo '$i='.$i.' $n='.$n."\n";
echo "\n";
print_r('DONE');
echo "\n";
die();

