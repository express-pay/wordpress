# Расширение Wordpress сервиса «Экспресс Платежи»
Расширение CMS Wordpress для интеграции с сервисом «Экспресс Платежи». Расширение позволяет производить прием платежей с помощью ЕРИП, банковских карт и E-POS.

<a href="https://express-pay.by/cms-extensions/wordpress">Инструкция для установки и настройки</a>

<a href="https://www.youtube.com/c/express-pay-by">Наш Youtube канал с опубликованными видео по расширениям</a>

## Описание

Плагин WordPress (файл `expresspay.payment.php`, версия 1.3.0), который добавляет на сайт форму оплаты через сервис «Экспресс Платежи» и позволяет принимать платежи через ЕРИП, банковские карты и E-POS.
Оплата добавляется на страницу через шорткод, а настройки и список выставленных счетов доступны в админ-панели WordPress в меню **Express Payments**.

Плагин хранит настройки платежных методов и счета в отдельных таблицах БД WordPress:
- `{$wpdb->prefix}expresspay_options` — платежные методы и их параметры
- `{$wpdb->prefix}expresspay_invoices` — созданные счета и статусы

Для приема уведомлений используется endpoint WordPress AJAX:
`/wp-admin/admin-ajax.php?action=receive_notification&type_id=<ID_МЕТОДА>`

## Возможности

- Добавление нескольких платежных методов в админке (тип `erip`, `card`, `epos`) и переключение активного метода.
- Вывод формы оплаты на сайте через шорткод (с выбором метода, суммой и назначением платежа).
- Формирование параметров счета и подписи (HMAC-SHA1) для API v1:
  - `web_invoices` (ЕРИП/E-POS)
  - `web_cardinvoices` (банковские карты)
- Тестовый режим `TestMode` с использованием `SandboxUrl` и тестовых параметров из `config/test_settings.json`.
- Проверка подписи входящих уведомлений при `UseSignatureForNotification=1` (поле `Signature`, ключ `SecretWordForNotification`).
- Возможность разрешить изменение данных на стороне оплаты (ФИО/адрес/сумма): `CanChangeName`, `CanChangeAddress`, `CanChangeAmount`.
- Отправка уведомлений плательщику (email/SMS) при включении `SendEmail`/`SendSms`.
- Отображение QR-кода для ЕРИП/E-POS при включении `ShowQrCode` (получение QR через `https://api.express-pay.by/v1/qrcode/getqrcode/`).
- Страница со списком счетов и статусами в админ-панели (**Invoices and payemnts**).

## Требования

- WordPress (минимальная версия не указана в коде плагина).
- PHP (используются стандартные функции PHP и WordPress API).
- Исходящий доступ к API сервиса:
  - `https://api.express-pay.by/v1/` (production)
  - `https://sandbox-api.express-pay.by/v1/` (test)
- Публичная доступность URL для приема уведомлений (если сайт закрыт авторизацией/файрволом — уведомления не дойдут):
  - `/wp-admin/admin-ajax.php?action=receive_notification&type_id=<ID_МЕТОДА>`

## Установка

1. Скопируйте каталог плагина `wordpress-main/` в директорию WordPress:
   - `wp-content/plugins/wordpress-main/`
2. В админ-панели WordPress откройте **Плагины** и активируйте плагин **ExpressPay Payment Module**.
3. После активации плагин создаст таблицы `expresspay_options` и `expresspay_invoices` в базе данных.
4. Убедитесь, что в админ-меню появился раздел **Express Payments**.

Проверка установки:
- В админке отображается меню **Express Payments**.
- Доступны страницы настроек методов оплаты и список счетов.

## Настройка

Настройка выполняется в админ-панели WordPress: **Express Payments → Settings** (добавление/редактирование метода оплаты).

- `Name` - название платежного метода, отображаемое в форме оплаты.
  - пример: `ExpressPay (ERIP)`
- `Type` - тип метода оплаты (`erip`, `epos`, `card`).
  - пример: `erip`
- `Token` - токен доступа к API сервиса.
  - пример: `YOUR_TOKEN`
- `ServiceId` - номер услуги в сервисе.
  - пример: `123`
- `SecretWord` - секретное слово для формирования подписи запросов на создание счетов.
  - пример: `YOUR_SECRET_WORD`
- `SecretWordForNotification` - секретное слово для проверки подписи входящих уведомлений.
  - пример: `YOUR_NOTIFICATION_SECRET`
- `ApiUrl` - базовый URL production API (используется как префикс, далее добавляется endpoint).
  - пример: `https://api.express-pay.by/v1/`
- `SandboxUrl` - базовый URL sandbox API (используется в тестовом режиме).
  - пример: `https://sandbox-api.express-pay.by/v1/`
- `TestMode` - включает использование `SandboxUrl` и тестовых параметров (token/serviceid берутся из `config/test_settings.json` через кнопку/запрос в настройках).
- `UseSignatureForNotification` - включает проверку поля `Signature` в уведомлениях.
- `EripPath` - путь в дереве ЕРИП, который показывается пользователю в инструкции оплаты.
  - пример: `Система "Расчет" (ЕРИП) -> ... -> Ваш сервис`
- `ShowQrCode` - показывает QR-код для оплаты (ЕРИП/E-POS).
- `CanChangeName` - разрешает изменять ФИО при оплате.
- `CanChangeAddress` - разрешает изменять адрес при оплате.
- `CanChangeAmount` - разрешает изменять сумму при оплате.
- `SendEmail` - передавать email для уведомлений плательщика.
- `SendSms` - передавать телефон для SMS-уведомлений (плагин приводит телефон к формату `375XXXXXXXXX`).
- `ServiceProviderCode` - код поставщика услуг для E-POS (используется для формирования кода оплаты).
  - пример: `12345`
- `ServiceEposCode` - код услуги E-POS (используется для формирования кода оплаты).
  - пример: `67890`

URL для уведомлений по выбранному методу формируется плагином так:
`/wp-admin/admin-ajax.php?action=receive_notification&type_id=<ID_МЕТОДА>`

## Использование

1. Создайте страницу (или откройте существующую) и добавьте шорткод формы оплаты, например:
   - `[expresspay_payment amount=25.5 edit_amount=true info="Назначение платежа"]`
2. На странице пользователь выбирает метод оплаты, вводит данные (для `card` блок ФИО скрывается) и подтверждает оплату.
3. Плагин формирует параметры счета и подпись и отправляет пользователя на оплату (ReturnType=`redirect`) в сервис:
   - `.../web_invoices` (ЕРИП/E-POS)
   - `.../web_cardinvoices` (банковские карты)
4. После оплаты сервис возвращает пользователя на ту же страницу (плагин добавляет параметры `type_id` и `result`), а также параметры сервиса:
   - `ExpressPayAccountNumber`, `ExpressPayInvoiceNo`, `Signature`
   Плагин проверяет подпись (метод `response-web-invoice`) и отмечает счет как оплаченный.
5. Дополнительно сервис отправляет серверное уведомление на:
   - `/wp-admin/admin-ajax.php?action=receive_notification&type_id=<ID_МЕТОДА>`
   В POST передаются `Data` (JSON) и `Signature` (если включено `UseSignatureForNotification`).

Проверка оплаты в тестовом режиме:
- Включите `TestMode`, убедитесь что используется `SandboxUrl`, и выполните оплату через sandbox-сценарий сервиса.

Имитация webhook (проверка обработчика уведомлений):
- Отправьте POST-запрос на `/wp-admin/admin-ajax.php?action=receive_notification&type_id=<ID_МЕТОДА>` с параметрами `Data` и `Signature`.
- Подпись формируется как `HMAC-SHA1` (в верхнем регистре) от строки `Data` с ключом `SecretWordForNotification` (метод `notification`).