<?php

/**
 * Menu Helper - Coretan App Navigation
 */

if (!function_exists('render_sidebar_menu')) {
    function render_sidebar_menu($role, $current_uri = '') {
        // Coretan: semua user punya menu yang sama (tidak ada role hierarchy)
        $items = [
            ['name' => 'Beranda',       'url' => 'dashboard',         'icon' => 'fa-house'],
            ['type' => 'divider', 'label' => 'Kreasi'],
            ['name' => 'Papan Tulis',   'url' => 'whiteboard',        'icon' => 'fa-chalkboard'],
            ['name' => 'Catatan',       'url' => 'notes',             'icon' => 'fa-note-sticky'],
        ];

        $html = '<nav class="p-3 flex-1 overflow-y-auto">';

        $section_label_cls = 'px-3 pt-4 pb-1 text-[10px] font-bold uppercase tracking-widest text-white/30';
        $item_base = 'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-150 font-medium';

        foreach ($items as $item) {
            if (isset($item['type']) && $item['type'] === 'divider') {
                if (!empty($item['label'])) {
                    $html .= '<div class="' . $section_label_cls . '">' . htmlspecialchars($item['label']) . '</div>';
                } else {
                    $html .= '<div class="my-2 border-t border-white/10"></div>';
                }
                continue;
            }

            $is_active = (
                strpos($current_uri, $item['url']) === 0
                || ($item['url'] === 'dashboard' && ($current_uri === '' || $current_uri === 'dashboard'))
            );

            if ($is_active) {
                $cls = 'bg-gradient-to-r from-violet-600 to-violet-500 text-white shadow-lg shadow-violet-500/30 font-semibold';
            } else {
                $cls = 'text-white/60 hover:bg-white/10 hover:text-white';
            }

            $html .= '<a href="' . site_url($item['url']) . '" '
                   . 'class="' . $item_base . ' ' . $cls . '">'
                   . '<i class="fa ' . htmlspecialchars($item['icon']) . ' w-4 text-center text-[13px]"></i>'
                   . '<span>' . htmlspecialchars($item['name']) . '</span>'
                   . '</a>';
        }

        $html .= '</nav>';
        return $html;
    }
}
