<?php
/**
 * German copy for the front page, as ACF field values.
 *
 * The front page stores no post content — every heading and paragraph is an ACF
 * field on the page itself, read through iptv_text(). So the German home page
 * is translated by filling those fields rather than by substituting into
 * content, and the table lives here so the copy is in version control and can
 * be re-applied if the page is ever rebuilt.
 *
 * Only ever written into an *empty* field: once someone edits the page in
 * wp-admin, their wording wins. See iptv_de_fill_home_fields().
 *
 * "Nordic IPTV" is the focus keyword for this page and appears in the H1, the
 * hero paragraph, the section copy, both content image alts and the FAQ
 * heading — the places Rank Math's tests actually read, and which
 * inc/front-page-seo.php feeds to the analyser.
 *
 * Prices here are illustrative comparison figures, in euros because that is
 * what German visitors are quoted; the live plan prices come from the price
 * table, not from these fields.
 *
 * @package Nordic_IPTV
 */

$de_cta = function ($title) {
    return array('title' => $title, 'url' => '#pricing', 'target' => '');
};

return array(

    // ── Navigation ───────────────────────────────────────────────────────────
    'nav_link_home'     => 'Start',
    'nav_link_features' => 'Funktionen',
    'nav_link_pricing'  => 'Preise',
    'nav_link_guide'    => 'Anleitung',
    'nav_link_contact'  => 'Kontakt',
    'nav_link_account'  => 'Mein Konto',
    'nav_cta_label'     => 'Jetzt freischalten',
    'nav_region_label'  => 'Sprache',

    // ── Hero ─────────────────────────────────────────────────────────────────
    'hero_badge'         => '4,8/5 von über 53.000 Kunden',
    'hero_savings_badge' => 'Sparen Sie über 1.100 € im Jahr!',
    'hero_title'         => 'Nordic IPTV ohne Grenzen.',
    'hero_title_span'    => 'Jedes Spiel. Jeder Sender.',
    'hero_title_3'       => 'Ein einziges Abo.',
    'hero_subtitle'      => 'Sie suchen Nordic IPTV? NordicTV bietet 40.000+ Livesender, 200.000+ Filme und Serien und jeden Sport in 4K/8K – sofort und auf jedem Gerät.',
    'hero_cta'           => array('title' => '', 'url' => '#pricing', 'target' => ''),
    'hero_button'        => array('title' => '', 'url' => '#pricing', 'target' => ''),
    'hero_primary_cta_label'   => 'Jetzt starten',
    'hero_primary_cta_url'     => '#pricing',
    'hero_secondary_cta_label' => 'Mein Bereich',
    'hero_secondary_cta_url'   => 'https://panel.nordictv.io/login',
    'hero_trust_label'   => 'Hervorragend',
    'hero_trust_score'   => '4,9',
    'hero_trust_suffix'  => 'von 5',
    'hero_image_url'     => 'https://nordictv.io/wp-content/uploads/2026/07/hero-image.webp',
    'hero_image_alt'     => 'Nordic IPTV – Livesport und Unterhaltung bei NordicTV',
    'hero_stat_1_val'    => '42.537',
    'hero_stat_1_desc'   => 'Zuschauer streamen gerade',
    'hero_stat_2_val'    => '0 €',
    'hero_stat_2_label'  => 'PPV-Events',
    'hero_stat_2_desc'   => 'Basketball, MMA, Boxen … – alles inklusive',
    'hero_stat_3_val'    => '40.000+',
    'hero_stat_3_label'  => 'Livesender',
    'hero_stat_3_desc'   => 'Aus 198 Ländern',

    // ── Live channels ────────────────────────────────────────────────────────
    'showcase_tag'          => 'Unbegrenzte Unterhaltung',
    'showcase_title'        => 'Entdecken Sie',
    'showcase_title_span'   => '40.000+',
    'showcase_title_3'      => 'Live-TV-Sender',
    'showcase_subtitle'     => 'Nordic IPTV bringt Live-TV, Filme und Serien über das Internet direkt auf Ihre Geräte – ohne Satellitenschüssel, ohne Vertrag, ohne teure Hardware. Inhalte aus 198 Ländern, mehrsprachige Untertitel und 4K/8K Ultra HD. Täglich kommen neue Titel dazu.',
    'showcase_f1'           => '200.000+ Filme und Serien',
    'showcase_f2'           => '4K- und 8K-Ultra-HD-Qualität',
    'showcase_f3'           => 'Mehrsprachige Untertitel',
    'showcase_f4'           => 'Tägliche Aktualisierungen',
    'showcase_channel_more' => '+40.000 Live-TV-Sender aus 198 Ländern',
    'showcase_cta'          => $de_cta('Heute noch loslegen'),

    // ── Movies & series ──────────────────────────────────────────────────────
    'vod_title'             => 'Genießen Sie',
    'vod_title_span'        => '200.000+',
    'vod_title_3'           => 'Filme und Serien',
    'vod_subtitle'          => 'Alle Genres und Sprachen, auf Abruf und wann es Ihnen passt. Komplette Programmübersicht mit täglichen Updates und mehrsprachigen Untertiteln.',
    'vod_cta'               => 'Heute noch anschauen',
    'vod_search_placeholder' => 'Film oder Serie suchen',
    'vod_image_alt'         => 'Filme und Serien mit Nordic IPTV bei NordicTV',

    // ── Features ─────────────────────────────────────────────────────────────
    'features_tag'         => 'Funktionen',
    'features_title'       => 'Alles an einem Ort',
    'features_title_span'  => 'mit Nordic IPTV',
    'features_subtitle'    => 'Nordic IPTV überzeugt durch ein unschlagbares Preis-Leistungs-Verhältnis und hohe Zuverlässigkeit. Das macht uns zum besten IPTV-Dienst für den deutschsprachigen Raum:',
    'feature_1_title'      => '40.000+ Livesender',
    'feature_1_desc'       => 'Sport, Nachrichten, Unterhaltung und Kindersender aus 198 Ländern – vom Regionalsender bis zum globalen Netzwerk.',
    'feature_2_title'      => 'PPV-Events gratis',
    'feature_2_desc'       => 'Kampfsport – alle PPV-Events ohne Aufpreis. Das spart bis zu 60 € pro Event.',
    'feature_3_title'      => 'Sport live',
    'feature_3_desc'       => 'Europäischer Spitzenfußball, Klubwettbewerbe, American Football, Basketball und Motorsport – alle großen Ligen, live.',
    'feature_4_title'      => '200.000+ Filme und Serien',
    'feature_4_desc'       => 'Von der aktuellen Premiere bis zum Klassiker. Jeden Tag kommen neue Titel dazu.',
    'feature_5_title'      => 'Gestochen scharfe 4K-Qualität',
    'feature_5_desc'       => 'Ultra HD auf allen Geräten, optimiert für null Buffering und null Ruckler.',
    'feature_6_title'      => 'Support rund um die Uhr',
    'feature_6_desc'       => 'Echte Menschen im Livechat und auf WhatsApp, 24 Stunden am Tag.',
    'feature_7_title'      => '7 Tage Catch-up-TV',
    'feature_7_desc'       => 'Spiel verpasst? Springen Sie bis zu eine Woche zurück und sehen Sie es, wann es Ihnen passt.',
    'feature_8_title'      => 'Anti-Buffer™ 3.0',
    'feature_8_desc'       => 'Intelligente Stream-Stabilisierung hält die Wiedergabe flüssig – auch bei normalen Anschlüssen.',
    'features_cta'         => $de_cta('Jetzt losschauen'),

    // ── Sport ────────────────────────────────────────────────────────────────
    'sports_tag'        => 'Sport',
    'sports_title'      => 'Verpassen Sie nie wieder ein',
    'sports_title_span' => 'Spiel',
    'sports_desc'       => 'Alle Ihre Lieblingsligen und -turniere, live und auf Abruf.',
    'sport_1_name'      => 'Fußball',
    'sport_1_subtitle'  => 'Liga, Pokal und Nationalmannschaft',
    'sport_2_name'      => 'Basketball',
    'sport_2_subtitle'  => 'Hauptrunde, Playoffs und Finals',
    'sport_3_name'      => 'Baseball',
    'sport_3_subtitle'  => 'Komplette Saison, World Series',
    'sport_4_name'      => 'American Football',
    'sport_4_subtitle'  => 'Regular Season, Super Bowl',
    'sport_5_name'      => 'Motorsport',
    'sport_5_subtitle'  => 'Alle Grand-Prix-Rennen',
    'sport_6_name'      => 'Kampfsport',
    'sport_6_subtitle'  => 'Alle PPV-Events gratis',
    'sport_live_text'   => 'JETZT LIVE',
    'sports_cta'        => $de_cta('Das ultimative Sportpaket sichern'),
    'sports_image_alt'  => 'Livesport mit Nordic IPTV bei NordicTV',

    // ── Comparison ───────────────────────────────────────────────────────────
    'comp_badge'          => 'Sparen Sie jedes Jahr Hunderte Euro',
    'comp_tag'            => 'Geld sparen',
    'comp_col_1_label'    => 'OHNE NORDIC IPTV',
    'comp_col_2_label'    => 'MIT NORDIC IPTV',
    'comp_c1_total_label' => 'Kosten pro Jahr',
    'comp_c1_total_val'   => '1.200 €+',
    'comp_c2_total_label' => 'Nordic IPTV Kosten pro Jahr',
    'comp_savings_label'  => 'Ihre Ersparnis pro Jahr',
    'comp_monthly_sub'    => 'Nur ~5,83 €/Monat',
    'comp_title_main'     => 'Zahlen Sie nicht länger zu viel fürs Kabelfernsehen.',
    'comp_title_sub'      => 'Wechseln Sie zu Nordic IPTV.',
    'comp_desc'           => 'Warum Geld verschwenden? Sofortiger Zugriff auf 40.000+ Livesender und 200.000+ Filme und Serien in brillantem 4K. Keine Verträge, keine versteckten Gebühren – nur Unterhaltung.',
    'comp_price'          => '69,99 €',
    'comp_price_sub'      => 'Nur ~5,83 €/Monat',
    'comp_savings_val'    => '1.130 €',
    'comp_cta_text'       => $de_cta('Jetzt streamen'),
    'comp_cta'            => array('title' => '', 'url' => '#pricing', 'target' => ''),
    'comp_col_1_rows'     => array(
        array('label' => 'Streamingdienst 1', 'value' => '17,99 €/Mon.'),
        array('label' => 'Streamingdienst 2', 'value' => '12,99 €/Mon.'),
        array('label' => 'Streamingdienst 3', 'value' => '14,99 €/Mon.'),
        array('label' => 'Sportpaket',        'value' => '35,00 €/Mon.'),
        array('label' => 'PPV-Events (pro Jahr)', 'value' => '70 €+'),
    ),
    'comp_col_2_rows'     => array(
        array('label' => 'Alle Streaminginhalte', 'value' => 'Inklusive'),
        array('label' => 'Sport live',            'value' => 'Inklusive'),
        array('label' => 'Alle PPV-Events',       'value' => 'Inklusive'),
        array('label' => '40.000+ Livesender',    'value' => 'Inklusive'),
        array('label' => 'Gestochen scharfes 4K', 'value' => 'Inklusive'),
    ),

    // ── Pricing configurator ─────────────────────────────────────────────────
    'cta_bar_label'        => '90 % GÜNSTIGER ALS HERKÖMMLICHE ANBIETER',
    'pricing_badge'        => 'Schlauer streamen, weniger zahlen – heute starten!',
    'pricing_title'        => 'Unbegrenztes Streaming',
    'pricing_title_span'   => 'zu einem fairen Preis',
    'pricing_subtitle'     => '40.000+ Livesender und 200.000+ Filme und Serien in 4K mit Nordic IPTV.',
    'step_1_label'         => 'Geräte wählen',
    'step_2_label'         => 'Laufzeit wählen',
    'step_3_label'         => 'Bestellung abschließen',
    'devices_title'        => 'Schauen auf',
    'device_singular'      => 'Gerät',
    'device_plural'        => 'Geräte',
    'screens_title'        => 'Wie viele Bildschirme?',
    'screen_singular'      => 'Bildschirm',
    'screen_plural'        => 'Bildschirme',
    'duration_title'       => 'Wählen Sie Ihre Laufzeit',
    'month_1_label'        => '1 Monat',
    'month_3_label'        => '3 Monate',
    'month_6_label'        => '6 Monate',
    'month_12_label'       => '12 Monate',
    'save_40_text'         => '40 % sparen',
    'save_58_text'         => '58 % sparen',
    'best_value_text'      => 'Bestes Angebot',
    'popular_badge'        => 'BELIEBT',
    'per_month'            => 'pro Monat',
    'save_more'            => 'Mehr sparen',
    'best_deal'            => 'Bester Preis!',
    'total_label'          => 'Ihr Gesamtpreis',
    'checkout_button'      => 'Bestellung abschließen',
    'checkout_slots_line'  => '🔥 Nur noch 32 Aktivierungsplätze in diesem Monat',
    'checkout_trust_1'     => '14 Tage Geld-zurück',
    'checkout_trust_2'     => 'Sofortige Aktivierung',
    'checkout_trust_3'     => 'Keine automatische Verlängerung',
    'payments_label'       => 'Sichere Zahlung',
    'guarantee_text'       => '14 Tage Geld-zurück-Garantie. Ohne Wenn und Aber.',
    'plan_includes_title'  => 'In jedem Abo ist alles enthalten',
    'trial_prompt'         => 'Noch nicht kaufbereit?',
    'trial_cta'            => '24-Stunden-Test starten – ohne Karte',
    'trust_1_title'        => 'Transparente Preise',
    'trust_1_desc'         => 'Keine Verträge. Jederzeit kündbar.',
    'trust_2_title'        => 'Sofortige Aktivierung',
    'trust_2_desc'         => 'In wenigen Minuten startklar.',
    'trust_3_title'        => 'Ohne Risiko',
    'trust_3_desc'         => '14 Tage Geld-zurück-Garantie.',

    // ── Steps ────────────────────────────────────────────────────────────────
    'steps_tag'          => 'Einfache Einrichtung',
    'steps_title'        => 'Streamen Sie in',
    'steps_title_span'   => '3 Schritten',
    'steps_subtitle'     => 'In Minuten startklar, nicht in Stunden. Unser Team sorgt für eine reibungslose Einrichtung.',
    'step_1_title'       => 'Abo auswählen',
    'step_1_desc'        => 'Sehen Sie sich unsere flexiblen Pakete an und wählen Sie das, das zu Ihrem Budget und Ihren Geräten passt.',
    'step_2_title'       => 'Zahlung abschließen',
    'step_2_desc'        => 'Zahlen Sie sicher über unser verschlüsseltes Zahlungs-Gateway. Wir akzeptieren die gängigen Karten und Kryptowährungen.',
    'step_3_title'       => 'Losschauen',
    'step_3_desc'        => 'Wir richten Ihr Konto ein und schicken Ihnen die Zugangsdaten per E-Mail.',
    'step_1_visual'      => 'Registrieren',
    'step_2_visual'      => 'Gerät auswählen',
    'step_2_visual_cta'  => 'Herunterladen',
    'step_3_visual'      => 'Zugangsdaten eingeben',
    'step_3_visual_cta'  => 'Jetzt schauen',
    'steps_cta'          => $de_cta('Heute sofort freischalten!'),

    // ── Devices ──────────────────────────────────────────────────────────────
    'devices_tag'           => 'Kompatibilität',
    'devices_title_span'    => 'Alle Geräte',
    'devices_subtitle'      => 'Läuft einwandfrei auf Smart-TV, Android, iOS, Firestick, MAG und mehr.',
    'devices_section_title' => 'Läuft auf jedem Bildschirm',
    'device_list'           => 'Smart-TV, Android TV, Apple TV, Fire Stick, iPhone / iPad, Android-Handy, Windows, Mac, Set-Top-Box, Chromecast, Roku, Kodi',

    // ── Reviews ──────────────────────────────────────────────────────────────
    'reviews_title'    => 'Was unsere Kunden über Nordic IPTV sagen',
    'reviews_subtitle' => 'Schließen Sie sich Tausenden an, die dem Kabelfernsehen den Rücken gekehrt haben.',
    'reviews_list'     => array(
        array(
            'review_title'  => 'Gestochen scharf auf allen Geräten',
            'review_when'   => 'Dez. 2024',
            'review_author' => 'Marcus L. · Stockholm, SE',
            'review_text'   => 'Gestochen scharfes Bild auf allen meinen Geräten. Kein Buffering, keine Aussetzer – einfach sauberes Streaming. Habe vor sechs Monaten das Kabelabo gekündigt und es keine Sekunde bereut.',
        ),
        array(
            'review_title'  => 'Das Sportangebot ist überragend',
            'review_when'   => 'Jan. 2025',
            'review_author' => 'Anna K. · Oslo, NO',
            'review_text'   => 'Jedes Topspiel, die europäischen Klubabende, Basketball – alles in HD. Die Einrichtung hat fünf Minuten gedauert. Wirklich starker Dienst.',
        ),
        array(
            'review_title'  => 'Die Qualität hat mich überrascht',
            'review_when'   => 'Nov. 2024',
            'review_author' => 'Thomas B. · Kopenhagen, DK',
            'review_text'   => 'Ich war zuerst skeptisch, aber die Qualität hat mich überrascht. 40.000+ Sender und alle laufen einwandfrei. Der Support hat innerhalb einer Stunde geantwortet.',
        ),
        array(
            'review_title'  => 'Läuft auf allem gleichzeitig',
            'review_when'   => 'Feb. 2025',
            'review_author' => 'Erika V. · Helsinki, FI',
            'review_text'   => 'Endlich ein Dienst, der wirklich auf meinem Fire Stick UND dem Smart-TV gleichzeitig läuft. Das Paket für vier Geräte ist jeden Cent wert.',
        ),
        array(
            'review_title'  => 'Nie eine Unterbrechung',
            'review_when'   => 'Jan. 2025',
            'review_author' => 'Jonas H. · Göteborg, SE',
            'review_text'   => 'Ich habe NordicTV jetzt seit über einem Jahr. Null Ausfälle, und die Senderliste wird ständig aktualisiert. So soll Streaming sein.',
        ),
        array(
            'review_title'  => 'Gutes Preis-Leistungs-Verhältnis',
            'review_when'   => 'vor 3 Tagen',
            'review_author' => 'Sofia N. · Bergen, NO',
            'review_text'   => 'Gutes Preis-Leistungs-Verhältnis. Der Support hat meine Fragen innerhalb von Minuten auf WhatsApp beantwortet – ohne Warteschleife.',
        ),
        array(
            'review_title'  => 'Ersetzt vier Abos',
            'review_when'   => 'vor 1 Woche',
            'review_author' => 'Henrik D. · Malmö, SE',
            'review_text'   => 'Ich habe das Kabelabo und drei Streaming-Apps gekündigt. Eine Rechnung, mehr Inhalte und 4K auf allem. Hätte ich schon vor Jahren machen sollen.',
        ),
    ),

    // ── FAQ ──────────────────────────────────────────────────────────────────
    'faq_title'    => 'Häufige Fragen zu Nordic IPTV',
    'faq_subtitle' => 'Antworten auf die Fragen, die uns am häufigsten gestellt werden.',
    'faq_list'     => array(
        array(
            'question' => 'Was ist IPTV?',
            'answer'   => 'IPTV (Internet Protocol Television) ist eine moderne Art, Fernsehsender, Filme und Serien über die Internetverbindung zu sehen – statt über klassisches Kabel- oder Satellitenfernsehen.',
        ),
        array(
            'question' => 'Was ist NordicTV?',
            'answer'   => 'NordicTV ist ein Premium-Anbieter für Nordic IPTV mit 40.000+ Livesendern, 200.000+ Filmen und Serien, brillanter 4K-Ultra-HD-Qualität und Kundenservice rund um die Uhr – nutzbar auf mehreren Geräten.',
        ),
        array(
            'question' => 'Wie schließe ich ein Abo bei NordicTV ab?',
            'answer'   => 'Wählen Sie auf nordictv.io ein Abo, schließen Sie Ihre Bestellung ab, und Sie erhalten Ihre Aktivierungsdaten samt verständlicher Einrichtungsanleitung per E-Mail.',
        ),
        array(
            'question' => 'Welche Geräte werden unterstützt?',
            'answer'   => 'NordicTV läuft auf den meisten gängigen Geräten: Smart-TV, Android TV und Android-Handys, iPhone und iPad, Amazon Firestick / Fire TV, MAG-Boxen sowie Windows und macOS. Bei der Einrichtung hilft unser Support rund um die Uhr.',
        ),
        array(
            'question' => 'Wie viele Geräte kann ich gleichzeitig nutzen?',
            'answer'   => 'Jedes Abo erlaubt bis zu 4 gleichzeitige Verbindungen. Innerhalb dieses Rahmens können Sie auf mehreren Geräten gleichzeitig schauen.',
        ),
        array(
            'question' => 'Welche Inhalte bieten Sie an?',
            'answer'   => 'Nordic IPTV umfasst 40.000+ Live-TV-Sender (Sport, Unterhaltung, Nachrichten, international), 200.000+ Filme und Serien, Streaming in 4K Ultra HD und HD sowie eine laufend aktualisierte Bibliothek.',
        ),
        array(
            'question' => 'Wie erhalte ich meine Zugangsdaten?',
            'answer'   => 'Nach bestätigter Zahlung gehen Ihre Zugangsdaten (Benutzername, Passwort oder Playlist) an Ihre E-Mail-Adresse. In der Regel dauert das wenige Minuten, in Einzelfällen bis zu 8 Stunden.',
        ),
        array(
            'question' => 'Bieten Sie Sport- und Premiumsender an?',
            'answer'   => 'Ja. NordicTV enthält ein breites Angebot an Sport, Premiumunterhaltung und internationalen Sendern, inklusive Liveübertragungen und der großen Ligen.',
        ),
        array(
            'question' => 'Welche Zahlungsmethoden akzeptieren Sie?',
            'answer'   => 'Wir akzeptieren sichere Zahlungen per Kredit- und Debitkarte sowie PayPal, wo verfügbar. Alle Zahlungen laufen über gesicherte Zahlungsdienstleister.',
        ),
        array(
            'question' => 'Bieten Sie Erstattungen an?',
            'answer'   => 'Kundenzufriedenheit ist uns wichtig. Wenn ernsthafte Probleme mit dem Dienst auftreten, wenden Sie sich an unseren Support – wir tun unser Bestes, um Ihnen zu helfen.',
        ),
        array(
            'question' => 'Wie erreiche ich den Support?',
            'answer'   => 'Unser Support-Team erreichen Sie rund um die Uhr unter: support@nordictv.io',
        ),
        array(
            'question' => 'Kann ich Reseller werden?',
            'answer'   => 'Ja, Reseller-Möglichkeiten gibt es. Schreiben Sie uns an support@nordictv.io für weitere Informationen.',
        ),
    ),

    // ── Closing CTA ──────────────────────────────────────────────────────────
    'cta_title'    => 'Genug von Streaming-Problemen?',
    'cta_subtitle' => 'Schließen Sie sich Tausenden zufriedener Kunden an, die zu Premium-IPTV gewechselt sind.',
    'cta_btn_text' => array('title' => '', 'url' => '#pricing', 'target' => ''),
    'cta_btn_link' => array('title' => '', 'url' => '#pricing', 'target' => ''),
    'cta_f1'       => '256-Bit-SSL-Verschlüsselung',
    'cta_f2'       => 'Sofortige Aktivierung',
    'cta_f3'       => 'Support rund um die Uhr',

    // ── Contact ──────────────────────────────────────────────────────────────
    'contact_title'    => 'Wir sind für Sie da',
    'contact_subtitle' => 'Melden Sie sich jederzeit per E-Mail, WhatsApp oder Telegram. Unser Support antwortet meist innerhalb weniger Minuten.',
    'contact_cards'    => array(
        array(
            'card_label' => 'E-Mail-Support',
            'card_value' => 'support@nordictv.io',
            'card_link'  => 'mailto:support@nordictv.io',
            'card_blank' => '',
        ),
        array(
            'card_label' => 'WhatsApp',
            'card_value' => 'Schreiben Sie uns live',
            'card_link'  => 'https://wa.me/33745476690',
            'card_blank' => '1',
        ),
        array(
            'card_label' => 'Telegram',
            'card_value' => '@NordicTV',
            'card_link'  => 'https://t.me/NordicTV',
            'card_blank' => '1',
        ),
    ),

    // ── Footer ───────────────────────────────────────────────────────────────
    'footer_desc'      => 'Premium-Streaming mit Nordic IPTV: 40.000+ Sender aus aller Welt.',
    'footer_copyright' => 'Alle Rechte vorbehalten.',
);
