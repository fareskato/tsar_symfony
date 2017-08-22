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

$dbh = new PDO('mysql:host', $user, $pass);
/**
 * extension
 */
$query = '
REPLACE INTO ' . $db_new . '.extension
(id,image_background,image_thumbnail, location, starting_point, amount_days, mini_groupe, promoted_fronpage, minigroup_promotion_weight, minigroup_prix_euros, minigroup_prix_rubles, product_365, 
 tariffed_product,  prix_euro, prix_rouble)
(
SELECT n.nid,
fdfbp.field_background_photo_fid as image_background,
fdflf.field_list_photo_fid as image_thumbnail,
fdfl.field_location_lid as location,
fdfedd.field_escapade_dep_destinatio_target_id as starting_point,
fdfnd.field_number_days_value as amount_days,
fdfmg.field_is_minigroup_value as mini_groupe,
fdfpfp.field_promoted_on_fronpage_value as promoted_fronpage,
fdfwe.field_minigroup_promotion_weight_value as minigroup_promotion_weight,
fdfeu.field_minigroup_prix_in_euros_value as minigroup_prix_euros,
fdfru.field_minigroup_prix_in_rubles_value as minigroup_prix_rubles,
fdflp.field_linked_product_value as product_365,
fdffhp.field_has_price_value as tariffed_product,
fdfpve.field_price_value_euro_value as prix_euro,
fdfpvr.field_price_value_ruble_value as prix_rouble
FROM  ' . $db_old . '.node n
LEFT JOIN  ' . $db_old . '.field_data_field_background_photo fdfbp  ON fdfbp.entity_id = n.nid 
LEFT JOIN  ' . $db_old . '.field_data_field_list_photo fdflf ON fdflf.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_field_location fdfl ON fdfl.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_field_escapade_dep_destinatio fdfedd ON fdfedd.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_field_number_days fdfnd ON fdfnd.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_field_is_minigroup fdfmg ON fdfmg.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_field_promoted_on_fronpage fdfpfp ON fdfpfp.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_field_minigroup_promotion_weight fdfwe ON fdfwe.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_field_minigroup_prix_in_euros fdfeu ON fdfeu.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_field_minigroup_prix_in_rubles fdfru ON fdfru.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_field_linked_product fdflp ON fdflp.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_field_displayed_product_price fdfdpp ON fdfdpp.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_field_has_price fdffhp ON fdffhp.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_field_price_value_euro fdfpve ON fdfpve.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_field_price_value_ruble fdfpvr ON fdfpvr.entity_id = n.nid
WHERE n.type = "escapade")
';

$dbh->query($query)->execute();


$query = '
REPLACE INTO ' . $db_new . '.extension_translation
(id, translatable_id,name, headline_liste, body_summary,body,active, locale, slug, minigroup_name)
(SELECT 
n.nid, 
n.nid, 
n.title as name,
fdflh.field_list_headline_value as headline_liste,
fdb.body_summary AS body_summary, 
fdb.body_value as body,
n.status as active,
"fr" as locale,
REPLACE(ua.alias, "extension\/", "") AS slug,
fdfmn.field_minigroup_name_value as minigroup_name
FROM  ' . $db_old . '.node n
LEFT JOIN  ' . $db_old . '.field_data_field_list_headline fdflh ON fdflh.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_body fdb ON fdb.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_field_texte_under_le_prix fdftulp ON fdftulp.entity_id = n.nid
LEFT JOIN ' . $db_old . '.url_alias ua ON ua.source = CONCAT("node/",n.nid) 
LEFT JOIN  ' . $db_old . '.field_data_field_minigroup_name fdfmn ON fdfmn.entity_id = n.nid
WHERE n.type = "escapade"
)
';
$dbh->query($query)->execute();

// EXTENSION TO FILES
$query = '
REPLACE INTO ' . $db_new . '.extension_to_files
(extension_id,files_id)
(SELECT n.nid, fp.field_photos_fid
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_photos fp ON fp.entity_id = n.nid
WHERE n.type = "escapade" AND fp.field_photos_fid IS NOT NULL
)
';
$dbh->query($query)->execute();

// EXTENSION TO DOMAIN
$query = '
REPLACE INTO ' . $db_new . '.extension_to_domain
(extension_id,domain_id)
(SELECT n.nid, fdfd.field_domaine_tid
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_domaine fdfd ON fdfd.entity_id = n.nid
WHERE n.type = "escapade")
';
$dbh->query($query)->execute();

// EXTENSION TO DESTINATION
$query = '
REPLACE INTO ' . $db_new . '.extension_to_extension_destination
(extension,destination, position)
(SELECT n.nid, fdfld.field_escapade_vers_destination_target_id, fdfld.delta as position
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_escapade_vers_destination fdfld ON fdfld.entity_id = n.nid
LEFT JOIN ' . $db_old . '.node n2 ON fdfld.field_escapade_vers_destination_target_id = n2.nid
WHERE n.type = "escapade" AND fdfld.field_escapade_vers_destination_target_id IS NOT NULL AND n2.nid IS NOT NULL )
';
$dbh->query($query)->execute();


// EXTENSION TO DAY
$query = '
REPLACE INTO ' . $db_new . '.extension_to_day
(extension,day, position)
(SELECT n.nid, fdfld.field_link_dayblocks_target_id as day, fdfld.delta as position
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_link_dayblocks fdfld ON fdfld.entity_id = n.nid
LEFT JOIN ' . $db_old . '.node n2 ON fdfld.field_link_dayblocks_target_id = n2.nid
WHERE n.type = "escapade" AND fdfld.field_link_dayblocks_target_id IS NOT NULL AND n2.nid IS NOT NULL )
';
$dbh->query($query)->execute();

// EXTENSION TO CONTENT To DESTINATION
$query = '
REPLACE INTO ' . $db_new . '.extension_content_to_destination
(extension_id,destination_id)
(SELECT n.nid, fdfld.field_link_destinations_target_id
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_link_destinations fdfld ON fdfld.entity_id = n.nid
LEFT JOIN ' . $db_old . '.node n2 ON fdfld.field_link_destinations_target_id = n2.nid
WHERE n.type = "escapade" AND fdfld.field_link_destinations_target_id IS NOT NULL AND n2.nid IS NOT NULL )
';
$dbh->query($query)->execute();

// EXTENSION TO RECREATION
$query = '
REPLACE INTO ' . $db_new . '.extension_to_recreation
(extension_id,recreation_id)
(SELECT n.nid, fdfld.field_escapade_type_target_id
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_escapade_type fdfld ON fdfld.entity_id = n.nid
LEFT JOIN ' . $db_old . '.node n2 ON fdfld.field_escapade_type_target_id = n2.nid
WHERE n.type = "escapade" AND fdfld.field_escapade_type_target_id IS NOT NULL AND n2.nid IS NOT NULL )
';
$dbh->query($query)->execute();

// EXTENSION TO SEASON
$query = '
REPLACE INTO ' . $db_new . '.extension_to_season
(extension_id,season_id)
(SELECT n.nid, fdfld.field_saison_tid
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_saison fdfld ON fdfld.entity_id = n.nid
LEFT JOIN ' . $db_old . '.node n2 ON fdfld.field_saison_tid = n2.nid
WHERE n.type = "escapade" AND fdfld.field_saison_tid IS NOT NULL)
';
$dbh->query($query)->execute();



/**
 * MINIGROUPS
 */


$query = '(SELECT
f.field_minigroup_dates_tariff_value AS id,
f.entity_id AS orig_id,
fdfmsd.field_minigroup_start_date_value AS start,
fdfmed.field_minigroup_end_date_value AS end,
fdfmapie.field_minigroup_array_prix_in_eu_value AS prix_eur,
fdfmapir.field_minigroup_array_prix_in_ru_value AS prix_rub
FROM ' . $db_old . '.field_data_field_minigroup_dates_tariff f
LEFT JOIN ' . $db_old . '.field_data_field_minigroup_start_date fdfmsd ON fdfmsd.entity_id = f.field_minigroup_dates_tariff_value
LEFT JOIN ' . $db_old . '.field_data_field_minigroup_end_date fdfmed ON fdfmed.entity_id = f.field_minigroup_dates_tariff_value
LEFT JOIN ' . $db_old . '.field_data_field_minigroup_array_prix_in_eu fdfmapie ON fdfmapie.entity_id = f.field_minigroup_dates_tariff_value
LEFT JOIN ' . $db_old . '.field_data_field_minigroup_array_prix_in_ru fdfmapir ON fdfmapir.entity_id = f.field_minigroup_dates_tariff_value
WHERE f.bundle = "escapade"
)';
$services = [];


foreach ($dbh->query($query, PDO::FETCH_ASSOC) as $row) {
  $start = explode(" ", $row['start']);
  $start[0] = explode("-", $start[0]);
  $start[1] = str_replace(":", "", $start[1]);
  $start = $start[0][0] . $start[0][1] . $start[0][2] . $start[1];
  $start = substr($start, 0, -2);
  $end = explode(" ", $row['end']);
  $end[0] = explode("-", $end[0]);
  $end[1] = str_replace(":", "", $end[1]);
  $end = $end[0][0] . $end[0][1] . $end[0][2] . $end[1];
  $end = substr($end, 0, -2);
  if (!$row['prix_eur']) {
    $row['prix_eur'] = 'null';
  }
  if (!$row['prix_rub']) {
    $row['prix_rub'] = 'null';
  }
  $q2 = 'REPLACE INTO ' . $db_new . '.minigroup  (id,start,`end`,prix_eur,prix_rub) VALUES (' . $row['id'] . ',' . $start . ',' . $end . ',' . $row['prix_eur'] . ',' . $row['prix_rub'] . ') ';

  $dbh->query($q2)->execute();
}

foreach ($dbh->query($query, PDO::FETCH_ASSOC) as $row) {
  $q1 = 'REPLACE INTO ' . $db_new . '.extension_to_minigroup  (extension_id,minigroup_id) VALUES (' . $row['orig_id'] . ',' . $row['id'] . ') ';
  $dbh->query($q1)->execute();
}

echo "\n";
print_r('DONE');
echo "\n";
die();

