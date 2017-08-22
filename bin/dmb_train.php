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


$query = 'REPLACE INTO '.$db_new.'.train
(id, label, excel_title, excel_description, custom_id, period, categorie, type_customer, phone, type_group)
(SELECT n.nid,
n.title as label,
fdfet.field_excel_title_value as excel_title,
fdfed.field_excel_description_value as excel_description,
fdfci.field_custom_id_value as custom_id,
fdfpt.field_periode_train_value as period,
fdfct.field_categorie_train_value as categorie,
fdftct.field_type_clientele_train_value as type_customer,
fdfph.field_telephone_value as phone,
fdftf.field_type_formule_value as type_group
FROM   '.$db_old.'.node n
LEFT JOIN '.$db_old.'.field_data_field_excel_title fdfet ON fdfet.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_data_field_excel_description fdfed ON fdfed.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_data_field_custom_id fdfci ON fdfci.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_data_field_train fdft ON fdft.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_data_field_periode_train fdfpt ON fdfpt.entity_id = fdft.field_train_value
LEFT JOIN '.$db_old.'.field_data_field_categorie_train fdfct ON fdfct.entity_id = fdft.field_train_value
LEFT JOIN '.$db_old.'.field_data_field_type_clientele_train fdftct ON fdftct.entity_id = fdft.field_train_value
LEFT JOIN '.$db_old.'.field_data_field_info_sup_vouchers fdfisv ON fdfisv.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_data_field_telephone fdfph ON fdfph.entity_id = fdfisv.field_info_sup_vouchers_value
LEFT JOIN '.$db_old.'.field_data_field_info_sup_agents fdfisa ON fdfisa.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_data_field_type_formule fdftf ON fdftf.entity_id = fdfisa.field_info_sup_agents_value
WHERE n.`type` = "train")';
$dbh->query($query)->execute();

$query = 'REPLACE INTO '.$db_new.'.train_translation
(id, translatable_id, active, locale, commentaires, informations_supplementaires, information_utile)
(SELECT 
n.nid, 
n.nid, 
n.status as active,
"fr" as locale,
fdfct.field_commentaires_train_value as commentaires,
fdfif.field_informations_sup_value as informations_supplementaires,
fdfiu.field_information_utile_value as information_utile
FROM  '.$db_old.'.node n
LEFT JOIN '.$db_old.'.field_data_field_train fdft ON fdft.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_data_field_commentaires_train fdfct ON fdfct.entity_id = fdft.field_train_value
LEFT JOIN '.$db_old.'.field_data_field_info_sup_vouchers fdfisv ON fdfisv.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_revision_field_informations_sup fdfif ON fdfif.entity_id = fdfisv.field_info_sup_vouchers_value
LEFT JOIN '.$db_old.'.field_data_field_info_sup_agents fdfisa ON fdfisa.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_data_field_information_utile fdfiu ON fdfiu.entity_id = fdfisa.field_info_sup_agents_value
WHERE n.type = "train")';
$dbh->query($query)->execute();

$query = 'SELECT 
n.nid,
fdfvh.field_ville_hotel_value as departure_city,
fdfvat.field_ville_arrivee_train_value as city_arrival
FROM  '.$db_old.'.node n
LEFT JOIN '.$db_old.'.field_data_field_train fdft ON fdft.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_data_field_ville_hotel fdfvh ON fdfvh.entity_id = fdft.field_train_value
LEFT JOIN '.$db_old.'.field_data_field_ville_arrivee_train fdfvat ON fdfvat.entity_id = fdft.field_train_value
WHERE n.type = "train"';
$array=array();
foreach ($dbh->query($query, PDO::FETCH_ASSOC) as $row) {
    $q1="SELECT * FROM ".$db_new.".`location_translation` nt WHERE nt.`city` LIKE '%".$row['departure_city']."%' LIMIT 50";
    foreach ($dbh->query($q1, PDO::FETCH_ASSOC) as $value) {
        break;
    }
    $departure_city=NULL;
    if($value['translatable_id']){
        $departure_city=$value['translatable_id'];
    }
    $q1="SELECT * FROM ".$db_new.".`location_translation` nt WHERE nt.`city` LIKE '%".$row['city_arrival']."%' LIMIT 50";
    foreach ($dbh->query($q1, PDO::FETCH_ASSOC) as $value) {
        break;
    }
    $city_arrival=NULL;
    if($value['translatable_id']){
        $city_arrival=$value['translatable_id'];
    }
    $q = 'UPDATE ' . $db_new . '.train SET departure_city="' . $departure_city . '", city_arrival="' . $city_arrival . '" WHERE id=' . $row['nid'];
    $dbh->query($q)->execute();

}


echo '<pre>', "\n";
print_r('DONE');
echo '</pre>', "\n";
die();

