<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Plugin strings are defined here.
 *
 * @package     availability_payallways
 * @category    string
 * @copyright   <email1>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['1'] = 'uk';
$string['description'] = 'Контролює платний доступ до елементів курсу. Студенти не можуть отримати доступ до модуля, доки не сплатять вказану викладачем ціну';
$string['title'] = 'PayAllWays';
$string['pluginname'] = 'PayAllWays';
$string['pluginname_desc'] = 'Плагін PayAllWays дозволяє налаштувати платний доступ до активностей та інших елементів курсу';
$string['legalentity'] = 'Юридична особа';
$string['fop'] = 'ФОП';
$string['billingentity'] = 'Отримувач';
$string['billingentity_help'] = 'Оберіть схему оплати: "юридична особа" встановлює глобальні реквізити для виплат, а "ФОП" дозволяє викладачу/власнику курсу індивідуально вказати свої особисті реквізити для виплат від своїх студентів';
$string['paymentaccount'] = 'Розрахунковий рахунок';
$string['paymentaccount_help'] = 'Розрахунковий рахунок, куди будуть зараховуватися кошти платника';
$string['paymentnote'] = 'Примітка до платежу';
$string['cardnum'] = 'Номер платіжної карти Visa або MC';
$string['card_details'] = 'Редагування деталей картки';
$string['card_section_heading'] = 'Платіжні реквізити';
$string['getdescription'] = 'Користувач сплатив за доступ';
$string['getdescriptionnot'] = 'Модуль доступний безкоштовно';
$string['makepaymentnow'] = 'Оплатити зараз';

$string['contactmanager'] = 'Помилка отримання даних курсу, будь ласка запитайте допомоги у менеджера або викладача';
$string['payallways_settings'] = 'Редагувати налаштування PayAllWays';
$string['plugin_oprtating_mode'] = 'Режим роботи плагіну';
$string['mode_course'] = 'Обмежити доступ до всього курсу починаючи з вказаного елементу';
$string['mode_activity'] = 'Обмежити доступ до окремих елементів курсу';
$string['section_block_headline'] = 'Преміум-секція';
$string['bankmfo'] = 'МФО банку';
$string['access_cost'] = 'Вартість доступу';
$string['edrpu'] = 'Код ЄДРПОУ';
$string['entity_name'] = 'Назва юр.особи';
$string['passport'] = 'Серія та номер паспорту або номер ID-картки';
$string['fio'] = 'ПІБ';
$string['inn'] = 'ІПН';
$string['data_valid'] = 'Усі реквізити перевірені мною особисто та вірні';
$string['rules'] = 'Використовуя плагін PayAllWays ви погоджуєтесь з <a href="https://expert.education/mod/page/view.php?id=113&forceview=1" target="_blank">правилами</a>';

//errors
$string['nopermissionseditcourse'] = 'Вибачте, у вас немає прав для редагування цього курсу';
$string['paidinvalidparam'] = 'Параметр is_paid не відповідає типу';
$string['cost_error'] = 'Вартість курсу має бути числом та не може бути менше нуля';
$string['empty_field'] = 'Поле не може бути порожнім';
$string['account_error'] = 'Некоректно вказаний номер рахунку. Будь ласка, введіть тільки цифри без пробілів';
$string['mfo_error'] = 'Некоректно вказаний номер МФО (Введіть 6 цифр без пробілів)';
$string['edrpu_error'] = 'Код ЄДРПОУ вказан некоректно';
$string['iban_error'] = 'Формат IBAN: 2 літери кода країни + 27 цифр без пробілів';