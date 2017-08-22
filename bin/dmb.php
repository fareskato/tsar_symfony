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
 * FILES
 */
$query = '
REPLACE INTO ' . $db_new . '.files (id,user_id,file_name,url,mime,active,type)  
(SELECT f.fid, 1 AS user_id, f.filename, REPLACE(f.uri, "public\:\/\/", "/"), f.filemime, 1 AS active, f.type FROM ' . $db_old . '.file_managed f)
';
$dbh->query($query)->execute();

$query = '
REPLACE INTO ' . $db_new . '.files_translation 
(id,translatable_id,locale,NAME,description)  
(SELECT f.fid, f.fid, "fr" AS locale, f.filename, "" AS  description FROM ' . $db_old . '.file_managed f)
';
$dbh->query($query)->execute();

/**
 * SLIDER
 */
$query = '
REPLACE INTO ' . $db_new . '.front_slider
(
id,
image,
color,
position,
reorder,
external)
(SELECT 
n.nid, 
fdfpf.field_push_photo_fid, 
fdfmc.field_mobile_color_value, 
	CASE WHEN fdfpeh.field_position_en_home_value = "mobile" THEN "mobile"
	ELSE "desktop"
	END as position,
	n.nid as reorder,
	CASE WHEN ua.alias IS NULL THEN 1
	ELSE 0
	END as external
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_push_title_line_1 l1 ON l1.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_push_title_line_2 l2 ON l2.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_body fdb ON fdb.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_mobile_color fdfmc ON fdfmc.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_position_en_home fdfpeh ON fdfpeh.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_push_link fdfpl ON fdfpl.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_push_photo fdfpf ON fdfpf.entity_id = n.nid
LEFT JOIN ' . $db_old . '.url_alias ua ON ua.source = fdfpl.field_push_link_url
WHERE n.type = "push_home_page")
';
$dbh->query($query)->execute();

$query = '
REPLACE INTO ' . $db_new . '.front_slider_translation
(
id,
translatable_id,
name1,
name2,
body,
slug,
active,
locale)
(SELECT 
n.nid,
n.nid,
l1.field_push_title_line_1_value, 
l2.field_push_title_line_2_value, 
fdb.body_value,  
	CASE WHEN ua.alias IS NULL THEN fdfpl.field_push_link_url
	ELSE ua.alias
	END as slug,
n.status AS active, "fr" as locale
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_push_title_line_1 l1 ON l1.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_push_title_line_2 l2 ON l2.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_body fdb ON fdb.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_mobile_color fdfmc ON fdfmc.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_position_en_home fdfpeh ON fdfpeh.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_push_link fdfpl ON fdfpl.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_push_photo fdfpf ON fdfpf.entity_id = n.nid
LEFT JOIN ' . $db_old . '.url_alias ua ON ua.source = fdfpl.field_push_link_url
WHERE n.type = "push_home_page")
';
$dbh->query($query)->execute();

// SLIDER TO DOMAIN
$query = '
REPLACE INTO ' . $db_new . '.front_slider_to_domain
(frontslider_id,domain_id)
(SELECT n.nid, fdfd.field_domaine_tid
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_domaine fdfd ON fdfd.entity_id = n.nid
WHERE n.type = "push_home_page")
';
$dbh->query($query)->execute();

/**
 * PARTNER
 */
$query = '
REPLACE INTO ' . $db_new . '.partners
(id,image,link,reorder)
(SELECT 
n.nid, 
fdfpl.field_partner_logo_fid AS image, 
fdfpu.field_partner_url_url AS link, n.nid as reorder
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_body fdb ON fdb.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_partner_url fdfpu ON fdfpu.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_partner_logo fdfpl ON fdfpl.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_partner_priority fdfpp ON fdfpp.entity_id = n.nid
LEFT JOIN ' . $db_old . '.url_alias ua ON ua.source = CONCAT("node/",n.nid) 
WHERE n.type = "partners")
';
$dbh->query($query)->execute();

$query = '
REPLACE INTO ' . $db_new . '.partners_translation
(id,translatable_id,name,slug,active,keywords,locale)
(SELECT 
n.nid, n.nid, n.title, ua.alias AS slug, n.status as active,
"" AS keywords, "fr" as locale
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_body fdb ON fdb.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_partner_url fdfpu ON fdfpu.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_partner_logo fdfpl ON fdfpl.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_partner_priority fdfpp ON fdfpp.entity_id = n.nid
LEFT JOIN ' . $db_old . '.url_alias ua ON ua.source = CONCAT("node/",n.nid) 
WHERE n.type = "partners")
';

$dbh->query($query)->execute();


/**
 * ARTICLE
 */
//KEEP BODIES IN ARTICLE
/*$articleBody = array();
$query = 'SELECT id,body FROM '.$db_new.'.article_translation ';
foreach ($dbh->query($query, PDO::FETCH_ASSOC) as $row) {
	$articleBody[$row['id']] = $row['body'];
}
*/

/*$query = '
REPLACE INTO '.$db_new.'.article
(id,user_id,image,created,changed)
(
SELECT n.nid, 1 AS uid, fdfbp.field_background_photo_fid, n.created, n.changed
FROM '.$db_old.'.node n
LEFT JOIN '.$db_old.'.field_data_field_background_photo fdfbp ON fdfbp.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_data_body fdb ON fdb.entity_id = n.nid
LEFT JOIN '.$db_old.'.url_alias ua ON ua.source = CONCAT("node/",n.nid)
WHERE n.type = "static_page"
)
';
$dbh->query($query)->execute();

$query = '
REPLACE INTO '.$db_new.'.article_translation
(id,translatable_id,name,slug,body_summary,body,active,keywords,locale)
(
SELECT n.nid, n.nid, n.title, ua.alias as  slug, fdb.body_summary AS summary, fdb.body_value, n.status AS active, "" AS keywords, "fr" as locale
FROM '.$db_old.'.node n
LEFT JOIN '.$db_old.'.field_data_field_background_photo fdfbp ON fdfbp.entity_id = n.nid
LEFT JOIN '.$db_old.'.field_data_body fdb ON fdb.entity_id = n.nid
LEFT JOIN '.$db_old.'.url_alias ua ON ua.source = CONCAT("node/",n.nid)
WHERE n.type = "static_page"
)
';
$dbh->query($query)->execute();*/
/*
foreach($articleBody as $id => $article) {
	$query = 'UPDATE '.$db_new.'.article_translation SET body = "'.addslashes($article).'" WHERE translatable_id = '.$id.' ';
	$dbh->query($query)->execute();
}*/
/*
$query = '
REPLACE INTO '.$db_new.'.article_to_domain
(article_id,domain_id)
(
SELECT n.nid, fdfd.field_domaine_tid
FROM '.$db_old.'.node n
LEFT JOIN '.$db_old.'.field_data_field_domaine fdfd ON fdfd.entity_id = n.nid
WHERE n.type = "static_page" AND fdfd.field_domaine_tid IS NOT NULL
)
';
$dbh->query($query)->execute();
*/


//USERS
$query = '
REPLACE INTO ' . $db_new . '.user
(id,username, username_canonical, email, email_canonical, enabled, salt, password, last_login, confirmation_token, password_requested_at, roles, active)
(
SELECT uid as id, name as username, name as username_canonical,mail as email, mail as email_canonical, "1" as enabled, null as salt,
"$2y$13$4jXdJdwhh5mDGMh0NPE4CuCJBXUnOfc7HX5eXHGqdjqa6zoxt82mS" as password, null as last_login, null as confirmation_token, null as password_requested_at,
"a:1:{i:0;s:12:\"ROLE_MANAGER\";}" as roles, "1" as active
FROM ' . $db_old . '.users
)
';
$dbh->query($query)->execute();
$cmd = "php bin/console fos:user:promote admin-altima --super";
shell_exec($cmd);


/**
 * LOCATION
 */
$query = '
REPLACE INTO ' . $db_new . '.location
(id,postal_code,country,latitude,longitude)
(
SELECT lid,postal_code,country,latitude,longitude
FROM ' . $db_old . '.location
)
';
$dbh->query($query)->execute();

$query = '
REPLACE INTO ' . $db_new . '.location_translation
(id,translatable_id,locale, name,street,additional,city,province)
(
SELECT lid,lid,"fr" as locale,name,street,additional,city,province
FROM ' . $db_old . '.location
)
';
$dbh->query($query)->execute();

/**
 * DESTINATION
 */

$query = '
REPLACE INTO ' . $db_new . '.destination
(id,type_destination_id,master_destination,present_in_list,location,
image_background,image,image_panorama,image_header,etiquette)
(

SELECT n.nid, fdftdd.field_type_de_destination_target_id, fdfmd.field_master_destination_value,
fdfsil.field_selected_in_list_value, fdfl.field_location_lid,
fdfbf.field_background_photo_fid as image_background,
fdfcf.field_central_photo_fid as image,
fdflf.field_long_photo_fid as image_panorama,
fdfrf.field_rounded_photo_fid as image_header,
fdfe.field_etiquette_tid as etiquette
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_type_de_destination fdftdd ON fdftdd.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_master_destination fdfmd ON fdfmd.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_selected_in_list fdfsil ON fdfsil.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_body fdb ON fdb.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_location fdfl ON fdfl.entity_id = n.nid
LEFT JOIN ' . $db_old . '.url_alias ua ON ua.source = CONCAT("node/",n.nid) 
LEFT JOIN ' . $db_old . '.field_data_field_etiquette fdfe ON fdfe.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_background_photo fdfbf ON fdfbf.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_central_photo fdfcf ON fdfcf.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_rounded_photo fdfrf ON fdfrf.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_long_photo fdflf ON fdflf.entity_id = n.nid
WHERE n.type = "destination"
)
';
$dbh->query($query)->execute();

$query = '
REPLACE INTO ' . $db_new . '.destination_translation
(id,translatable_id,locale, body, name,slug,body_summary,keywords)
(

SELECT 
n.nid, n.nid, "fr" as locale, fdb.body_value, n.title, 
REPLACE(ua.alias, "destination\/", "") AS slug,
fdb.body_summary, "" as keywords
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_type_de_destination fdftdd ON fdftdd.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_master_destination fdfmd ON fdfmd.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_selected_in_list fdfsil ON fdfsil.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_body fdb ON fdb.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_location fdfl ON fdfl.entity_id = n.nid
LEFT JOIN ' . $db_old . '.url_alias ua ON ua.source = CONCAT("node/",n.nid) 
LEFT JOIN ' . $db_old . '.field_data_field_etiquette fdfe ON fdfe.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_background_photo fdfbf ON fdfbf.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_central_photo fdfcf ON fdfcf.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_rounded_photo fdfrf ON fdfrf.entity_id = n.nid
LEFT JOIN ' . $db_old . '.field_data_field_long_photo fdflf ON fdflf.entity_id = n.nid
WHERE n.type = "destination"
)
';
$dbh->query($query)->execute();

// DESTINATION TO DOMAIN
$query = '
REPLACE INTO ' . $db_new . '.destination_to_domain
(destination_id,domain_id)
(SELECT n.nid, fdfd.field_domaine_tid
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_domaine fdfd ON fdfd.entity_id = n.nid
WHERE n.type = "destination")
';
$dbh->query($query)->execute();

// DESTINATION TO FILES
$query = '
REPLACE INTO ' . $db_new . '.destination_to_files
(destination_id,files_id)
(SELECT n.nid, fp.field_photos_fid
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_photos fp ON fp.entity_id = n.nid
WHERE n.type = "destination" AND fp.field_photos_fid IS NOT NULL
)
';
$dbh->query($query)->execute();


// DESTINATION TO PARENT
$query = '
REPLACE INTO ' . $db_new . '.destination_to_parent
(destination_id,destination_parent_id)
(SELECT n.nid, fdfld.field_link_destinations_target_id
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_link_destinations fdfld ON fdfld.entity_id = n.nid
WHERE n.type = "destination" AND fdfld.field_link_destinations_target_id IS NOT NULL
)
';
$dbh->query($query)->execute();

// VISIT MAIN
$query = '
REPLACE INTO ' . $db_new . '.visit
(id, image, image_background,image_miniature,location, mini_groupe, minigroup_prix_euros, minigroup_prix_rubles, product_365, 
 number_hours_visit, tariffed_product, prix_euro, prix_rouble, visit_duration, ville)
(SELECT n.nid,
fdfcf.field_central_photo_fid as image,
fdfbp.field_background_photo_fid as image_background,
fdflf.field_list_photo_fid as image_miniature,
fdfl.field_location_lid as location,
fdfmg.field_is_minigroup_value as mini_groupe,
fdfeu.field_minigroup_prix_in_euros_value as minigroup_prix_euros,
fdfru.field_minigroup_prix_in_rubles_value as minigroup_prix_rubles,
fdflp.field_linked_product_value as product_365,
fdfnhv.field_number_hour_visite_value as number_hours_visit,
fdffhp.field_has_price_value as tariffed_product,
fdfpve.field_price_value_euro_value as prix_euro,
fdfpvr.field_price_value_ruble_value as prix_rouble,
fdfvd.field_visite_duration_tid as visit_duration,
fdfedd.field_escapade_dep_destinatio_target_id as ville
FROM  ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_central_photo fdfcf ON fdfcf.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_field_background_photo fdfbp  ON fdfbp.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_field_list_photo fdflf ON fdflf.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_field_location fdfl ON fdfl.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_field_is_minigroup fdfmg ON fdfmg.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_field_minigroup_prix_in_euros fdfeu ON fdfeu.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_field_minigroup_prix_in_rubles fdfru ON fdfru.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_field_linked_product fdflp ON fdflp.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_field_displayed_product_price fdfdpp ON fdfdpp.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_field_number_hour_visite fdfnhv ON fdfnhv.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_field_has_price fdffhp ON fdffhp.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_field_price_value_euro fdfpve ON fdfpve.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_field_price_value_ruble fdfpvr ON fdfpvr.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_field_visite_duration fdfvd ON fdfvd.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_field_escapade_dep_destinatio fdfedd ON fdfedd.entity_id = n.nid
WHERE n.type = "visite")
';
$dbh->query($query)->execute();

// VISIT TRANSLATE
$query = '
REPLACE INTO ' . $db_new . '.visit_translation
(id, translatable_id,name, headline_liste, slug, body_summary,body,active, locale)
(SELECT 
n.nid, 
n.nid, 
n.title as name,
fdflh.field_list_headline_value as name,
REPLACE(ua.alias, "visite\/", "") AS slug,
fdb.body_summary AS body_summary, 
fdb.body_value as body,
n.status as active,
"fr" as locale
FROM  ' . $db_old . '.node n
LEFT JOIN  ' . $db_old . '.field_data_field_list_headline fdflh ON fdflh.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_body fdb ON fdb.entity_id = n.nid
LEFT JOIN ' . $db_old . '.url_alias ua ON ua.source = CONCAT("node/",n.nid) 
WHERE n.type = "visite")
';
$dbh->query($query)->execute();

// VISIT TO FILES
$query = '
REPLACE INTO ' . $db_new . '.visit_to_files
(visit_id,files_id)
(SELECT n.nid, fp.field_photos_fid
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_photos fp ON fp.entity_id = n.nid
WHERE n.type = "visite" AND fp.field_photos_fid IS NOT NULL
)
';
$dbh->query($query)->execute();

// VISIT TO DOMAINS
$query = '
REPLACE INTO ' . $db_new . '.visit_to_domain
(visit_id,domain_id)
(SELECT n.nid, fdfd.field_domaine_tid
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_domaine fdfd ON fdfd.entity_id = n.nid
WHERE n.type = "visite")
';
$dbh->query($query)->execute();

// VISIT TO RECREATION
$query = '
REPLACE INTO ' . $db_new . '.visit_to_recreation
(visit_id,recreation_id)
(SELECT n.nid, fdfld.field_type_d_escapade_tid
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_type_d_escapade fdfld ON fdfld.entity_id = n.nid
LEFT JOIN ' . $db_old . '.node n2 ON fdfld.field_type_d_escapade_tid = n2.nid
WHERE n.type = "visite" AND fdfld.field_type_d_escapade_tid IS NOT NULL AND n2.nid IS NOT NULL )
';
$dbh->query($query)->execute();

// VISIT TO SEASSON
$query = '
REPLACE INTO ' . $db_new . '.visit_to_season
(visit_id,season_id)
(SELECT n.nid, fdfld.field_saison_tid
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_saison fdfld ON fdfld.entity_id = n.nid
LEFT JOIN ' . $db_old . '.node n2 ON fdfld.field_saison_tid = n2.nid
WHERE n.type = "visite" AND fdfld.field_saison_tid IS NOT NULL AND n2.nid IS NOT NULL )
';
$dbh->query($query)->execute();

// VISIT TO TRAVEL POINTS
$query = '
REPLACE INTO ' . $db_new . '.visit_to_travel_points
(visit_id,destination_id)
(SELECT n.nid, fdfld.field_link_destinations_target_id
FROM ' . $db_old . '.node n
LEFT JOIN ' . $db_old . '.field_data_field_link_destinations fdfld ON fdfld.entity_id = n.nid
LEFT JOIN ' . $db_old . '.node n2 ON fdfld.field_link_destinations_target_id = n2.nid
WHERE n.type = "visite" AND fdfld.field_link_destinations_target_id IS NOT NULL AND n2.nid IS NOT NULL )
';
$dbh->query($query)->execute();


// EVENT
$query = "DELETE FROM '.$db_new.'.event_to_related_product";
$stmt = $dbh->prepare($query);
$stmt->execute();
//$dbh->query($query)->execute();
$query = "DELETE FROM '.$db_new.'.event";
$stmt = $dbh->prepare($query);
$stmt->execute();
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


$query = '
REPLACE INTO ' . $db_new . '.event
(id, image, image_background,image_miniature,location, mini_groupe, minigroup_prix_euros, minigroup_prix_rubles, email, event_type)
(SELECT n.nid,
fdfcf.field_central_photo_fid as image,
fdfbp.field_background_photo_fid as image_background,
fdflf.field_list_photo_fid as image_miniature,
fdfl.field_location_lid as location,
fdfmg.field_is_minigroup_value as mini_groupe,
fdfeu.field_minigroup_prix_in_euros_value as minigroup_prix_euros,
fdfru.field_minigroup_prix_in_rubles_value as minigroup_prix_rubles,
fdfee.field_event_email_value as email,
fdfet.field_event_type_target_id as event_type
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
WHERE n.type = "event")
';
//print_r($query); exit;
//$dbh->query($query)->execute();

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
$array = [];
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
(id, translatable_id,name, headline_liste, body_summary,body,active, locale, slug, introduction, show_times)
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
fdfdh.field_displayed_hours_value as show_times
FROM  ' . $db_old . '.node n
LEFT JOIN  ' . $db_old . '.field_data_field_list_headline fdflh ON fdflh.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.field_data_body fdb ON fdb.entity_id = n.nid
LEFT JOIN  ' . $db_old . '.url_alias ua ON ua.source = CONCAT("node/",n.nid)
LEFT JOIN  ' . $db_old . '.field_data_field_introduction fdfi ON fdfi.entity_id = n.nid
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


/**
 * MINIGROUPS
 */

//VISIT MG

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
WHERE f.bundle = "visite"
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
  $q1 = 'REPLACE INTO ' . $db_new . '.visit_to_minigroup  (visit_id,minigroup_id) VALUES (' . $row['orig_id'] . ',' . $row['id'] . ') ';

  $dbh->query($q1)->execute();
}

//EVENT MG

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
WHERE f.bundle = "event"
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
  $q1 = 'REPLACE INTO ' . $db_new . '.event_to_minigroup  (event_id,minigroup_id) VALUES (' . $row['orig_id'] . ',' . $row['id'] . ') ';

  $dbh->query($q1)->execute();
}



echo "\n";
print_r('DONE');
echo "\n";
die();

