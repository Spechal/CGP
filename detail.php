<?php

    require_once 'conf/common.inc.php';
    require_once 'inc/functions.inc.php';
    require_once 'inc/html.inc.php';
    require_once 'inc/collectd.inc.php';

    # use width/height from config if nothing is given
    if (empty($_GET['x']))
        $_GET['x'] = $CONFIG['detail-width'];
    if (empty($_GET['y']))
        $_GET['y'] = $CONFIG['detail-heigth'];

    $host = validate_get(GET('h'), 'host');
    $plugin = validate_get(GET('p'), 'plugin');
    $pinstance = validate_get(GET('pi'), 'pinstance');
    $category = validate_get(GET('c'), 'category');
    $type = validate_get(GET('t'), 'type');
    $tinstance = validate_get(GET('ti'), 'tinstance');
    $width = GET('x');
    $heigth = GET('y');
    $seconds = GET('s');

    $selected_plugins = !$plugin ? $CONFIG['overview'] : array($plugin);

    html_start();

    $plugins = collectd_plugins($host);

    if(!$plugins) {
        echo "Unknown host\n";
        return false;
    }

    echo '<div class="row-fluid">';

        echo '<div class="span2">';
        list_plugins($plugins, array($host), $CONFIG);
        echo '</div>';

        echo '<div class="span10">';
        printf('<h2>%s</h2>'."\n", $host);

        plugin_header($host, $plugin);

        $args = $_GET;
        echo '<ul class="inline">';
            foreach($CONFIG['term'] as $key => $s) {
                $args['s'] = $s;
                $selected = selected_timerange($seconds, $s);
                printf('<li><a %s href="%s%s">%s</a></li>'."\n",
                    $selected, $CONFIG['weburl'], build_url('detail.php', $args), $key);
            }
        echo '</ul>';

        printf('<img src="%s%s">'."\n", $CONFIG['weburl'], build_url('graph.php', $_GET));

    echo '</div>';

    html_end();
