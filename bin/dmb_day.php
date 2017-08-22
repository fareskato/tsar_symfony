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

/**
 * DAY
 */
/*
$query = '
REPLACE INTO ' . $db_new . '.day
(id)
(SELECT n.nid
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_dayblock_content fdfdc ON fdfdc.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_dayblock_title fdfdt ON fdfdt.entity_id = n.nid
LEFT JOIN ' . $db_old . '.url_alias ua ON ua.source = CONCAT("node/",n.nid) 

WHERE n.type = "dayblock"  )
';
$dbh->query($query)->execute();
$query = '
REPLACE INTO ' . $db_new . '.day_translation
(id,translatable_id,locale,body,slug,name,title)
(SELECT n.nid, n.nid, "fr" as locale, fdfdc.field_dayblock_content_value, 
REPLACE(ua.alias, "bloc-jour\/", "") AS slug,
n.title, fdfdt.field_dayblock_title_value
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_dayblock_content fdfdc ON fdfdc.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_dayblock_title fdfdt ON fdfdt.entity_id = n.nid
LEFT JOIN ' . $db_old . '.url_alias ua ON ua.source = CONCAT("node/",n.nid) 

WHERE n.type = "dayblock"  )
';
$dbh->query($query)->execute();

// DAY TO FILES
$query = '
REPLACE INTO ' . $db_new . '.day_to_files
(day_id,files_id)
(SELECT n.nid, fp.field_dayblock_photos_fid
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_dayblock_photos fp ON fp.entity_id = n.nid
WHERE n.type = "dayblock" AND fp.field_dayblock_photos_fid IS NOT NULL
)
';
$dbh->query($query)->execute();

// DAY TO DESTINATION
$query = '
REPLACE INTO ' . $db_new . '.day_to_destination
(day_id,destination_id)
(SELECT n.nid, fdfld.field_link_destinations_target_id
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_link_destinations fdfld ON fdfld.entity_id = n.nid
LEFT JOIN ' . $db_old . '.node n2 ON fdfld.field_link_destinations_target_id = n2.nid
WHERE n.type = "dayblock" AND fdfld.field_link_destinations_target_id IS NOT NULL AND n2.nid IS NOT NULL )
';
$dbh->query($query)->execute();


// DAY TO HOTEL
$query = '
REPLACE INTO ' . $db_new . '.day_to_hotel
(day_id,hotel_id)
(SELECT n.nid, fdfh.field_hebergements_target_id
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_hebergements fdfh ON fdfh.entity_id = n.nid
LEFT JOIN ' . $db_old . '.node n2 ON fdfh.field_hebergements_target_id = n2.nid
WHERE n.type = "dayblock" AND fdfh.field_hebergements_target_id  )
';
$dbh->query($query)->execute();
*/

$query='SELECT n.nid as id,
fdfla.field_label_automatique_value as label,
fdtf.title_field_value as name,
fdfndlvbj.field_nom_de_la_ville_bloc_jour_value as ville,
fdfdbj.field_description_bloc_jour_value as description,
fdfbja1.field_bloc_jour_alternatif1_target_id as alt1,
fdtf1.title_field_value as alt1_name,
fdfbja2.field_bloc_jour_alternatif2_target_id as alt2,
fdtf2.title_field_value as alt2_name,
fdfatb.field_ajouter_transfert_blocjour_target_id as transfer_one,
fdft3.field_transfert_3_7_pax_blocjour_target_id as transfer_two,
fdft8.field_transfert_8_pax_blocjour_target_id as transfer_three,
fdfap.field_ajouter_produit_target_id as b_j_id,
n2.type as b_j_type

FROM   '.$db_old_devis.'.node n
LEFT JOIN '.$db_old_devis.'.field_data_field_label_automatique fdfla ON fdfla.entity_id=n.nid
LEFT JOIN '.$db_old_devis.'.field_data_title_field fdtf ON fdtf.entity_id=n.nid
LEFT JOIN '.$db_old_devis.'.field_data_field_nom_de_la_ville_bloc_jour fdfndlvbj ON fdfndlvbj.entity_id=n.nid
LEFT JOIN '.$db_old_devis.'.field_data_field_bloc_jour_alternatif1 fdfbja1 ON fdfbja1.entity_id=n.nid
LEFT JOIN '.$db_old_devis.'.field_data_field_bloc_jour_alternatif2 fdfbja2 ON fdfbja2.entity_id=n.nid
LEFT JOIN '.$db_old_devis.'.field_data_field_description_bloc_jour fdfdbj ON fdfdbj.entity_id=n.nid
LEFT JOIN '.$db_old_devis.'.field_data_field_transfert_bloc_jour fdftbj ON fdftbj.entity_id=n.nid
LEFT JOIN '.$db_old_devis.'.field_data_field_ajouter_transfert_blocjour fdfatb ON fdfatb.entity_id = fdftbj.field_transfert_bloc_jour_value
LEFT JOIN '.$db_old_devis.'.field_data_field_transfert_3_7_pax_blocjour fdft3 ON fdft3.entity_id = fdftbj.field_transfert_bloc_jour_value
LEFT JOIN '.$db_old_devis.'.field_data_field_transfert_8_pax_blocjour fdft8 ON fdft8.entity_id = fdftbj.field_transfert_bloc_jour_value

LEFT JOIN '.$db_old_devis.'.field_data_field_produits_bloc_jour fdfpbj ON fdfpbj.entity_id=n.nid
LEFT JOIN '.$db_old_devis.'.field_data_field_ajouter_produit fdfap ON fdfap.entity_id=fdfpbj.field_produits_bloc_jour_value

LEFT JOIN '.$db_old_devis.'.field_data_title_field fdtf1 ON fdtf1.entity_id=fdfbja1.field_bloc_jour_alternatif1_target_id
LEFT JOIN '.$db_old_devis.'.field_data_title_field fdtf2 ON fdtf2.entity_id=fdfbja2.field_bloc_jour_alternatif2_target_id
LEFT JOIN '.$db_old_devis.'.node n2 ON n2.nid = fdfap.field_ajouter_produit_target_id

WHERE n.`type` = "bloc_jour"';

$array=array();
$i=0;
$n=0;
foreach ($dbh->query($query, PDO::FETCH_ASSOC) as $row) {
    //print_r($row['name']); echo"\n";
    if($row['name']) {
        $q1 = "SELECT * FROM " . $db_new . ".day h JOIN " . $db_new . ".day_translation ht ON ht.translatable_id=h.id WHERE ht.locale='fr' AND (ht.name LIKE '%" . $row['name'] . "%' OR h.label LIKE '%" . $row['name'] . "%')";
        //print_r($q1); echo"\n";
        $value = '';
        foreach ($dbh->query($q1, PDO::FETCH_ASSOC) as $value) {
            break;
        }
        if ($value != '') {
            $q = "UPDATE " . $db_new . ".day_translation SET `name`=? WHERE translation_id=?";
            $dbh->prepare($q)->execute(array($row['name'], $value['id']));
            if ($value['body'] == '') {
                $q = "UPDATE " . $db_new . ".day_translation SET body=? WHERE translation_id=?";
                $dbh->prepare($q)->execute(array($row['description'], $value['id']));
            }
            $q = "UPDATE " . $db_new . ".day SET label=?, transfer_one=?, transfer_two=?, transfer_three=? WHERE id=?";
            $dbh->prepare($q)->execute(array($row['label'], $row['transfer_one'], $row['transfer_two'], $row['transfer_three'], $value['id']));
            //  Добавляем в таблицу связей дополнительных блок джуров...
            if ($row['alt1_name']) {
                $q1 = "SELECT * FROM " . $db_new . ".day h JOIN " . $db_new . ".day_translation ht ON ht.translatable_id=h.id WHERE ht.locale='fr' AND (ht.name LIKE '%" . $row['alt1_name'] . "%' OR h.label LIKE '%" . $row['alt1_name'] . "%')";
                $value1 = '';
                foreach ($dbh->query($q1, PDO::FETCH_ASSOC) as $value1) {
                    break;
                }
                if ($value1 != '') {
                    $q = "INSERT INTO " . $db_new . ".alternative_day_to_day (alternative_day_id, day_id) VALUES (?,?)";
                    $dbh->prepare($q)->execute(array($value1['id'], $value['id']));
                }
            }
            if ($row['alt2_name']) {
                $q1 = "SELECT * FROM " . $db_new . ".day h JOIN " . $db_new . ".day_translation ht ON ht.translatable_id=h.id WHERE ht.locale='fr' AND (ht.name LIKE '%" . $row['alt2_name'] . "%' OR h.label LIKE '%" . $row['alt2_name'] . "%')";
                $value1 = '';
                foreach ($dbh->query($q1, PDO::FETCH_ASSOC) as $value1) {
                    break;
                }
                if ($value1 != '') {
                    $q = "INSERT INTO " . $db_new . ".alternative_day_to_day (alternative_day_id, day_id) VALUES (?,?)";
                    $dbh->prepare($q)->execute(array($value1['id'], $value['id']));
                 }
            }
            //  теперь добавляем запись в таблицу day_to_product
            if($row['b_j_type']) {
                if ($row['b_j_type'] == 'tickets_musee') {
                    $q = "INSERT INTO " . $db_new . ".day_to_product (`day`, tickets_de_musee) VALUES (?,?)";
                    $dbh->prepare($q)->execute(array($value['id'], $row['b_j_id']));
                } elseif ($row['b_j_type'] == 'autre_produit') {
                    $q = "INSERT INTO " . $db_new . ".day_to_product (`day`, autre_produit) VALUES (?,?)";
                    $dbh->prepare($q)->execute(array($value['id'], $row['b_j_id']));
                } elseif ($row['b_j_type'] == 'guide_touristique') {
                    $q = "INSERT INTO " . $db_new . ".day_to_product (`day`, guide_touristique) VALUES (?,?)";
                    $dbh->prepare($q)->execute(array($value['id'], $row['b_j_id']));
                } elseif ($row['b_j_type'] == 'train') {
                    $q = "INSERT INTO " . $db_new . ".day_to_product (`day`, train) VALUES (?,?)";
                    $dbh->prepare($q)->execute(array($value['id'], $row['b_j_id']));
                } else {
                    echo 'type - ';
                    print_r($row['b_j_type']);
                    echo "\n";
                }
            }
            //print_r($value); exit;
            $n++;
        }else{ //echo"1111\n";
            //  Создаем новый день.
            $q="INSERT INTO " . $db_new . ".day (label, transfer_one, transfer_two, transfer_three) VALUES (?,?,?,?)";
            //print_r($q); print_r(array($row['label'], $row['transfer_one'], $row['transfer_two'], $row['transfer_three'])); exit;
            $dbh->prepare($q)->execute(array($row['label'], $row['transfer_one'], $row['transfer_two'], $row['transfer_three']));
            $id=$dbh->lastInsertId();
            if($id) {
                $q = "INSERT INTO " . $db_new . ".day_translation (id, translate_id, locale, `name`, body) VALUES (?,?,?,?,?)";
                $dbh->prepare($q)->execute(array($id, $id, 'fr', $row['label'], $row['description']));
                if ($row['alt1_name']) {
                    $q1 = "SELECT * FROM " . $db_new . ".day h JOIN " . $db_new . ".day_translation ht ON ht.translatable_id=h.id WHERE ht.locale='fr' AND (ht.name LIKE '%" . $row['alt1_name'] . "%' OR h.label LIKE '%" . $row['alt1_name'] . "%')";
                    $value1 = '';
                    foreach ($dbh->query($q1, PDO::FETCH_ASSOC) as $value1) {
                        break;
                    }
                    if ($value1 != '') {
                        $q = "INSERT INTO " . $db_new . ".alternative_day_to_day (alternative_day_id, day_id) VALUES (?,?)";
                        $dbh->prepare($q)->execute(array($value1['id'], $id));
                    }
                }
                if ($row['alt2_name']) {
                    $q1 = "SELECT * FROM " . $db_new . ".day h JOIN " . $db_new . ".day_translation ht ON ht.translatable_id=h.id WHERE ht.locale='fr' AND (ht.name LIKE '%" . $row['alt2_name'] . "%' OR h.label LIKE '%" . $row['alt2_name'] . "%')";
                    $value1 = '';
                    foreach ($dbh->query($q1, PDO::FETCH_ASSOC) as $value1) {
                        break;
                    }
                    if ($value1 != '') {
                        $q = "INSERT INTO " . $db_new . ".alternative_day_to_day (alternative_day_id, day_id) VALUES (?,?)";
                        $dbh->prepare($q)->execute(array($value1['id'], $id));
                    }
                }
                //  теперь добавляем запись в таблицу day_to_product
                if($row['b_j_type']) {
                    if ($row['b_j_type'] == 'tickets_musee') {
                        $q = "INSERT INTO " . $db_new . ".day_to_product (`day`, tickets_de_musee) VALUES (?,?)";
                        $dbh->prepare($q)->execute(array($id, $row['b_j_id']));
                    } elseif ($row['b_j_type'] == 'autre_produit') {
                        $q = "INSERT INTO " . $db_new . ".day_to_product (`day`, autre_produit) VALUES (?,?)";
                        $dbh->prepare($q)->execute(array($id, $row['b_j_id']));
                    } elseif ($row['b_j_type'] == 'guide_touristique') {
                        $q = "INSERT INTO " . $db_new . ".day_to_product (`day`, guide_touristique) VALUES (?,?)";
                        $dbh->prepare($q)->execute(array($id, $row['b_j_id']));
                    } elseif ($row['b_j_type'] == 'train') {
                        $q = "INSERT INTO " . $db_new . ".day_to_product (`day`, train) VALUES (?,?)";
                        $dbh->prepare($q)->execute(array($id, $row['b_j_id']));
                    } else {
                        echo 'type - ';
                        print_r($row['b_j_type']);
                        echo "\n";
                    }
                }
            }
        }
    }
    $i++;
}
echo '$I-'.$i.' $N-'.$n."\n";
echo "\n";
print_r('DONE');
echo "\n";
die();

