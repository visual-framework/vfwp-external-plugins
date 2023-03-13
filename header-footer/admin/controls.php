<?php
defined('ABSPATH') || exit;

function hefo_request($name, $default = null) {
    if (!isset($_REQUEST[$name])) {
        return $default;
    }
    return stripslashes_deep($_REQUEST[$name]);
}

function hefo_base_checkbox($name, $label = '') {
    global $options;
    echo '<label>';
    echo '<input type="checkbox" name="options[' . esc_attr($name) . ']" value="1" ' .
    (isset($options[$name]) ? 'checked' : '') . '>';
    echo esc_html($label);
    echo '</label>';
}

function hefo_field_checkbox_only($name, $tips = '', $attrs = '', $link = null) {
    global $options;
    echo '<td><input type="checkbox" name="options[' . esc_attr($name) . ']" value="1" ' .
    (isset($options[$name]) ? 'checked' : '') . '/>';
    echo ' ' . $tips;
    if ($link) {
        echo '<br><a href="' . esc_attr($link) . '" target="_blank">Read more</a>.';
    }
    echo '</td>';
}

function hefo_field_text($name, $label = '', $tips = '') {
    global $options;

    if (!isset($options[$name])) {
        $options[$name] = '';
    }

    echo '<th scope="row">';
    echo '<label>' . esc_html($label) . '</label></th>';
    echo '<td><input type="text" name="options[' . esc_attr($name) . ']" value="' .
    esc_attr($options[$name]) . '" size="50"/>';
    echo '<br /> ' . $tips;
    echo '</td>';
}

function hefo_base_text($name) {
    global $options;

    if (!isset($options[$name])) {
        $options[$name] = '';
    }

    echo '<input type="text" name="options[' . esc_attr($name) . ']" value="' .
    esc_attr($options[$name]) . '" size="30">';
}

function hefo_field_textarea($name, $label = '', $tips = '') {
    global $options;

    if (!isset($options[$name])) {
        $options[$name] = '';
    }

    if (is_array($options[$name])) {
        $options[$name] = implode("\n", $options[$name]);
    }

    echo '<th scope="row">';
    echo '<label>' . esc_html($label) . '</label></th>';
    echo '<td><textarea style="width: 100%; height: 100px" wrap="off" name="options[' . esc_attr($name) . ']">' .
    esc_html($options[$name]) . '</textarea>';
    echo '<p class="description">' . esc_html($tips) . '</p>';
    echo '</td>';
}

function hefo_base_textarea_cm($name, $type = '', $tips = '') {
    global $options;

    if (!empty($type)) {
        $type = '-' . $type;
    }
    
    if (!isset($options[$name])) {
        $options[$name] = '';
    }

    if (is_array($options[$name])) {
        $options[$name] = implode("\n", $options[$name]);
    }

    echo '<textarea class="hefo-cm' . esc_attr($type) . '" name="options[' . esc_attr($name) . ']" onfocus="hefo_cm_on(this)">';
    echo esc_html($options[$name]);
    echo '</textarea>';
    echo '<p class="description">' . $tips . '</p>';
}

function hefo_rule($number) {
    global $options;
    
    if (!isset($options['inner_pos_' . $number])) {
        $options['inner_pos_' . $number] = 'after';
    }
    
    if (!isset($options['inner_skip_' . $number])) {
        $options['inner_skip_' . $number] = 0;
    }
    
    if (!isset($options['inner_tag_' . $number])) {
        $options['inner_tag_' . $number] = '';
    }

    echo '<div class="rules">';
    echo '<div style="float: left">Inject</div>';
    echo '<select style="float: left" name="options[inner_pos_' . esc_attr($number) . ']">';
    echo '<option value="after"';
    echo $options['inner_pos_' . $number] == 'after' ? ' selected' : '';
    echo '>after</option>';
    echo '<option value="before"';
    echo $options['inner_pos_' . $number] == 'before' ? ' selected' : '';
    echo '>before</option>';
    echo '</select>';
    echo '<input style="float: left" type="text" placeholder="marker" name="options[inner_tag_' . esc_attr($number) . ']" value="';
    echo esc_attr($options['inner_tag_' . $number]);
    echo '">';
    echo '<div style="float: left">skipping</div>';
    echo '<input style="float: left" type="text" size="5" name="options[inner_skip_' . esc_attr($number) . ']" value="';
    echo esc_attr($options['inner_skip_' . $number]);
    echo '">';
    echo '<div style="float: left">chars, on failure inject</div>';
    echo '<select style="float: left" name="options[inner_alt_' . esc_attr($number) . ']">';
    echo '<option value=""';
    echo $options['inner_alt_' . $number] == 'after' ? ' selected' : '';
    echo '>nowhere</option>';
    echo '<option value="after"';
    echo $options['inner_alt_' . $number] == 'after' ? ' selected' : '';
    echo '>after the content</option>';
    echo '<option value="before"';
    echo $options['inner_alt_' . $number] == 'before' ? ' selected' : '';
    echo '>before the content</option>';
    echo '</select>';
    echo '<div class="clearfix"></div></div>';
}


