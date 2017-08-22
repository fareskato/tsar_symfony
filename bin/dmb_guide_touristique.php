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


/*$query = 'REPLACE INTO '.$db_new.'.product_packs
(id, label, transfer_one, transfer_two, transfer_three)
(SELECT n.nid,
fdfl.field_label_value as label,
fdfatb.field_ajouter_transfert_blocjour_target_id as transfer_one,
fdft3.field_transfert_3_7_pax_blocjour_target_id as transfer_two,
fdft8.field_transfert_8_pax_blocjour_target_id as transfer_three

FROM   '.$db_old.'.node n
LEFT JOIN '.$db_old.'.field_data_field_label fdfl ON fdfl.entity_id=n.nid
LEFT JOIN '.$db_old.'.field_data_field_transfert_bloc_jour fdftbj ON fdftbj.entity_id=n.nid
LEFT JOIN '.$db_old.'.field_data_field_ajouter_transfert_blocjour fdfatb ON fdfatb.entity_id = fdftbj.field_transfert_bloc_jour_value
LEFT JOIN '.$db_old.'.field_data_field_transfert_3_7_pax_blocjour fdft3 ON fdft3.entity_id = fdftbj.field_transfert_bloc_jour_value
LEFT JOIN '.$db_old.'.field_data_field_transfert_8_pax_blocjour fdft8 ON fdft8.entity_id = fdftbj.field_transfert_bloc_jour_value

WHERE n.`type` = "product_pack")'; print_r($query); exit;
$dbh->query($query)->execute();*/

$query = 'REPLACE INTO '.$db_new.'.guide_touristique
(id, label, excel_title, excel_description, custom_id, phone, type_group, langue, type_guide, duree)
(SELECT n.nid,
fdtf.title_field_value as label,
fdfet.field_excel_title_value as excel_title,
fdfed.field_excel_description_value as excel_description,
fdfci.field_custom_id_value as custom_id,
fdft.field_telephone_value as phone,
fdftf.field_type_formule_value as type_group,
fdfl.field_langue_value as langue,
fdftdq.field_type_de_guide_value as type_guide,
fdfdg.field_duree_guide_value as duree


FROM   '.$db_old.'.node n
LEFT JOIN '.$db_old.'.field_data_field_excel_title fdfet ON fdfet.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_data_field_excel_description fdfed ON fdfed.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_data_field_custom_id fdfci ON fdfci.entity_id = n.nid

LEFT JOIN '.$db_old.'.field_data_title_field fdtf ON fdtf.entity_id = n.nid

LEFT JOIN '.$db_old.'.field_data_field_info_sup_vouchers fdfisv ON fdfisv.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_data_field_telephone fdft ON fdft.entity_id = fdfisv.field_info_sup_vouchers_value

LEFT JOIN '.$db_old.'.field_data_field_info_sup_agents fdfisa ON fdfisa.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_data_field_type_formule fdftf ON fdftf.entity_id = fdfisa.field_info_sup_agents_value


LEFT JOIN '.$db_old.'.field_data_field_guide_touristique fdfqt ON fdfqt.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_data_field_langue fdfl ON fdfl.entity_id = fdfqt.field_guide_touristique_value
LEFT JOIN '.$db_old.'.field_data_field_type_de_guide fdftdq ON fdftdq.entity_id = fdfqt.field_guide_touristique_value
LEFT JOIN '.$db_old.'.field_data_field_duree_guide fdfdg ON fdfdg.entity_id = fdfqt.field_guide_touristique_value

WHERE n.`type` = "guide_touristique")
';
$dbh->query($query)->execute();

$query = 'REPLACE INTO '.$db_new.'.guide_touristique_translation
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
WHERE n.`type` = "guide_touristique")';
$dbh->query($query)->execute();


$query = 'SELECT n.nid,
fdfc.field_city_value as ville

FROM   '.$db_old.'.node n
LEFT JOIN '.$db_old.'.field_data_field_city fdfc ON fdfc.entity_id=n.nid
WHERE n.`type` = "guide_touristique"'; //print_r($query); exit;
$array=array();
foreach ($dbh->query($query, PDO::FETCH_ASSOC) as $row) {
    $q1="SELECT * FROM ".$db_new.".`location_translation` nt WHERE nt.`city` LIKE '%".$row['ville']."%' LIMIT 50";
    foreach ($dbh->query($q1, PDO::FETCH_ASSOC) as $value) {
        break;
    }
    //print_r($value); exit;
    $ville='NULL';
    if($value['id']){
        $ville=$value['id'];
    }

    $q = 'UPDATE ' . $db_new . '.guide_touristique SET ville="' . $ville . '"';

    $q = $q.' WHERE id=' . $row['nid'];
    $dbh->query($q)->execute();

}
//  Импортируем продукты
$query='SELECT n.nid,
fdfap.field_ajouter_produit_target_id as p_id,
fdfpbj.delta as delta,
n2.type as type


FROM   devis.node n
LEFT JOIN devis.field_data_field_produits_bloc_jour fdfpbj ON fdfpbj.entity_id=n.nid
LEFT JOIN devis.field_data_field_ajouter_produit fdfap ON fdfap.entity_id = fdfpbj.field_produits_bloc_jour_value
LEFT JOIN devis.node n2 ON n2.nid = fdfap.field_ajouter_produit_target_id

WHERE n.`type` = "product_pack"';
echo '<pre>', "\n";
print_r('DONE');
echo '</pre>', "\n";
die();

