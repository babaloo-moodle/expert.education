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

$string['1'] = 'ru';
$string['description'] = 'Контролирует платный доступ к элементам курса. Студенты не могут посмотреть модуль, пока не оплатят доступ.';
$string['title'] = 'PayAllWays';
$string['pluginname'] = 'PayAllWays';
$string['pluginname_desc'] = 'Плагин PayAllWays позволяет настроить платный доступ к активностям и другим элементам курса';
$string['legalentity'] = 'Юр.лицо';
$string['fop'] = 'ФОП';
$string['billingentity'] = 'Получатель';
$string['billingentity_help'] = 'Выберите схему оплаты: "юр.лицо" устанавливается для плагина глобально, а "ИП" позволяет менеджеру/создателю курса индивидуально указать карту для выплат';
$string['paymentaccount'] = 'Номер карты юр.лица';
$string['paymentaccount_help'] = 'Счет, куда будут поступать деньги';
$string['paymentnote'] = 'Примечание к платежу';
$string['cardnum'] = 'Номер карты';
$string['card_details'] = 'Редактирование деталей карты';
$string['card_section_heading'] = 'Инфо по карте';
$string['getdescription'] = 'Пользователь оплатил доступ';
$string['getdescriptionnot'] = 'Модуль доступен бесплатно';
$string['makepaymentnow'] = 'Оплатить сейчас';
$string['contactmanager'] = 'Ошибка получения данных курса, пожалуйста, свяжитесь с менеджером или создателем курса';
$string['payallways_settings'] = 'Редактировать настройки монетизации';
$string['plugin_oprtating_mode'] = 'Режим работы плагина';
$string['mode_course'] = 'Ограничить доступ ко всему курсу';
$string['mode_activity'] = 'Ограничения по активностям';
$string['section_block_headline'] = 'Премиум-секция';
$string['bankmfo'] = 'МФО банка';
$string['access_cost'] = 'Стоимость доступа';
$string['edrpu'] = 'ЕДРПУ';
$string['entity_name'] = 'Название юр.лица';
$string['passport'] = 'Серия и номер паспорта/номер ID-карты';
$string['fio'] = 'ФИО';
$string['inn'] = 'ИНН';
$string['data_valid'] = 'Все реквизиты мною проверены лично и верны';
$string['rules'] = 'Используя плагин PayAllWays вы соглашаетесь с <a href="https://expert.education/mod/page/view.php?id=113&forceview=1" target="_blank">правилами</a>';

//errors
$string['nopermissionseditcourse'] = 'Извините, у Вас нет прав для редактирования этого курса';
$string['paidinvalidparam'] = 'Параметр is_paid не соответствует типу';
$string['cost_error'] = 'Стоимость курса должна быть числом и не должна быть меньше 0';
$string['empty_field'] = 'Поле не может быть пустым';
$string['account_error'] = 'Некорректно указан номер карты (16 цифр, без пробелов)';
$string['mfo_error'] = 'Некорректно указан номер МФО (6 цифр без пробелов)';
$string['edrpu_error'] = 'ЕДРПУ указан некорректно';
$string['iban_error'] = 'Формат IBAN: 2 буквы + 27 цифр без пробелов';