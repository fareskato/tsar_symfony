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

$query = 'REPLACE INTO '.$db_new.'.product_packs
(id, label)
(SELECT n.nid,
fdfl.field_label_value as label

FROM   '.$db_old.'.node n
LEFT JOIN '.$db_old.'.field_data_field_label fdfl ON fdfl.entity_id=n.nid


WHERE n.`type` = "product_pack")';
$dbh->query($query)->execute();

$query = 'REPLACE INTO '.$db_new.'.product_packs_translation
(id, translatable_id, active, locale, name)
(SELECT 
n.nid, 
n.nid, 
n.status as active,
"fr" as locale,
fdftf.title_field_value as name
FROM  '.$db_old.'.node n
LEFT JOIN '.$db_old.'.field_data_title_field fdftf ON fdftf.entity_id = n.nid


WHERE n.type = "product_pack")';
$dbh->query($query)->execute();


$query = 'SELECT n.nid,
fdfc.field_city_value as ville,
fdfatb.field_ajouter_transfert_blocjour_target_id as transfer_one,
fdft3.field_transfert_3_7_pax_blocjour_target_id as transfer_two,
fdft8.field_transfert_8_pax_blocjour_target_id as transfer_three

FROM   '.$db_old.'.node n
LEFT JOIN '.$db_old.'.field_data_field_city fdfc ON fdfc.entity_id=n.nid
LEFT JOIN '.$db_old.'.field_data_field_transfert_bloc_jour fdftbj ON fdftbj.entity_id=n.nid
LEFT JOIN '.$db_old.'.field_data_field_ajouter_transfert_blocjour fdfatb ON fdfatb.entity_id = fdftbj.field_transfert_bloc_jour_value
LEFT JOIN '.$db_old.'.field_data_field_transfert_3_7_pax_blocjour fdft3 ON fdft3.entity_id = fdftbj.field_transfert_bloc_jour_value
LEFT JOIN '.$db_old.'.field_data_field_transfert_8_pax_blocjour fdft8 ON fdft8.entity_id = fdftbj.field_transfert_bloc_jour_value
WHERE n.`type` = "product_pack"'; //print_r($query); exit;
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
    //print_r($row['transfer_one']); echo"\n";
    //  Проверяем на трансферы
    $transfer_one='NULL';
    if($row['transfer_one']){
        $q2 = "SELECT * FROM ".$db_new.".transferts WHERE id=".$row['transfer_one'];
        foreach ($dbh->query($q2, PDO::FETCH_ASSOC) as $value1) {
            break;
        }
        if(isset($value1)) {
            if ($value1['id']) {
                $transfer_one = $value1['id'];
            }
        }
    }

    $transfer_two='NULL';
    if($row['transfer_two']){
        $q2 = "SELECT * FROM ".$db_new.".transferts WHERE id=".$row['transfer_two'];
        foreach ($dbh->query($q2, PDO::FETCH_ASSOC) as $value2) {
            break;
        }
        if(isset($value2)) {
            if ($value2['id']) {
                $transfer_two = $value2['id'];
            }
        }
    }

    $transfer_three='NULL';
    if($row['transfer_three']){
        $q2 = "SELECT * FROM ".$db_new.".transferts WHERE id=".$row['transfer_three'];
        foreach ($dbh->query($q2, PDO::FETCH_ASSOC) as $value3) {
            break;
        }
        if(isset($value3)) {
            if ($value3['id']) {
                $transfer_three = $value3['id'];
            }
        }
    }

    $q = 'UPDATE ' . $db_new . '.product_packs SET ville="' . $ville . '"';
    if($transfer_one!='NULL'){
        $q=$q.', transfer_one="'.$transfer_one.'"';
    }
    if($transfer_two!='NULL'){
        $q=$q.', transfer_two="'.$transfer_two.'"';
    }
    if($transfer_three!='NULL'){
        $q=$q.', transfer_three="'.$transfer_three.'"';
    }
    $q = $q.' WHERE id=' . $row['nid'];
    //print_r($q); echo"\n";
    $dbh->query($q)->execute();

}
//  Импортируем продукты
$query="SELECT id FROM ".$db_new.".product_packs_to_product";
foreach ($dbh->query($query, PDO::FETCH_ASSOC) as $row) {
    $q = "DELETE FROM " . $db_new . ".`product_packs_to_product` WHERE (`id` = " . $row['id'] . ")";
    $dbh->query($q);
    //print_r($q); echo"\n"; exit;
}
$q='';
$query='SELECT n.nid,
fdfap.field_ajouter_produit_target_id as p_id,
fdfpbj.delta as delta,
n2.type as type


FROM   '.$db_old.'.node n
LEFT JOIN '.$db_old.'.field_data_field_produits_bloc_jour fdfpbj ON fdfpbj.entity_id=n.nid
LEFT JOIN '.$db_old.'.field_data_field_ajouter_produit fdfap ON fdfap.entity_id = fdfpbj.field_produits_bloc_jour_value
LEFT JOIN '.$db_old.'.node n2 ON n2.nid = fdfap.field_ajouter_produit_target_id

WHERE n.`type` = "product_pack"';
$povtor_key=array();
foreach ($dbh->query($query, PDO::FETCH_ASSOC) as $row) {
    $key = $row['nid'].'_'.$row['delta'];
    $povtor_key[$key]=$row;
}
print_r(count($povtor_key)); echo"\n"; //exit;
$i=0;
foreach ($povtor_key as $row) { $i++;
    if ($row['type'] == 'tickets_musee') {
        $q11='';
        $q11 = "INSERT INTO " . $db_new . ".product_packs_to_product (id, product_packs, visa, train, assurance, tickets_de_musee, guide_touristique, autre_produit, `position`) 
        VALUES(NULL, " . $row['nid'] . ", NULL, NULL, NULL, " . $row['p_id'] . ", NULL, NULL, " . $row['delta'] . ")";
        $dbh->query($q11)->execute();
    } elseif ($row['type'] == 'guide_touristique') {
        $q12='';
        $q12 = "INSERT INTO " . $db_new . ".product_packs_to_product (id, product_packs, visa, train, assurance, tickets_de_musee, guide_touristique, autre_produit, `position`) 
        VALUES(NULL, " . $row['nid'] . ", NULL, NULL, NULL, NULL, " . $row['p_id'] . ", NULL, " . $row['delta'] . ")";
        $dbh->query($q12)->execute();
    } elseif ($row['type'] == 'autre_produit') {
        $q13='';
        $q13 = "INSERT INTO " . $db_new . ".product_packs_to_product (id, product_packs, visa, train, assurance, tickets_de_musee, guide_touristique, autre_produit, `position`) 
        VALUES(NULL, " . $row['nid'] . ", NULL, NULL, NULL, NULL, NULL, " . $row['p_id'] . ", " . $row['delta'] . ")";
        $dbh->query($q13)->execute();
    } else {
        echo 'ERROR!!!! -- ' . $row['type'] . "\n";
    }

}
print_r($i); echo"\n"; exit;
echo '<pre>', "\n";
print_r('DONE');
echo '</pre>', "\n";
die();

