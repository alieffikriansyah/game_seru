<?php

/**
 * UI & App Helper - Coretan
 * Papan Tulis Digital & Catatan Pintar
 */

if (!class_exists('UI')) {
    class UI {
        // Modal
        const MODAL_CONTAINER = 'fixed inset-0 z-50 hidden bg-slate-900/60 flex justify-center items-center backdrop-blur-sm transition-opacity duration-300';
        const MODAL_DIALOG    = 'bg-white rounded-3xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto border border-slate-200';
        const MODAL_HEADER    = 'px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/80 rounded-t-3xl';
        const MODAL_TITLE     = 'text-lg font-bold text-slate-900 flex items-center gap-2';
        const MODAL_CLOSE_BTN = 'text-slate-400 hover:text-slate-700 transition-colors p-1 rounded-lg hover:bg-slate-200/50';

        // Card
        const CARD_CONTAINER = 'bg-white rounded-3xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-slate-100 p-6 mb-6';
        const CARD_TITLE     = 'text-lg font-bold text-slate-800 mb-4 flex items-center gap-2.5';

        // Buttons
        const BTN_PRIMARY   = 'inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-violet-600 to-violet-700 hover:from-violet-500 hover:to-violet-600 text-white font-bold rounded-2xl shadow-lg shadow-violet-500/30 hover:shadow-xl transition-all duration-200 text-sm';
        const BTN_SECONDARY = 'inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-2xl transition-all duration-200 text-sm';
        const BTN_SUCCESS   = 'inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-2xl shadow-sm transition-all duration-200 text-sm';
        const BTN_DANGER    = 'inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-2xl shadow-sm transition-all duration-200 text-sm';

        // Form
        const FORM_INPUT  = 'w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-violet-100 focus:border-violet-500 focus:bg-white outline-none transition-all text-slate-800 text-sm';
        const FORM_LABEL  = 'block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2';
        const FORM_GROUP  = 'space-y-1.5';
    }
}

if (!function_exists('tgl_indo')) {
    function tgl_indo($tanggal, $cetak_waktu = false) {
        if (!$tanggal || $tanggal === '0000-00-00') return '-';
        $bulan = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $ts = strtotime($tanggal);
        if (!$ts) return $tanggal;
        $hasil = date('d', $ts) . ' ' . $bulan[(int)date('m', $ts)] . ' ' . date('Y', $ts);
        if ($cetak_waktu) $hasil .= ' ' . date('H:i', $ts) . ' WIB';
        return $hasil;
    }
}

if (!function_exists('current_user')) {
    function current_user() {
        return [
            'id'    => Session::get('user_id'),
            'name'  => Session::get('name'),
            'email' => Session::get('email'),
        ];
    }
}

if (!function_exists('check_auth')) {
    function check_auth() {
        if (!Session::get('user_id')) {
            Session::set_flashdata('error', 'Silakan login terlebih dahulu.');
            redirect('auth');
            exit;
        }
    }
}
