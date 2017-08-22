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


$query = 'REPLACE INTO '.$db_new.'.visa
(id, label, excel_title, excel_description, custom_id, phone, type_group, type_visa, urgence, visa_period)
(SELECT n.nid,
n.title as label,
fdfet.field_excel_title_value as excel_title,
fdfed.field_excel_description_value as excel_description,
fdfci.field_custom_id_value as custom_id,
fdfph.field_telephone_value as phone,
fdftf.field_type_formule_value as type_group,
fdftv.field_type_visa_value as type_visa,
fdfu.field_urgence_value as urgence,
fdfpv.field_periode_visa_value as visa_period
FROM   '.$db_old.'.node n
LEFT JOIN '.$db_old.'.field_data_field_excel_title fdfet ON fdfet.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_data_field_excel_description fdfed ON fdfed.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_data_field_custom_id fdfci ON fdfci.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_data_field_info_sup_agents fdfisa ON fdfisa.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_data_field_type_formule fdftf ON fdftf.entity_id = fdfisa.field_info_sup_agents_value
LEFT JOIN '.$db_old.'.field_data_field_info_sup_vouchers fdfisv ON fdfisv.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_data_field_telephone fdfph ON fdfph.entity_id = fdfisv.field_info_sup_vouchers_value
LEFT JOIN '.$db_old.'.field_data_field_visa fdfv ON fdfv.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_data_field_type_visa fdftv ON fdftv.entity_id = fdfv.field_visa_value
LEFT JOIN '.$db_old.'.field_data_field_urgence fdfu ON fdfu.entity_id = fdfv.field_visa_value
LEFT JOIN '.$db_old.'.field_data_field_periode_visa fdfpv ON fdfpv.entity_id = fdfv.field_visa_value
WHERE n.`type` = "visa")';
$dbh->query($query)->execute();

$query = 'REPLACE INTO '.$db_new.'.visa_translation
(id, translatable_id, active, locale, commentaires, informations_supplementaires, information_utile)
(SELECT 
n.nid, 
n.nid, 
n.status as active,
"fr" as locale,
fdfct.field_commentaires_visa_value as commentaires,
fdfif.field_informations_sup_value as informations_supplementaires,
fdfiu.field_information_utile_value as information_utile
FROM  '.$db_old.'.node n
LEFT JOIN '.$db_old.'.field_data_field_train fdft ON fdft.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_data_field_commentaires_visa fdfct ON fdfct.entity_id = fdft.field_train_value
LEFT JOIN '.$db_old.'.field_data_field_info_sup_vouchers fdfisv ON fdfisv.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_revision_field_informations_sup fdfif ON fdfif.entity_id = fdfisv.field_info_sup_vouchers_value
LEFT JOIN '.$db_old.'.field_data_field_info_sup_agents fdfisa ON fdfisa.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_data_field_information_utile fdfiu ON fdfiu.entity_id = fdfisa.field_info_sup_agents_value
WHERE n.type = "visa")';
$dbh->query($query)->execute();


echo '<pre>', "\n";
print_r('DONE');
echo '</pre>', "\n";
die();

