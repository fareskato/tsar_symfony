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


$query = 'REPLACE INTO '.$db_new.'.assurance
(id, label, excel_title, excel_description, custom_id, phone, type_group, total_price_stay, duration_insurance, type_dassurance)
(SELECT n.nid,
n.title as label,
fdfet.field_excel_title_value as excel_title,
fdfed.field_excel_description_value as excel_description,
fdfci.field_custom_id_value as custom_id,
fdft1.field_telephone_value as phone,
fdftf.field_type_formule_value as type_group,
fdfpsa.field_prix_sejour_asssurance_value as total_price_stay,
fdfps.field_periode_assurance_value as duration_insurance,
fdfta.field_type_assurance_value as type_dassurance
FROM   '.$db_old.'.node n
LEFT JOIN '.$db_old.'.field_data_field_excel_title fdfet ON fdfet.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_data_field_excel_description fdfed ON fdfed.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_data_field_custom_id fdfci ON fdfci.entity_id = n.nid

LEFT JOIN '.$db_old.'.field_data_field_info_sup_vouchers fdfisv ON fdfisv.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_data_field_telephone fdft1 ON fdft1.entity_id = fdfisv.field_info_sup_vouchers_value

LEFT JOIN '.$db_old.'.field_data_field_info_sup_agents fdfisa ON fdfisa.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_data_field_type_formule fdftf ON fdftf.entity_id = fdfisa.field_info_sup_agents_value

LEFT JOIN '.$db_old.'.field_data_field_assurance fdft ON fdft.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_data_field_prix_sejour_asssurance fdfpsa ON fdfpsa.entity_id = fdft.field_assurance_value
LEFT JOIN '.$db_old.'.field_data_field_periode_assurance fdfps ON fdfps.entity_id = fdft.field_assurance_value
LEFT JOIN '.$db_old.'.field_data_field_type_assurance fdfta ON fdfta.entity_id = fdft.field_assurance_value

WHERE n.`type` = "assurance")'; //print_r($query); exit;
$dbh->query($query)->execute();

$query = 'REPLACE INTO '.$db_new.'.assurance_translation
(id, translatable_id, active, locale, commentaires, informations_supplementaires, information_utile, name)
(SELECT 
n.nid, 
n.nid, 
n.status as active,
"fr" as locale,
fdftc.field_type_formule_value as commentaires,
fdfis.field_informations_sup_value as informations_supplementaires,
fdfiu.field_information_utile_value as information_utile,
fdfldp.field_label_du_produit_value as name
FROM  '.$db_old.'.node n
LEFT JOIN '.$db_old.'.field_data_field_assurance fdft ON fdft.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_data_field_type_formule fdftc ON fdftc.entity_id = fdft.field_assurance_value
LEFT JOIN '.$db_old.'.field_data_field_label_du_produit fdfldp ON fdfldp.entity_id = fdft.field_assurance_value

LEFT JOIN '.$db_old.'.field_data_field_info_sup_vouchers fdfisv ON fdfisv.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_data_field_informations_sup fdfis ON fdfis.entity_id = fdfisv.field_info_sup_vouchers_value

LEFT JOIN '.$db_old.'.field_data_field_info_sup_agents fdfisa ON fdfisa.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_data_field_information_utile fdfiu ON fdfiu.entity_id = fdfisa.field_info_sup_agents_value

WHERE n.type = "assurance")';
$dbh->query($query)->execute();



echo '<pre>', "\n";
print_r('DONE');
echo '</pre>', "\n";
die();

