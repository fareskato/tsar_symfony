<?


use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\StreamOutput;

set_time_limit(0);

$host = '';
$user = 'dev';
$pass = '';


$db_old = 'tsar_devis';
//$db_old = 'devis';
$db_new = 'tsar';
/*$loader = require __DIR__.'/../vendor/autoload.php';
$output = new StreamOutput(fopen('php://memory', 'r+', false), true, StreamOutput::VERBOSITY_NORMAL);
$progress = new ProgressBar($output, 50);
$progress->start();
$i = 0;
while ($i++ < 50) {
    $progress->advance();
$progress->finish();*/
$dbh = new PDO('mysql:host', $user, $pass);


$query = 'REPLACE INTO '.$db_new.'.transferts
(id, label, excel_title, excel_description, custom_id, phone, number_passengers, vehicle)
(SELECT n.nid,
n.title as label,
fdfet.field_excel_title_value as excel_title,
fdfed.field_excel_description_value as excel_description,
fdfci.field_custom_id_value as custom_id,
fdft.field_telephone_value as phone,
fdfmdp.field_nombre_de_passagers_value as number_passengers,
fdftv.field_transfert_vehicule_value as vehicle
FROM   '.$db_old.'.node n
LEFT JOIN '.$db_old.'.field_data_field_excel_title fdfet ON fdfet.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_data_field_excel_description fdfed ON fdfed.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_data_field_custom_id fdfci ON fdfci.entity_id = n.nid

LEFT JOIN '.$db_old.'.field_data_field_info_sup_vouchers fdfisv ON fdfisv.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_data_field_telephone fdft ON fdft.entity_id = fdfisv.field_info_sup_vouchers_value

LEFT JOIN '.$db_old.'.field_data_field_description_transfert fdfdt ON fdfdt.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_data_field_nombre_de_passagers fdfmdp ON fdfmdp.entity_id = fdfdt.field_description_transfert_value
LEFT JOIN '.$db_old.'.field_data_field_transfert_vehicule fdftv ON fdftv.entity_id = fdfdt.field_description_transfert_value

WHERE n.`type` = "transferts")'; //print_r($query); exit;
$dbh->query($query)->execute();

$query = 'REPLACE INTO '.$db_new.'.transferts_translation
(id, translatable_id, active, locale, commentaires, informations_supplementaires, information_utile)
(SELECT 
n.nid, 
n.nid, 
n.status as active,
"fr" as locale,
fdftc.field_transfert_commentaires_value as commentaires,
fdfis.field_informations_sup_value as informations_supplementaires,
fdfiu.field_information_utile_value as information_utile
FROM  '.$db_old.'.node n
LEFT JOIN '.$db_old.'.field_data_field_description_transfert fdft ON fdft.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_data_field_transfert_commentaires fdftc ON fdftc.entity_id = fdft.field_description_transfert_value

LEFT JOIN '.$db_old.'.field_data_field_info_sup_vouchers fdfisv ON fdfisv.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_data_field_informations_sup fdfis ON fdfis.entity_id = fdfisv.field_info_sup_vouchers_value

LEFT JOIN '.$db_old.'.field_data_field_info_sup_agents fdfisa ON fdfisa.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_data_field_information_utile fdfiu ON fdfiu.entity_id = fdfisa.field_info_sup_agents_value

WHERE n.type = "transferts")'; //print_r($query); echo"\n"; exit;
$dbh->query($query)->execute();

$query = 'SELECT n.nid,
fdftdt.field_type_de_transfert_value as type_transfert,
fdfpda.field_point_d_arrivee_value as city_arrival,
fdfpdp.field_point_de_depart_value as departure_city,
fdfvh.field_ville_hotel_value as ville
FROM   '.$db_old.'.node n
LEFT JOIN '.$db_old.'.field_data_field_description_transfert fdfdt ON fdfdt.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_data_field_type_de_transfert fdftdt ON fdftdt.entity_id = fdfdt.field_description_transfert_value
LEFT JOIN '.$db_old.'.field_data_field_point_d_arrivee fdfpda ON fdfpda.entity_id = fdfdt.field_description_transfert_value
LEFT JOIN '.$db_old.'.field_data_field_point_de_depart fdfpdp ON fdfpdp.entity_id = fdfdt.field_description_transfert_value
LEFT JOIN '.$db_old.'.field_data_field_ville_hotel fdfvh ON fdfvh.entity_id = fdfdt.field_description_transfert_value
WHERE n.`type` = "transferts"';
$array=array();
foreach ($dbh->query($query, PDO::FETCH_ASSOC) as $row) {
    $value='NULL';
    $q1="SELECT * FROM ".$db_new.".`location_translation` nt WHERE nt.`city` LIKE '%".$row['departure_city']."%' LIMIT 50";
    foreach ($dbh->query($q1, PDO::FETCH_ASSOC) as $value) {
        break;
    }
    $departure_city='NULL';
    if($value) {
        if (!empty($value['translatable_id'])) {
            $departure_city = $value['translatable_id'];
        }
    }
    $value=NULL;
    $q1="SELECT * FROM ".$db_new.".`location_translation` nt WHERE nt.`city` LIKE '%".$row['city_arrival']."%' LIMIT 50";
    foreach ($dbh->query($q1, PDO::FETCH_ASSOC) as $value) {
        break;
    }
    $city_arrival='NULL';
    if($value) {
        if (!empty($value['translatable_id'])) {
            $city_arrival = $value['translatable_id'];
        }
    }
    $value=NULL;
    $q1="SELECT * FROM ".$db_new.".`location_translation` nt WHERE nt.`city` LIKE '%".$row['ville']."%' LIMIT 50";
    foreach ($dbh->query($q1, PDO::FETCH_ASSOC) as $value) {
        break;
    }
    $ville='NULL';
    if($value) {
        if (!empty($value['translatable_id'])) {
            $ville = $value['translatable_id'];
        }
    }
    $type_transfert = NULL;
    if($row['type_transfert']=='Apt'){
        $type_transfert=1;
    }
    if($row['type_transfert']=='Gare'){
        $type_transfert=2;
    }
    if($row['type_transfert']=='Intra'){
        $type_transfert=3;
    }
    if($row['type_transfert']=='Inter'){
        $type_transfert=4;
    }
    $q = 'UPDATE ' . $db_new . '.transferts SET departure_city=' . $departure_city . ', city_arrival=' . $city_arrival . ', ville=' . $ville . ', type_transfert='.$type_transfert.' WHERE id=' . $row['nid'];
    //print_r($q); echo"\n";
    $dbh->query($q)->execute();

}
//exit;


echo '<pre>', "\n";
print_r('DONE');
echo '</pre>', "\n";
die();

