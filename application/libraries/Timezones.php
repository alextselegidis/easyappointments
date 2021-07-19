<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.4.0
 * ---------------------------------------------------------------------------- */

/**
 * Timezones
 */
class Timezones {
    /**
     * @var EA_Controller
     */
    protected $CI;

    /**
     * @var string
     */
    protected $default = 'UTC';

    /**
     * @var array
     */
    protected $timezones = [
        'UTC' => [
            'UTC' => 'UTC'
        ],
        'America' => [
            'America/Adak' => 'Adak (-10:00)',
            'America/Atka' => 'Atka (-10:00)',
            'America/Anchorage' => 'Anchorage (-9:00)',
            'America/Juneau' => 'Juneau (-9:00)',
            'America/Nome' => 'Nome (-9:00)',
            'America/Yakutat' => 'Yakutat (-9:00)',
            'America/Dawson' => 'Dawson (-8:00)',
            'America/Ensenada' => 'Ensenada (-8:00)',
            'America/Los_Angeles' => 'Los_Angeles (-8:00)',
            'America/Tijuana' => 'Tijuana (-8:00)',
            'America/Vancouver' => 'Vancouver (-8:00)',
            'America/Whitehorse' => 'Whitehorse (-8:00)',
            'America/Boise' => 'Boise (-7:00)',
            'America/Cambridge_Bay' => 'Cambridge_Bay (-7:00)',
            'America/Chihuahua' => 'Chihuahua (-7:00)',
            'America/Dawson_Creek' => 'Dawson_Creek (-7:00)',
            'America/Denver' => 'Denver (-7:00)',
            'America/Edmonton' => 'Edmonton (-7:00)',
            'America/Hermosillo' => 'Hermosillo (-7:00)',
            'America/Inuvik' => 'Inuvik (-7:00)',
            'America/Mazatlan' => 'Mazatlan (-7:00)',
            'America/Phoenix' => 'Phoenix (-7:00)',
            'America/Shiprock' => 'Shiprock (-7:00)',
            'America/Yellowknife' => 'Yellowknife (-7:00)',
            'America/Belize' => 'Belize (-6:00)',
            'America/Cancun' => 'Cancun (-6:00)',
            'America/Chicago' => 'Chicago (-6:00)',
            'America/Costa_Rica' => 'Costa_Rica (-6:00)',
            'America/El_Salvador' => 'El_Salvador (-6:00)',
            'America/Guatemala' => 'Guatemala (-6:00)',
            'America/Knox_IN' => 'Knox_IN (-6:00)',
            'America/Managua' => 'Managua (-6:00)',
            'America/Menominee' => 'Menominee (-6:00)',
            'America/Merida' => 'Merida (-6:00)',
            'America/Mexico_City' => 'Mexico_City (-6:00)',
            'America/Monterrey' => 'Monterrey (-6:00)',
            'America/Rainy_River' => 'Rainy_River (-6:00)',
            'America/Rankin_Inlet' => 'Rankin_Inlet (-6:00)',
            'America/Regina' => 'Regina (-6:00)',
            'America/Swift_Current' => 'Swift_Current (-6:00)',
            'America/Tegucigalpa' => 'Tegucigalpa (-6:00)',
            'America/Winnipeg' => 'Winnipeg (-6:00)',
            'America/Atikokan' => 'Atikokan (-5:00)',
            'America/Bogota' => 'Bogota (-5:00)',
            'America/Cayman' => 'Cayman (-5:00)',
            'America/Coral_Harbour' => 'Coral_Harbour (-5:00)',
            'America/Detroit' => 'Detroit (-5:00)',
            'America/Fort_Wayne' => 'Fort_Wayne (-5:00)',
            'America/Grand_Turk' => 'Grand_Turk (-5:00)',
            'America/Guayaquil' => 'Guayaquil (-5:00)',
            'America/Havana' => 'Havana (-5:00)',
            'America/Indianapolis' => 'Indianapolis (-5:00)',
            'America/Iqaluit' => 'Iqaluit (-5:00)',
            'America/Jamaica' => 'Jamaica (-5:00)',
            'America/Lima' => 'Lima (-5:00)',
            'America/Louisville' => 'Louisville (-5:00)',
            'America/Montreal' => 'Montreal (-5:00)',
            'America/Nassau' => 'Nassau (-5:00)',
            'America/New_York' => 'New_York (-5:00)',
            'America/Nipigon' => 'Nipigon (-5:00)',
            'America/Panama' => 'Panama (-5:00)',
            'America/Pangnirtung' => 'Pangnirtung (-5:00)',
            'America/Port-au-Prince' => 'Port-au-Prince (-5:00)',
            'America/Resolute' => 'Resolute (-5:00)',
            'America/Thunder_Bay' => 'Thunder_Bay (-5:00)',
            'America/Toronto' => 'Toronto (-5:00)',
            'America/Caracas' => 'Caracas (-4:-30)',
            'America/Anguilla' => 'Anguilla (-4:00)',
            'America/Antigua' => 'Antigua (-4:00)',
            'America/Aruba' => 'Aruba (-4:00)',
            'America/Asuncion' => 'Asuncion (-4:00)',
            'America/Barbados' => 'Barbados (-4:00)',
            'America/Blanc-Sablon' => 'Blanc-Sablon (-4:00)',
            'America/Boa_Vista' => 'Boa_Vista (-4:00)',
            'America/Campo_Grande' => 'Campo_Grande (-4:00)',
            'America/Cuiaba' => 'Cuiaba (-4:00)',
            'America/Curacao' => 'Curacao (-4:00)',
            'America/Dominica' => 'Dominica (-4:00)',
            'America/Eirunepe' => 'Eirunepe (-4:00)',
            'America/Glace_Bay' => 'Glace_Bay (-4:00)',
            'America/Goose_Bay' => 'Goose_Bay (-4:00)',
            'America/Grenada' => 'Grenada (-4:00)',
            'America/Guadeloupe' => 'Guadeloupe (-4:00)',
            'America/Guyana' => 'Guyana (-4:00)',
            'America/Halifax' => 'Halifax (-4:00)',
            'America/La_Paz' => 'La_Paz (-4:00)',
            'America/Manaus' => 'Manaus (-4:00)',
            'America/Marigot' => 'Marigot (-4:00)',
            'America/Martinique' => 'Martinique (-4:00)',
            'America/Moncton' => 'Moncton (-4:00)',
            'America/Montserrat' => 'Montserrat (-4:00)',
            'America/Port_of_Spain' => 'Port_of_Spain (-4:00)',
            'America/Porto_Acre' => 'Porto_Acre (-4:00)',
            'America/Porto_Velho' => 'Porto_Velho (-4:00)',
            'America/Puerto_Rico' => 'Puerto_Rico (-4:00)',
            'America/Rio_Branco' => 'Rio_Branco (-4:00)',
            'America/Santiago' => 'Santiago (-4:00)',
            'America/Santo_Domingo' => 'Santo_Domingo (-4:00)',
            'America/St_Barthelemy' => 'St_Barthelemy (-4:00)',
            'America/St_Kitts' => 'St_Kitts (-4:00)',
            'America/St_Lucia' => 'St_Lucia (-4:00)',
            'America/St_Thomas' => 'St_Thomas (-4:00)',
            'America/St_Vincent' => 'St_Vincent (-4:00)',
            'America/Thule' => 'Thule (-4:00)',
            'America/Tortola' => 'Tortola (-4:00)',
            'America/Virgin' => 'Virgin (-4:00)',
            'America/St_Johns' => 'St_Johns (-3:-30)',
            'America/Araguaina' => 'Araguaina (-3:00)',
            'America/Bahia' => 'Bahia (-3:00)',
            'America/Belem' => 'Belem (-3:00)',
            'America/Buenos_Aires' => 'Buenos_Aires (-3:00)',
            'America/Catamarca' => 'Catamarca (-3:00)',
            'America/Cayenne' => 'Cayenne (-3:00)',
            'America/Cordoba' => 'Cordoba (-3:00)',
            'America/Fortaleza' => 'Fortaleza (-3:00)',
            'America/Godthab' => 'Godthab (-3:00)',
            'America/Jujuy' => 'Jujuy (-3:00)',
            'America/Maceio' => 'Maceio (-3:00)',
            'America/Mendoza' => 'Mendoza (-3:00)',
            'America/Miquelon' => 'Miquelon (-3:00)',
            'America/Montevideo' => 'Montevideo (-3:00)',
            'America/Paramaribo' => 'Paramaribo (-3:00)',
            'America/Recife' => 'Recife (-3:00)',
            'America/Rosario' => 'Rosario (-3:00)',
            'America/Santarem' => 'Santarem (-3:00)',
            'America/Sao_Paulo' => 'Sao_Paulo (-3:00)',
            'America/Noronha' => 'Noronha (-2:00)',
            'America/Scoresbysund' => 'Scoresbysund (-1:00)',
            'America/Danmarkshavn' => 'Danmarkshavn (+0:00)',
        ],
        'Canada' => [
            'Canada/Pacific' => 'Pacific (-8:00)',
            'Canada/Yukon' => 'Yukon (-8:00)',
            'Canada/Mountain' => 'Mountain (-7:00)',
            'Canada/Central' => 'Central (-6:00)',
            'Canada/East-Saskatchewan' => 'East-Saskatchewan (-6:00)',
            'Canada/Saskatchewan' => 'Saskatchewan (-6:00)',
            'Canada/Eastern' => 'Eastern (-5:00)',
            'Canada/Atlantic' => 'Atlantic (-4:00)',
            'Canada/Newfoundland' => 'Newfoundland (-3:-30)',
        ],
        'Mexico' => [
            'Mexico/BajaNorte' => 'BajaNorte (-8:00)',
            'Mexico/BajaSur' => 'BajaSur (-7:00)',
            'Mexico/General' => 'General (-6:00)',
        ],
        'Chile' => [
            'Chile/EasterIsland' => 'EasterIsland (-6:00)',
            'Chile/Continental' => 'Continental (-4:00)',
        ],
        'Antarctica' => [
            'Antarctica/Palmer' => 'Palmer (-4:00)',
            'Antarctica/Rothera' => 'Rothera (-3:00)',
            'Antarctica/Syowa' => 'Syowa (+3:00)',
            'Antarctica/Mawson' => 'Mawson (+6:00)',
            'Antarctica/Vostok' => 'Vostok (+6:00)',
            'Antarctica/Davis' => 'Davis (+7:00)',
            'Antarctica/Casey' => 'Casey (+8:00)',
            'Antarctica/DumontDUrville' => 'DumontDUrville (+10:00)',
            'Antarctica/McMurdo' => 'McMurdo (+12:00)',
            'Antarctica/South_Pole' => 'South_Pole (+12:00)',
        ],
        'Atlantic' => [
            'Atlantic/Bermuda' => 'Bermuda (-4:00)',
            'Atlantic/Stanley' => 'Stanley (-4:00)',
            'Atlantic/South_Georgia' => 'South_Georgia (-2:00)',
            'Atlantic/Azores' => 'Azores (-1:00)',
            'Atlantic/Cape_Verde' => 'Cape_Verde (-1:00)',
            'Atlantic/Canary' => 'Canary (+0:00)',
            'Atlantic/Faeroe' => 'Faeroe (+0:00)',
            'Atlantic/Faroe' => 'Faroe (+0:00)',
            'Atlantic/Madeira' => 'Madeira (+0:00)',
            'Atlantic/Reykjavik' => 'Reykjavik (+0:00)',
            'Atlantic/St_Helena' => 'St_Helena (+0:00)',
            'Atlantic/Jan_Mayen' => 'Jan_Mayen (+1:00)',
        ],
        'Brazil' => [
            'Brazil/Acre' => 'Acre (-4:00)',
            'Brazil/West' => 'West (-4:00)',
            'Brazil/East' => 'East (-3:00)',
            'Brazil/DeNoronha' => 'DeNoronha (-2:00)',
        ],
        'Africa' => [
            'Africa/Abidjan' => 'Abidjan (+0:00)',
            'Africa/Accra' => 'Accra (+0:00)',
            'Africa/Bamako' => 'Bamako (+0:00)',
            'Africa/Banjul' => 'Banjul (+0:00)',
            'Africa/Bissau' => 'Bissau (+0:00)',
            'Africa/Casablanca' => 'Casablanca (+0:00)',
            'Africa/Conakry' => 'Conakry (+0:00)',
            'Africa/Dakar' => 'Dakar (+0:00)',
            'Africa/El_Aaiun' => 'El_Aaiun (+0:00)',
            'Africa/Freetown' => 'Freetown (+0:00)',
            'Africa/Lome' => 'Lome (+0:00)',
            'Africa/Monrovia' => 'Monrovia (+0:00)',
            'Africa/Nouakchott' => 'Nouakchott (+0:00)',
            'Africa/Ouagadougou' => 'Ouagadougou (+0:00)',
            'Africa/Sao_Tome' => 'Sao_Tome (+0:00)',
            'Africa/Timbuktu' => 'Timbuktu (+0:00)',
            'Africa/Algiers' => 'Algiers (+1:00)',
            'Africa/Bangui' => 'Bangui (+1:00)',
            'Africa/Brazzaville' => 'Brazzaville (+1:00)',
            'Africa/Ceuta' => 'Ceuta (+1:00)',
            'Africa/Douala' => 'Douala (+1:00)',
            'Africa/Kinshasa' => 'Kinshasa (+1:00)',
            'Africa/Lagos' => 'Lagos (+1:00)',
            'Africa/Libreville' => 'Libreville (+1:00)',
            'Africa/Luanda' => 'Luanda (+1:00)',
            'Africa/Malabo' => 'Malabo (+1:00)',
            'Africa/Ndjamena' => 'Ndjamena (+1:00)',
            'Africa/Niamey' => 'Niamey (+1:00)',
            'Africa/Porto-Novo' => 'Porto-Novo (+1:00)',
            'Africa/Tunis' => 'Tunis (+1:00)',
            'Africa/Windhoek' => 'Windhoek (+1:00)',
            'Africa/Blantyre' => 'Blantyre (+2:00)',
            'Africa/Bujumbura' => 'Bujumbura (+2:00)',
            'Africa/Cairo' => 'Cairo (+2:00)',
            'Africa/Gaborone' => 'Gaborone (+2:00)',
            'Africa/Harare' => 'Harare (+2:00)',
            'Africa/Johannesburg' => 'Johannesburg (+2:00)',
            'Africa/Kigali' => 'Kigali (+2:00)',
            'Africa/Lubumbashi' => 'Lubumbashi (+2:00)',
            'Africa/Lusaka' => 'Lusaka (+2:00)',
            'Africa/Maputo' => 'Maputo (+2:00)',
            'Africa/Maseru' => 'Maseru (+2:00)',
            'Africa/Mbabane' => 'Mbabane (+2:00)',
            'Africa/Tripoli' => 'Tripoli (+2:00)',
            'Africa/Addis_Ababa' => 'Addis_Ababa (+3:00)',
            'Africa/Asmara' => 'Asmara (+3:00)',
            'Africa/Asmera' => 'Asmera (+3:00)',
            'Africa/Dar_es_Salaam' => 'Dar_es_Salaam (+3:00)',
            'Africa/Djibouti' => 'Djibouti (+3:00)',
            'Africa/Kampala' => 'Kampala (+3:00)',
            'Africa/Khartoum' => 'Khartoum (+3:00)',
            'Africa/Mogadishu' => 'Mogadishu (+3:00)',
            'Africa/Nairobi' => 'Nairobi (+3:00)',
        ],
        'Europe' => [
            'Europe/Belfast' => 'Belfast (+0:00)',
            'Europe/Dublin' => 'Dublin (+0:00)',
            'Europe/Guernsey' => 'Guernsey (+0:00)',
            'Europe/Isle_of_Man' => 'Isle_of_Man (+0:00)',
            'Europe/Jersey' => 'Jersey (+0:00)',
            'Europe/Lisbon' => 'Lisbon (+0:00)',
            'Europe/London' => 'London (+0:00)',
            'Europe/Amsterdam' => 'Amsterdam (+1:00)',
            'Europe/Andorra' => 'Andorra (+1:00)',
            'Europe/Belgrade' => 'Belgrade (+1:00)',
            'Europe/Berlin' => 'Berlin (+1:00)',
            'Europe/Bratislava' => 'Bratislava (+1:00)',
            'Europe/Brussels' => 'Brussels (+1:00)',
            'Europe/Budapest' => 'Budapest (+1:00)',
            'Europe/Copenhagen' => 'Copenhagen (+1:00)',
            'Europe/Gibraltar' => 'Gibraltar (+1:00)',
            'Europe/Ljubljana' => 'Ljubljana (+1:00)',
            'Europe/Luxembourg' => 'Luxembourg (+1:00)',
            'Europe/Madrid' => 'Madrid (+1:00)',
            'Europe/Malta' => 'Malta (+1:00)',
            'Europe/Monaco' => 'Monaco (+1:00)',
            'Europe/Oslo' => 'Oslo (+1:00)',
            'Europe/Paris' => 'Paris (+1:00)',
            'Europe/Podgorica' => 'Podgorica (+1:00)',
            'Europe/Prague' => 'Prague (+1:00)',
            'Europe/Rome' => 'Rome (+1:00)',
            'Europe/San_Marino' => 'San_Marino (+1:00)',
            'Europe/Sarajevo' => 'Sarajevo (+1:00)',
            'Europe/Skopje' => 'Skopje (+1:00)',
            'Europe/Stockholm' => 'Stockholm (+1:00)',
            'Europe/Tirane' => 'Tirane (+1:00)',
            'Europe/Vaduz' => 'Vaduz (+1:00)',
            'Europe/Vatican' => 'Vatican (+1:00)',
            'Europe/Vienna' => 'Vienna (+1:00)',
            'Europe/Warsaw' => 'Warsaw (+1:00)',
            'Europe/Zagreb' => 'Zagreb (+1:00)',
            'Europe/Zurich' => 'Zurich (+1:00)',
            'Europe/Athens' => 'Athens (+2:00)',
            'Europe/Bucharest' => 'Bucharest (+2:00)',
            'Europe/Chisinau' => 'Chisinau (+2:00)',
            'Europe/Helsinki' => 'Helsinki (+2:00)',
            'Europe/Istanbul' => 'Istanbul (+2:00)',
            'Europe/Kaliningrad' => 'Kaliningrad (+2:00)',
            'Europe/Kiev' => 'Kiev (+2:00)',
            'Europe/Mariehamn' => 'Mariehamn (+2:00)',
            'Europe/Minsk' => 'Minsk (+2:00)',
            'Europe/Nicosia' => 'Nicosia (+2:00)',
            'Europe/Riga' => 'Riga (+2:00)',
            'Europe/Simferopol' => 'Simferopol (+2:00)',
            'Europe/Sofia' => 'Sofia (+2:00)',
            'Europe/Tallinn' => 'Tallinn (+2:00)',
            'Europe/Tiraspol' => 'Tiraspol (+2:00)',
            'Europe/Uzhgorod' => 'Uzhgorod (+2:00)',
            'Europe/Vilnius' => 'Vilnius (+2:00)',
            'Europe/Zaporozhye' => 'Zaporozhye (+2:00)',
            'Europe/Moscow' => 'Moscow (+3:00)',
            'Europe/Volgograd' => 'Volgograd (+3:00)',
            'Europe/Samara' => 'Samara (+4:00)',
        ],
        'Arctic' => [
            'Arctic/Longyearbyen' => 'Longyearbyen (+1:00)',
        ],
        'Asia' => [
            'Asia/Amman' => 'Amman (+2:00)',
            'Asia/Beirut' => 'Beirut (+2:00)',
            'Asia/Damascus' => 'Damascus (+2:00)',
            'Asia/Gaza' => 'Gaza (+2:00)',
            'Asia/Istanbul' => 'Istanbul (+2:00)',
            'Asia/Jerusalem' => 'Jerusalem (+2:00)',
            'Asia/Nicosia' => 'Nicosia (+2:00)',
            'Asia/Tel_Aviv' => 'Tel_Aviv (+2:00)',
            'Asia/Aden' => 'Aden (+3:00)',
            'Asia/Baghdad' => 'Baghdad (+3:00)',
            'Asia/Bahrain' => 'Bahrain (+3:00)',
            'Asia/Kuwait' => 'Kuwait (+3:00)',
            'Asia/Qatar' => 'Qatar (+3:00)',
            'Asia/Tehran' => 'Tehran (+3:30)',
            'Asia/Baku' => 'Baku (+4:00)',
            'Asia/Dubai' => 'Dubai (+4:00)',
            'Asia/Muscat' => 'Muscat (+4:00)',
            'Asia/Tbilisi' => 'Tbilisi (+4:00)',
            'Asia/Yerevan' => 'Yerevan (+4:00)',
            'Asia/Kabul' => 'Kabul (+4:30)',
            'Asia/Aqtau' => 'Aqtau (+5:00)',
            'Asia/Aqtobe' => 'Aqtobe (+5:00)',
            'Asia/Ashgabat' => 'Ashgabat (+5:00)',
            'Asia/Ashkhabad' => 'Ashkhabad (+5:00)',
            'Asia/Dushanbe' => 'Dushanbe (+5:00)',
            'Asia/Karachi' => 'Karachi (+5:00)',
            'Asia/Oral' => 'Oral (+5:00)',
            'Asia/Samarkand' => 'Samarkand (+5:00)',
            'Asia/Tashkent' => 'Tashkent (+5:00)',
            'Asia/Yekaterinburg' => 'Yekaterinburg (+5:00)',
            'Asia/Calcutta' => 'Calcutta (+5:30)',
            'Asia/Colombo' => 'Colombo (+5:30)',
            'Asia/Kolkata' => 'Kolkata (+5:30)',
            'Asia/Katmandu' => 'Katmandu (+5:45)',
            'Asia/Almaty' => 'Almaty (+6:00)',
            'Asia/Bishkek' => 'Bishkek (+6:00)',
            'Asia/Dacca' => 'Dacca (+6:00)',
            'Asia/Dhaka' => 'Dhaka (+6:00)',
            'Asia/Novosibirsk' => 'Novosibirsk (+6:00)',
            'Asia/Omsk' => 'Omsk (+6:00)',
            'Asia/Qyzylorda' => 'Qyzylorda (+6:00)',
            'Asia/Thimbu' => 'Thimbu (+6:00)',
            'Asia/Thimphu' => 'Thimphu (+6:00)',
            'Asia/Rangoon' => 'Rangoon (+6:30)',
            'Asia/Bangkok' => 'Bangkok (+7:00)',
            'Asia/Ho_Chi_Minh' => 'Ho_Chi_Minh (+7:00)',
            'Asia/Hovd' => 'Hovd (+7:00)',
            'Asia/Jakarta' => 'Jakarta (+7:00)',
            'Asia/Krasnoyarsk' => 'Krasnoyarsk (+7:00)',
            'Asia/Phnom_Penh' => 'Phnom_Penh (+7:00)',
            'Asia/Pontianak' => 'Pontianak (+7:00)',
            'Asia/Saigon' => 'Saigon (+7:00)',
            'Asia/Vientiane' => 'Vientiane (+7:00)',
            'Asia/Brunei' => 'Brunei (+8:00)',
            'Asia/Choibalsan' => 'Choibalsan (+8:00)',
            'Asia/Chongqing' => 'Chongqing (+8:00)',
            'Asia/Chungking' => 'Chungking (+8:00)',
            'Asia/Harbin' => 'Harbin (+8:00)',
            'Asia/Hong_Kong' => 'Hong_Kong (+8:00)',
            'Asia/Irkutsk' => 'Irkutsk (+8:00)',
            'Asia/Kashgar' => 'Kashgar (+8:00)',
            'Asia/Kuala_Lumpur' => 'Kuala_Lumpur (+8:00)',
            'Asia/Kuching' => 'Kuching (+8:00)',
            'Asia/Macao' => 'Macao (+8:00)',
            'Asia/Macau' => 'Macau (+8:00)',
            'Asia/Makassar' => 'Makassar (+8:00)',
            'Asia/Manila' => 'Manila (+8:00)',
            'Asia/Shanghai' => 'Shanghai (+8:00)',
            'Asia/Singapore' => 'Singapore (+8:00)',
            'Asia/Taipei' => 'Taipei (+8:00)',
            'Asia/Ujung_Pandang' => 'Ujung_Pandang (+8:00)',
            'Asia/Ulaanbaatar' => 'Ulaanbaatar (+8:00)',
            'Asia/Ulan_Bator' => 'Ulan_Bator (+8:00)',
            'Asia/Urumqi' => 'Urumqi (+8:00)',
            'Asia/Dili' => 'Dili (+9:00)',
            'Asia/Jayapura' => 'Jayapura (+9:00)',
            'Asia/Pyongyang' => 'Pyongyang (+9:00)',
            'Asia/Seoul' => 'Seoul (+9:00)',
            'Asia/Tokyo' => 'Tokyo (+9:00)',
            'Asia/Yakutsk' => 'Yakutsk (+9:00)',
            'Asia/Sakhalin' => 'Sakhalin (+10:00)',
            'Asia/Vladivostok' => 'Vladivostok (+10:00)',
            'Asia/Magadan' => 'Magadan (+11:00)',
            'Asia/Anadyr' => 'Anadyr (+12:00)',
            'Asia/Kamchatka' => 'Kamchatka (+12:00)',
        ],
        'Indian' => [
            'Indian/Antananarivo' => 'Antananarivo (+3:00)',
            'Indian/Comoro' => 'Comoro (+3:00)',
            'Indian/Mayotte' => 'Mayotte (+3:00)',
            'Indian/Mahe' => 'Mahe (+4:00)',
            'Indian/Mauritius' => 'Mauritius (+4:00)',
            'Indian/Reunion' => 'Reunion (+4:00)',
            'Indian/Kerguelen' => 'Kerguelen (+5:00)',
            'Indian/Maldives' => 'Maldives (+5:00)',
            'Indian/Chagos' => 'Chagos (+6:00)',
            'Indian/Cocos' => 'Cocos (+6:30)',
            'Indian/Christmas' => 'Christmas (+7:00)',
        ],
        'Australia' => [
            'Australia/Perth' => 'Perth (+8:00)',
            'Australia/West' => 'West (+8:00)',
            'Australia/Eucla' => 'Eucla (+8:45)',
            'Australia/Adelaide' => 'Adelaide (+9:30)',
            'Australia/Broken_Hill' => 'Broken_Hill (+9:30)',
            'Australia/Darwin' => 'Darwin (+9:30)',
            'Australia/North' => 'North (+9:30)',
            'Australia/South' => 'South (+9:30)',
            'Australia/Yancowinna' => 'Yancowinna (+9:30)',
            'Australia/ACT' => 'ACT (+10:00)',
            'Australia/Brisbane' => 'Brisbane (+10:00)',
            'Australia/Canberra' => 'Canberra (+10:00)',
            'Australia/Currie' => 'Currie (+10:00)',
            'Australia/Hobart' => 'Hobart (+10:00)',
            'Australia/Lindeman' => 'Lindeman (+10:00)',
            'Australia/Melbourne' => 'Melbourne (+10:00)',
            'Australia/NSW' => 'NSW (+10:00)',
            'Australia/Queensland' => 'Queensland (+10:00)',
            'Australia/Sydney' => 'Sydney (+10:00)',
            'Australia/Tasmania' => 'Tasmania (+10:00)',
            'Australia/Victoria' => 'Victoria (+10:00)',
            'Australia/LHI' => 'LHI (+10:30)',
            'Australia/Lord_Howe' => 'Lord_Howe (+10:30)',
        ],
        'Pacific' => [
            'Pacific/Apia' => 'Apia (+13:00)',
            'Pacific/Auckland' => 'Auckland (+12:00)',
            'Pacific/Bougainville' => 'Bougainville (+11:00)',
            'Pacific/Chatham' => 'Chatham (+12:45)',
            'Pacific/Chuuk' => 'Chuuk (+10:00)',
            'Pacific/Easter' => 'Easter (−06:00)',
            'Pacific/Efate' => 'Efate (+11:00)',
            'Pacific/Enderbury' => 'Enderbury (+13:00)',
            'Pacific/Fakaofo' => 'Fakaofo (+13:00)',
            'Pacific/Fiji' => 'Fiji (+12:00)',
            'Pacific/Funafuti' => 'Funafuti (+12:00)',
            'Pacific/Galapagos' => 'Galapagos (−06:00)',
            'Pacific/Gambier' => 'Gambier (−09:00)',
            'Pacific/Guadalcanal' => 'Guadalcanal (+11:00)',
            'Pacific/Guam' => 'Guam (+10:00)',
            'Pacific/Honolulu' => 'Honolulu (−10:00)',
            'Pacific/Kiritimati' => 'Kiritimati (+14:00)',
            'Pacific/Kosrae' => 'Kosrae (+11:00)',
            'Pacific/Kwajalein' => 'Kwajalein (+12:00)',
            'Pacific/Majuro' => 'Majuro (+12:00)',
            'Pacific/Marquesas' => 'Marquesas (−09:30)',
            'Pacific/Nauru' => 'Nauru (+12:00)',
            'Pacific/Niue' => 'Niue (−11:00)',
            'Pacific/Norfolk' => 'Norfolk (+11:00)',
            'Pacific/Noumea' => 'Noumea (+11:00)',
            'Pacific/Pago_Pago' => 'Pago_Pago (−11:00)',
            'Pacific/Palau' => 'Palau (+09:00)',
            'Pacific/Pitcairn' => 'Pitcairn (−08:00)',
            'Pacific/Pohnpei' => 'Pohnpei (+11:00)',
            'Pacific/Port_Moresby' => 'Port_Moresby (+10:00)',
            'Pacific/Rarotonga' => 'Rarotonga (−10:00)',
            'Pacific/Tahiti' => 'Tahiti (−10:00)',
            'Pacific/Tarawa' => 'Tarawa (+12:00)',
            'Pacific/Tongatapu' => 'Tongatapu (+13:00)',
            'Pacific/Wake' => 'Wake (+12:00)',
            'Pacific/Wallis' => 'Wallis (+12:00)',
        ],
    ];

    /**
     * Timezones constructor.
     */
    public function __construct()
    {
        $this->CI = & get_instance();

        $this->CI->load->model('user_model');
    }

    /**
     * Get all timezones to a grouped array (by continent).
     *
     * @return array
     */
    public function to_grouped_array()
    {
        return $this->timezones;
    }

    /**
     * Returns the session timezone or the default timezone as a fallback.
     *
     * @return string
     */
    public function get_session_timezone()
    {
        $default_timezone = $this->get_default_timezone();

        return $this->CI->session->has_userdata('timezone')
            ? $this->CI->session->userdata('timezone')
            : $default_timezone;
    }

    /**
     * Get the default timezone value of the current system.
     *
     * @return string
     */
    public function get_default_timezone()
    {
        return 'UTC';
    }

    /**
     * Convert a date time value to a new timezone.
     *
     * @param string $value Provide a date time value as a string (format Y-m-d H:i:s).
     * @param string $from_timezone From timezone value.
     * @param string $to_timezone To timezone value.
     *
     * @return string
     *
     * @throws Exception
     */
    public function convert($value, $from_timezone, $to_timezone)
    {
        if ( ! $to_timezone || $from_timezone === $to_timezone)
        {
            return $value;
        }

        $from = new DateTimeZone($from_timezone);

        $to = new DateTimeZone($to_timezone);

        $result = new DateTime($value, $from);

        $result->setTimezone($to);

        return $result->format('Y-m-d H:i:s');
    }

    /**
     * Get the timezone name for the provided value.
     *
     * @param string $value
     *
     * @return string|null
     */
    public function get_timezone_name($value)
    {
        $timezones = $this->to_array();

        return isset($timezones[$value]) ? $timezones[$value] : NULL;
    }

    /**
     * Get all timezones to a flat array.
     *
     * @return array
     */
    public function to_array()
    {
        return array_merge(...array_values($this->timezones));
    }
}
