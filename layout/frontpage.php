<?php

defined('MOODLE_INTERNAL') || die();

user_preference_allow_ajax_update('drawer-open-nav', PARAM_ALPHA);
require_once($CFG->libdir . '/behat/lib.php');

if (isloggedin()) {
    $navdraweropen = (get_user_preferences('drawer-open-nav', 'true') == 'true');
} else {
    $navdraweropen = false;
}
$extraclasses = [];
if ($navdraweropen) {
    $extraclasses[] = 'drawer-open-left';
}
$bodyattributes = $OUTPUT->body_attributes($extraclasses);
$blockshtml = $OUTPUT->blocks('side-pre');
$hasblocks = strpos($blockshtml, 'data-block=') !== false;
$regionmainsettingsmenu = $OUTPUT->region_main_settings_menu();

// Feeds de Noticias
$feed = file_get_contents(get_config('theme_moodleidg', 'rss'));

try {
    $xml = new SimpleXmlElement($feed);
    $news['link'] = (string)$xml->channel->link;
    foreach ($xml->channel->item as $value) {
        $news['item'][] = (array)$value;
    }
    $news['featured'] = $news['item'][0];
    $news['item'] = array_slice($news['item'], 1, 3);

} catch (Exception $e) {
    $xml = false;
    $news = null;
}

//slide
$slides['numerosslides'] = get_config('theme_moodleidg', 'numberofslides');

for ($i = 1; $i <= $slides['numerosslides']; $i++) {
    $slides['images'][] = [
        'id' => $i-1,
        'info' => get_config('theme_moodleidg',  'slide'.$i.'info'),
        'titulo' => get_config('theme_moodleidg',  'slide'.$i),
        'img'=> $OUTPUT->pix_url(get_config('theme_moodleidg','slide'.$i.'image')),
        //'img' => $OUTPUT->pix_url('Bannerteste/banner_teste-01', 'theme_moodleidg'),
        'url' => get_config('theme_moodleidg',  'slide'.$i.'url'),
        'caption' => get_config('theme_moodleidg', 'slide'.$i.'caption'),
        'active' => $i==1?'active':''
    ];
}
//fimslide

// .fim do Feeds de Noticias

$templatecontext = [
    'fullname' => format_string($SITE->fullname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'shortname' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'bodyattributes' => $bodyattributes,
    'navdraweropen' => $navdraweropen,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'organization' => get_config('theme_moodleidg', 'organization'),
    'subordination' => get_config('theme_moodleidg', 'subordination'),
    'address' => get_config('theme_moodleidg', 'address'),
    'news' => $news,
    'slides' => $slides,
];

$templatecontext['flatnavigation'] = $PAGE->flatnav;
$PAGE->requires->jquery();
// $PAGE->requires->js('/theme/moodleidg/javascript/jquery.cookie.js'); // precisa de atualização
// $PAGE->requires->js('/theme/moodleidg/javascript/template.js'); // precisa de atualização
$PAGE->requires->js('/theme/moodleidg/javascript/moodleidg.js');

echo $OUTPUT->render_from_template('theme_moodleidg/frontpage', $templatecontext);