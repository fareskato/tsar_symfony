<?


use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\StreamOutput;

set_time_limit(0);

$host = '';
$user = 'dev';
$pass = '';


$db_old = 'tsar_drupal';
//$db_old = 'website';
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




/*$query = 'SELECT n.nid, fp.field_event_date_value as date1, fp.field_event_date_value2 as date2
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_event_date fp ON fp.entity_id = n.nid
WHERE n.type = "event" AND fp.field_event_date_value IS NOT NULL';
$array=array();
foreach ($dbh->query($query, PDO::FETCH_ASSOC) as $row) {
  $start = explode("T", $row['date1']);
  $start[0] = explode("-", $start[0]);
  $start[1] = str_replace(":", "", $start[1]);
  $start = $start[0][0] . $start[0][1] . $start[0][2] . $start[1];
  $start = substr($start, 0, -2);
  $end = explode("T", $row['date2']);
  $end[0] = explode("-", $end[0]);
  $end[1] = str_replace(":", "", $end[1]);
  $end = $end[0][0] . $end[0][1] . $end[0][2] . $end[1];
  $end = substr($end, 0, -2);
  //print_r($start); exit;
  //$q = 'UPDATE ' . $db_new . '.event SET start="' . $start . '", end="' . $end . '" WHERE id=' . $row['nid'];
  $array[$row['nid']]=array('start'=>$start, 'end'=>$end);
  //$dbh->query($q)->execute();

}

foreach($array as $key=>$value){
    $insert = 'INSERT INTO ' . $db_new . '.event_to_date  (id, event, date_start, date_stop) VALUES (' . $key . ', ' . $key . ', ' . $value['start'] . ', ' . $value['end'] . ') ';
    $dbh->query($insert)->execute();
}*/

$query = '
REPLACE INTO ' . $db_new . '.event
(id, image, image_background,image_miniature,location, mini_groupe, minigroup_prix_euros, minigroup_prix_rubles, email, event_type, price_displayed, price_flexibility)
(SELECT n.nid,
fdfcf.field_central_photo_fid as image,
fdfbp.field_background_photo_fid as image_background,
fdflf.field_list_photo_fid as image_miniature,
fdfl.field_location_lid as location,
fdfmg.field_is_minigroup_value as mini_groupe,
fdfeu.field_minigroup_prix_in_euros_value as minigroup_prix_euros,
fdfru.field_minigroup_prix_in_rubles_value as minigroup_prix_rubles,
fdfee.field_event_email_value as email,
fdfet.field_event_type_target_id as event_type,
fdfdpp.field_displayed_product_price_value as price_displayed,
fdfpf.field_price_flexibility_value as price_flexibility
FROM  ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_central_photo fdfcf ON fdfcf.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_field_background_photo fdfbp  ON fdfbp.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_field_list_photo fdflf ON fdflf.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_field_location fdfl ON fdfl.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_field_is_minigroup fdfmg ON fdfmg.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_field_minigroup_prix_in_euros fdfeu ON fdfeu.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_field_minigroup_prix_in_rubles fdfru ON fdfru.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_field_event_email fdfee ON fdfee.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_field_event_type fdfet ON fdfet.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_field_displayed_product_price fdfdpp ON fdfdpp.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_field_price_flexibility fdfpf ON fdfpf.entity_id = n.nid
WHERE n.type = "event")
';
//print_r($query); exit;
$dbh->query($query)->execute();

//echo'11111'."\n"; exit;
$query = 'SELECT n.nid as id
FROM  ' . $db_old . '.node n
WHERE n.type = "event"';
foreach ($dbh->query($query, PDO::FETCH_ASSOC) as $row) {
    $insert = 'INSERT INTO ' . $db_new . '.event_to_related_product  (id, visit, extension, voyage, event) VALUES (' . $row['id'] . ', NULL, NULL, NULL, NULL)';
    //print_r($insert); exit;
    $stmt = $dbh->prepare($insert);
    $stmt->execute();
}
//echo'22222'."\n"; exit;

$query = 'SELECT n.nid, fp.field_event_date_value as date1, fp.field_event_date_value2 as date2
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_event_date fp ON fp.entity_id = n.nid
WHERE n.type = "event" AND fp.field_event_date_value IS NOT NULL';
$array=array();
foreach ($dbh->query($query, PDO::FETCH_ASSOC) as $row) {
    $start = explode("T", $row['date1']);
    $start[0] = explode("-", $start[0]);
    $start[1] = str_replace(":", "", $start[1]);
    $start = $start[0][0] . $start[0][1] . $start[0][2] . $start[1];
    $start = substr($start, 0, -2);
    $end = explode("T", $row['date2']);
    $end[0] = explode("-", $end[0]);
    $end[1] = str_replace(":", "", $end[1]);
    $end = $end[0][0] . $end[0][1] . $end[0][2] . $end[1];
    $end = substr($end, 0, -2);
    //print_r($start); exit;
    $q = 'UPDATE ' . $db_new . '.event SET start="' . $start . '", end="' . $end . '" WHERE id=' . $row['nid'];
    $dbh->query($q)->execute();
    $array[$row['nid']]=array('start'=>$start, 'end'=>$end);
}
foreach($array as $key=>$value){
    $insert = 'INSERT INTO ' . $db_new . '.event_to_date  (id, event, date_start, date_stop) VALUES (' . $key . ', ' . $key . ', ' . $value['start'] . ', ' . $value['end'] . ') ';
    $dbh->query($insert)->execute();
}

$query = 'SELECT n.nid, fp.field_content_linked_event_target_id as target
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_content_linked_event fp ON fp.entity_id = n.nid
WHERE n.type = "event"';
foreach ($dbh->query($query, PDO::FETCH_ASSOC) as $row) {
    // определить кому пренадлежит таргет
    $sub_query = 'SELECT * FROM ' . $db_old . '.node n WHERE n.nid=' . $row['target'];

    if ($row['target'] != '') {
        foreach ($dbh->query($sub_query, PDO::FETCH_ASSOC) as $data) {
            if ($data['type'] == 'visite') {
                $insert = 'UPDATE ' . $db_new . '.event_to_related_product SET visit=' . $row['target'] . ', event=' . $row['nid'] . ' WHERE id=' . $row['nid'];
                //$insert = 'INSERT INTO ' . $db_new . '.event_to_related_product  (id, visit, extension, voyage, event) VALUES (NULL, ' . $row['target'] . ', NULL, NULL, ' . $row['nid'] . ') ';
            }
            if ($data['type'] == 'escapade') {
                $insert = 'UPDATE ' . $db_new . '.event_to_related_product SET extension=' . $row['target'] . ', event=' . $row['nid'] . ' WHERE id=' . $row['nid'];
                //$insert = 'INSERT INTO ' . $db_new . '.event_to_related_product  (id, visit, extension, voyage, event) VALUES (NULL, NULL, ' . $row['target'] . ', NULL, ' . $row['nid'] . ') ';
            }
            if ($data['type'] == 'voyage') {
                $insert = 'UPDATE ' . $db_new . '.event_to_related_product SET voyage=' . $row['target'] . ', event=' . $row['nid'] . ' WHERE id=' . $row['nid'];
                //$insert = 'INSERT INTO ' . $db_new . '.event_to_related_product  (id, visit, extension, voyage, event) VALUES (NULL, NULL, NULL, ' . $row['target'] . ', ' . $row['nid'] . ') ';
            }
            //print_r($insert); exit;
            $stmt = $dbh->prepare($insert);
            $stmt->execute();
        }
    }
}

$query = '
REPLACE INTO ' . $db_new . '.event_translation
(id, translatable_id,name, headline_liste, body_summary,body,active, locale, slug, introduction, service_details, text_under_price, show_times)
(SELECT 
n.nid, 
n.nid, 
n.title as name,
fdflh.field_list_headline_value as headline_liste,
fdb.body_summary AS body_summary, 
fdb.body_value as body,
n.status as active,
"fr" as locale,
REPLACE(ua.alias, "evenement\/", "") AS slug,
fdfi.field_introduction_value as introduction,
fdfds.field_details_service_value as service_details,
fdftulp.field_texte_under_le_prix_value as text_under_price,
fdfdh.field_displayed_hours_value as show_times
FROM  ' . $db_old . '.node n
LEFT JOIN  ' . $db_old . '.field_data_field_list_headline fdflh ON fdflh.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_body fdb ON fdb.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.url_alias ua ON ua.source = CONCAT("node/",n.nid)
LEFT JOIN  ' . $db_old . '.field_data_field_introduction fdfi ON fdfi.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_field_details_service fdfds ON fdfds.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_field_texte_under_le_prix fdftulp ON fdftulp.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_field_displayed_hours fdfdh ON fdfdh.entity_id = n.nid
WHERE n.type = "event")
';
$dbh->query($query)->execute();

$query = '
REPLACE INTO ' . $db_new . '.event_to_files
(event_id,files_id)
(SELECT n.nid, fp.field_photos_fid
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_photos fp ON fp.entity_id = n.nid
WHERE n.type = "event" AND fp.field_photos_fid IS NOT NULL
)
';
$dbh->query($query)->execute();

$query = '
REPLACE INTO ' . $db_new . '.event_to_destination
(event_id,destination_id)
(SELECT n.nid, fdfld.field_link_destinations_target_id
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_link_destinations fdfld ON fdfld.entity_id = n.nid
LEFT JOIN ' . $db_old . '.node n2 ON fdfld.field_link_destinations_target_id = n2.nid
WHERE n.type = "event" AND fdfld.field_link_destinations_target_id IS NOT NULL AND n2.nid IS NOT NULL )
';
$dbh->query($query)->execute();

$query = '
REPLACE INTO ' . $db_new . '.event_to_day
(event,day, position)
(SELECT n.nid, fdfld.field_link_dayblocks_target_id as day, fdfld.delta as position
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_link_dayblocks fdfld ON fdfld.entity_id = n.nid
LEFT JOIN ' . $db_old . '.node n2 ON fdfld.field_link_dayblocks_target_id = n2.nid
WHERE n.type = "event" AND fdfld.field_link_dayblocks_target_id IS NOT NULL AND n2.nid IS NOT NULL )
';
$dbh->query($query)->execute();

$query = '
REPLACE INTO ' . $db_new . '.event_content_to_destination
(event_id,destination_id)
(SELECT n.nid, fdfld.field_link_destinations_target_id
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_link_destinations fdfld ON fdfld.entity_id = n.nid
LEFT JOIN ' . $db_old . '.node n2 ON fdfld.field_link_destinations_target_id = n2.nid
WHERE n.type = "event" AND fdfld.field_link_destinations_target_id IS NOT NULL AND n2.nid IS NOT NULL )
';
$dbh->query($query)->execute();


echo '<pre>', "\n";
print_r('DONE');
echo '</pre>', "\n";
die();

