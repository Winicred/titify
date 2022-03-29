<?php
//
//if (is_worthy('p')) {
//    $STH = pdo()->query("SELECT COUNT(*) as count FROM tickets WHERE have_answer = '0'");
//    $countOfOpenTickets = $STH->fetchColumn();
//}

// получение навигационной цепочки
global $nav;

// вызов шаблонов и передача переменных
//foreach (['index/top_panel.tpl', 'index/page_top.tpl'] as $template) {
//
//    // загрузка шаблона
//    tpl()->load_template($template);
//
//    // передача переменной url сайта
//    tpl()->set("{site_host}", '');
//
//    // передача переменной названия сайта
//    tpl()->set("{site_name}", configs()->name);
//
//    // передача локальных переменных
//    tpl()->set("{user_id}", user()->id);
//    tpl()->set("{login}", user()->login);
//    tpl()->set("{avatar}", user()->avatar);
//    tpl()->set("{name}", user()->name);
//    tpl()->set("{lastname}", user()->lastname);
////    tpl()->set("{menu}", $nav);
//
//    // компиляция шаблона
//    tpl()->compile('content');
//
//    // очистка переменных
//    tpl()->clear();
//}